<?php

namespace ArrayFunctions;

use ArrayFunctions\ArrayFunctions\ArrayFunction;
use ArrayFunctions\Exceptions\ArgumentPropagateException;
use ArrayFunctions\Exceptions\MissingRequiredKeywordArgumentException;
use ArrayFunctions\Exceptions\TooFewArgumentsException;
use ArrayFunctions\Exceptions\TooManyArgumentsException;
use ArrayFunctions\Exceptions\TypeMismatchException;
use ArrayFunctions\Exceptions\UnexpectedKeywordArgument;
use MWException;
use Parser;
use PPFrame;
use PPNode;
use ReflectionException;
use ReflectionMethod;
use ReflectionNamedType;

/**
 * Class to preprocess arguments using reflection.
 */
class ArgumentPreprocessor {
	/**
	 * @var ReflectionMethod The method for which to preprocess arguments
	 */
	private ReflectionMethod $method;

	/**
	 * @param mixed $objectOrMethod An object instance or a method name
	 * @param string|null $method The name of the method if $objectOrMethod is an object
	 * @throws ReflectionException
	 */
	public function __construct( $objectOrMethod, ?string $method = null ) {
		$this->method = new ReflectionMethod( $objectOrMethod, $method );
	}

	/**
	 * Preprocesses the given arguments.
	 *
	 * @param array $passedArgs The arguments to preprocess
	 * @param ArrayFunction $instance The array function instance to process the arguments for
	 * @param Parser $parser The current parser
	 * @param PPFrame $frame The current frame
	 *
	 * @return array A tuple of processed positional arguments and keyword arguments
	 *
	 * @throws TypeMismatchException
	 * @throws ReflectionException
	 * @throws TooFewArgumentsException
	 * @throws TooManyArgumentsException
	 * @throws MissingRequiredKeywordArgumentException
	 * @throws UnexpectedKeywordArgument
	 * @throws MWException
	 */
	public function preprocess( array $passedArgs, ArrayFunction $instance, Parser $parser, PPFrame $frame ): array {
		// Split the given arguments into positional and keyword arguments
		[ $passedPositionalArgs, $passedKeywordArgs ] = $this->partitionArgs( $passedArgs, $parser, $frame );

		$positionalArgs = $this->preprocessPositionalArgs( $passedPositionalArgs, $parser, $frame );
		$keywordArgs = $this->preprocessKeywordArgs( $passedKeywordArgs, $instance, $parser, $frame );

		return [ $positionalArgs, $keywordArgs ];
	}

	/**
	 * Preprocess the given positional arguments.
	 *
	 * @param array $passedArgs The positional arguments
	 * @param Parser $parser The current parser
	 * @param PPFrame $frame The current frame
	 *
	 * @return array
	 *
	 * @throws TooFewArgumentsException
	 * @throws TooManyArgumentsException
	 * @throws ReflectionException
	 * @throws TypeMismatchException
	 * @throws ArgumentPropagateException
	 */
	private function preprocessPositionalArgs( array $passedArgs, Parser $parser, PPFrame $frame ): array {
		// Keep track of the number of positional arguments that were passed
		$numArgs = count( $passedArgs );
		$result = [];

		foreach ( $this->method->getParameters() as $i => $arg ) {
			if ( $arg->isVariadic() ) {
				while ( !empty( $passedArgs ) ) {
					$type = $arg->getType();
					$required = $type !== null && !$type->allowsNull();
					$result[] = $this->preprocessArg(
						array_shift( $passedArgs ),
						$type,
						$required,
						$parser,
						$frame,
						$i + 1
					);

					$i++;
				}
			} elseif ( !$arg->isOptional() && empty( $passedArgs ) ) {
				// Required positional argument without a value
				throw new TooFewArgumentsException( $numArgs, $this->method->getNumberOfRequiredParameters() );
			} elseif ( $arg->isOptional() && empty( $passedArgs ) ) {
				// Optional positional argument without a value
				$result[] = $arg->getDefaultValue();
			} else {
				// Non-variadic positional argument with values
				$type = $arg->getType();
				$required = $type !== null && !$type->allowsNull();
				$result[] = $this->preprocessArg(
					array_shift( $passedArgs ),
					$type,
					$required,
					$parser,
					$frame,
					$i + 1
				);
			}
		}

		if ( !empty( $passedArgs ) ) {
			// All positional arguments must have been consumed at this point
			throw new TooManyArgumentsException( $numArgs, $this->method->getNumberOfParameters() );
		}

		return $result;
	}

	/**
	 * Preprocess the given keyword arguments.
	 *
	 * @param array $passedArgs Dictionary of keyword args
	 * @param ArrayFunction $instance The array function instance
	 * @param Parser $parser The current parser
	 * @param PPFrame $frame The current frame
	 * @return array
	 *
	 * @throws ArgumentPropagateException
	 * @throws MissingRequiredKeywordArgumentException
	 * @throws TypeMismatchException
	 * @throws UnexpectedKeywordArgument
	 */
	private function preprocessKeywordArgs(
		array $passedArgs,
		ArrayFunction $instance,
		Parser $parser,
		PPFrame $frame
	): array {
		$result = [];

		// Loop over the keyword arguments in the specification, to make sure that we do not forget a required argument
		foreach ( $instance::getKeywordSpec() as $keyword => $spec ) {
			$required = !array_key_exists( "default", $spec );

			if ( !isset( $passedArgs[$keyword] ) ) {
				if ( $required ) {
					// Missing required keyword argument
					throw new MissingRequiredKeywordArgumentException( $keyword );
				}

				$result[$keyword] = $spec["default"];
			} else {
				$arg = $this->preprocessArg(
					$passedArgs[$keyword],
					$spec["type"] ?? "mixed",
					$required,
					$parser,
					$frame,
					$keyword
				);

				if ( $arg === null ) {
					if ( $required ) {
						// Missing required keyword argument
						throw new MissingRequiredKeywordArgumentException( $keyword );
					}

					$arg = $spec["default"];
				}

				$result[$keyword] = $arg;

				unset( $passedArgs[$keyword] );
			}
		}

		if ( $instance::allowArbitraryKeywordArgs() ) {
			// For the remaining arbitrary keyword arguments, preprocess them as "mixed" and optional
			foreach ( $passedArgs as $keyword => $value ) {
				$result[$keyword] = $this->preprocessArg( $value, "mixed", false, $parser, $frame, $keyword );
			}
		} elseif ( !empty( $passedArgs ) ) {
			// Some keyword arguments have not been consumed, and arbitrary keyword arguments are not allowed
			throw new UnexpectedKeywordArgument( array_key_first( $passedArgs ) );
		}

		return $result;
	}

	/**
	 * Tries to preprocess the given argument into the given type and returns the result, or
	 * throws an IncorrectTypeException if this is not possible.
	 *
	 * @param PPNode|string $argument The argument to preprocess
	 * @param ReflectionNamedType|string|null $type The expected argument type
	 * @param bool $required Whether the argument is required
	 * @param Parser $parser The current parser
	 * @param PPFrame $frame The frame used for argument expansion
	 * @param int|string $name The name or index of the argument
	 * @return array|bool|float|int|PPNode|string|null
	 * @throws TypeMismatchException|ArgumentPropagateException
	 */
	private function preprocessArg( $argument, $type, bool $required, Parser $parser, PPFrame $frame, $name ) {
		if ( $this->compareTypes( $type, PPNode::class ) ) {
			return $argument;
		}

		$expandedArg = trim( $frame->expand( $argument ) );

		if ( $expandedArg === '' ) {
			if ( $required ) {
				throw new TypeMismatchException( "empty", $this->normalizeType( $type ), $argument, $name );
			}

			return null;
		}

		$errorArg = $this->detectErrorArg( $expandedArg, $parser );

		if ( $errorArg ) {
			throw new ArgumentPropagateException( $name, $errorArg['errorId'], $errorArg['errorMessage'] );
		}

		$importedArg = Utils::import( $expandedArg );

		if ( $this->compareTypes( $type, "mixed" ) ) {
			// No coalescing necessary (or possible) for mixed types
			return $importedArg;
		}

		// Try to coalesce the value to the expected type
		return $this->tryCoalesce( $importedArg, $this->normalizeType( $type ), $expandedArg, $name );
	}

	/**
	 * Tries to coalesce the given value into the given type.
	 *
	 * @param array|int|string|bool|float $value The value to coalesce
	 * @param ReflectionNamedType|string|null $wantedType The type to coalesce the value into
	 * @param string $raw The un-imported raw value (used for error reporting)
	 * @param int|string $name The name or index of the argument
	 * @return array|int|string|bool|float
	 * @throws TypeMismatchException If coalescing is not possible
	 */
	private function tryCoalesce( $value, $wantedType, string $raw, $name ) {
		$actualType = gettype( $value );

		if ( $this->compareTypes( $actualType, $wantedType ) ) {
			// The value is already of the correct form
			return $value;
		}

		if ( !$this->compareTypes( $actualType, 'string' ) ) {
			// Refusing to coalesce explicitly typed values
			throw new TypeMismatchException( $actualType, $wantedType, $raw, $name );
		}

		// At this point, $actualType is string and $wantedType is not string
		if ( $this->compareTypes( $wantedType, 'float' ) ) {
			$result = filter_var( $value, FILTER_VALIDATE_FLOAT, FILTER_NULL_ON_FAILURE );

			if ( $result !== null ) {
				return $result;
			}
		} elseif ( $this->compareTypes( $wantedType, 'integer' ) ) {
			$result = filter_var( $value, FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE );

			if ( $result !== null ) {
				return $result;
			}
		} elseif ( $this->compareTypes( $wantedType, 'boolean' ) ) {
			$result = filter_var( $value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE );

			if ( $result !== null ) {
				return $result;
			}
		}

		// Coalescing failed
		throw new TypeMismatchException(
			$this->normalizeType( $actualType ),
			$this->normalizeType( $wantedType ),
			$raw,
			$name
		);
	}

	/**
	 * Compares the given types for equality.
	 *
	 * @param ReflectionNamedType|string|null $left
	 * @param ReflectionNamedType|string|null $right
	 * @return bool
	 */
	private function compareTypes( $left, $right ): bool {
		return $this->normalizeType( $right ) === $this->normalizeType( $left );
	}

	/**
	 * Normalizes the given type so that it can be compared.
	 *
	 * @param ReflectionNamedType|string|null $type
	 * @return string
	 */
	private function normalizeType( $type ): string {
		if ( $type instanceof ReflectionNamedType ) {
			$type = ltrim( $type->getName(), '?' );
		}

		switch ( $type ) {
			case null:
				return 'mixed';
			case 'bool':
				return 'boolean';
			case 'double':
				return 'float';
			case 'int':
				return 'integer';
			default:
				return $type;
		}
	}

	/**
	 * Partitions arguments into positional arguments and keyword arguments. Throws an
	 * exception is a position argument comes before a keyword argument.
	 *
	 * @param array $arguments
	 * @param Parser $parser The current parser
	 * @param PPFrame $frame The frame to use for expansion
	 * @return array Tuple of $positionalArgs and $keywordArgs
	 * @throws MWException
	 */
	private function partitionArgs( array $arguments, Parser $parser, PPFrame $frame ): array {
		$positionalArgs = [];
		$keywordArgs = [];

		foreach ( $arguments as $argument ) {
			// Recover the original source wikitext
			// TODO: See if we can use some MediaWiki function to safely split the arguments for us
			$wikitext = trim( $frame->expand( $argument, PPFrame::RECOVER_ORIG ) );
			$parts = explode( '=', $wikitext, 2 );

			if ( count( $parts ) === 1 || !preg_match( '/^[a-zA-Z\d ]+$/', $parts[0] ) ) {
				// Positional argument
				$positionalArgs[] = $argument;
			} else {
				// Keyword argument
				$keywordArgs[$parts[0]] = $parser->getPreprocessor()->preprocessToObj( $parts[1] );
			}
		}

		return [ $positionalArgs, $keywordArgs ];
	}

	/**
	 * Detect an error (strings of the form <span class="af-error-xxx">...</span>) in the given value.
	 *
	 * @param string $value
	 * @param Parser $parser
	 * @return array{errorMessage: Message, errorId: string}|null
	 */
	private function detectErrorArg( string $value, Parser $parser ): ?array {
		// This regex finds "span" tags with the format <span class="af-error-xxx">...</span>
		$regex = '/<span\s(?:[^\s>]*\s+)*?class="(?:[^"\s>]*\s+)*?af-error-([^\s">]+)(?:\s[^">]*)?"/';

		$matches = [];
		if ( preg_match( $regex, $value, $matches ) && isset( $matches[1] ) ) {
			$errorId = $matches[1];
			$errorMessage = $parser
				->getOutput()
				->getExtensionData(
					ArrayFunctionInvoker::DATA_KEY_PREFIX_ERROR . $errorId
				);

			if ( $errorMessage !== null ) {
				return [ 'errorMessage' => $errorMessage, 'errorId' => $errorId ];
			}
		}

		return null;
	}
}
