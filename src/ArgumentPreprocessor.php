<?php

namespace ArrayFunctions;

use ArrayFunctions\ArrayFunctions\ArrayFunction;
use ArrayFunctions\Exceptions\MissingRequiredKeywordArgumentException;
use ArrayFunctions\Exceptions\PositionalAfterKeywordException;
use ArrayFunctions\Exceptions\TypeMismatchException;
use ArrayFunctions\Exceptions\TooFewArgumentsException;
use ArrayFunctions\Exceptions\TooManyArgumentsException;
use ArrayFunctions\Exceptions\UnexpectedKeywordArgument;
use FormatJson;
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
	 * @param PPFrame $frame The current frame
	 * @param Parser $parser The current parser
	 *
	 * @return array A tuple of processed positional arguments and keyword arguments
	 *
	 * @throws TypeMismatchException
	 * @throws ReflectionException
	 * @throws TooFewArgumentsException
	 * @throws TooManyArgumentsException
	 * @throws PositionalAfterKeywordException
	 * @throws MissingRequiredKeywordArgumentException
	 * @throws UnexpectedKeywordArgument
	 * @throws MWException
	 */
	public function preprocess( array $passedArgs, ArrayFunction $instance, PPFrame $frame, Parser $parser ): array {
		// Split the given arguments into positional and keyword arguments
		list( $passedPositionalArgs, $passedKeywordArgs ) = $this->partitionArgs( $passedArgs, $frame, $parser );

		$positionalArgs = $this->preprocessPositionalArgs( $passedPositionalArgs, $frame );
		$keywordArgs = $this->preprocessKeywordArgs( $passedKeywordArgs, $instance, $frame );

		return [$positionalArgs, $keywordArgs];
	}

	/**
	 * Preprocess the given positional arguments.
	 *
	 * @param array $passedArgs The positional arguments
	 * @param PPFrame $frame The current frame
	 *
	 * @throws TooFewArgumentsException
	 * @throws TooManyArgumentsException
	 * @throws ReflectionException
	 * @throws TypeMismatchException
	 */
	private function preprocessPositionalArgs( array $passedArgs, PPFrame $frame ): array {
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
						$frame,
						$i + 1
					);
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
	 * @param PPFrame $frame The current frame
	 *
	 * @throws TypeMismatchException
	 * @throws MissingRequiredKeywordArgumentException
	 * @throws UnexpectedKeywordArgument
	 */
	private function preprocessKeywordArgs( array $passedArgs, ArrayFunction $instance, PPFrame $frame ): array {
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
				$result[$keyword] = $this->preprocessArg( $passedArgs[$keyword], $spec["type"] ?? "mixed", $required, $frame, $keyword );
				unset( $passedArgs[$keyword] );
			}
		}

		if ( $instance::allowArbitraryKeywordArgs() ) {
			// For the remaining arbitrary keyword arguments, preprocess them as "mixed" and optional
			foreach ( $passedArgs as $keyword => $value ) {
				$result[$keyword] = $this->preprocessArg( $value, "mixed", false, $frame, $value );
			}
		} elseif ( !empty( $passedArgs ) ) {
			// Some keyword arguments have not been consumed, and arbitrary keyword arguments are not allowed
			throw new UnexpectedKeywordArgument( array_key_first( $passedArgs ) );
		}

		return $result;
	}

	/**
	 * Tries to preprocess the given argument into the given type and returns the result, or throws an IncorrectTypeException if this is not
	 * possible.
	 *
	 * @param PPNode|string $argument The argument to preprocess
	 * @param ReflectionNamedType|string|null $type The expected argument type
	 * @param bool $required Whether the argument is required
	 * @param PPFrame $frame The frame used for argument expansion
	 * @param int|string $name The name or index of the argument
	 * @return array|bool|float|int|PPNode|string|null
	 * @throws TypeMismatchException
	 */
	private function preprocessArg( $argument, $type, bool $required, PPFrame $frame, $name ) {
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

		$importedArg = $this->import( $expandedArg );

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
		throw new TypeMismatchException( $this->normalizeType( $actualType ), $this->normalizeType( $wantedType ), $raw, $name );
	}

	/**
	 * Compares the given types for equality.
	 *
	 * TODO: Make this function work for PHP 8 union types.
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
			$type = ltrim($type->getName(), '?');
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
	 * Partitions arguments into positional arguments and keyword arguments. Throws an exception is a position argument comes before a
	 * keyword argument.
	 *
	 * @param array $arguments
	 * @param PPFrame $frame The frame to use for expansion
	 * @param Parser $parser
	 * @return array Tuple of $positionalArgs and $keywordArgs
	 * @throws PositionalAfterKeywordException|MWException
	 */
	private function partitionArgs( array $arguments, PPFrame $frame, Parser $parser ): array {
		$positionalArgs = [];
		$keywordArgs = [];

		foreach ( $arguments as $argument ) {
			// Recover the original source wikitext
			$wikitext = trim( $frame->expand( $argument, PPFrame::RECOVER_ORIG ) );
			$parts = explode( '=', $wikitext, 2 );

			if ( count( $parts ) === 1 || !preg_match( '/^[a-zA-Z ]+$/', $parts[0] ) ) {
				// Positional argument
				if ( $keywordArgs !== [] ) {
					// Positional argument passed after keyword argument
					throw new PositionalAfterKeywordException();
				}

				$positionalArgs[] = $argument;
			} else {
				// Keyword argument
				// TODO: Research and test use of "preprocessToObj" here
				$keywordArgs[$parts[0]] = $parser->getPreprocessor()->preprocessToObj( $parts[1] );
			}
		}

		return [$positionalArgs, $keywordArgs];
	}

	/**
	 * Imports the given string and converts it to the correct type when appropriate.
	 *
	 * @param string $input The value to import
	 * @return array|int|string|bool|float The parsed value
	 */
	private function import( string $input ) {
		if ( strpos( $input, '__^__' ) === false ) {
			$type = "string";
			$value = $input;
		} else {
			list( $type, $value ) = explode( '__^__', $input, 2 );
		}

		// Handle any non-string type
		switch ( $type ) {
			case "boolean":
				$result = filter_var( $value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE );

				if ( $result !== null ) {
					return $result;
				}

				break;
			case "float":
				$result = filter_var( $value, FILTER_VALIDATE_FLOAT, FILTER_NULL_ON_FAILURE );

				if ( $result !== null ) {
					return $result;
				}

				break;
			case "integer":
				$result = filter_var( $value, FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE );

				if ( $result !== null ) {
					return $result;
				}

				break;
			case "array":
				// Try decoding and parsing to see if it was a base64 encoded JSON string
				$maybeJson = base64_decode( $value );

				if ( $maybeJson !== false ) {
					if ( $maybeJson === '{}' ) {
						// Short-circuit for empty objects, since FormatJson does not handle them correctly
						return [];
					}

					$status = FormatJson::parse( $maybeJson, FormatJson::FORCE_ASSOC | FormatJson::TRY_FIXING | FormatJson::STRIP_COMMENTS );

					if ( $status->isGood() ) {
						return $status->getValue();
					}
				}

				break;
			case "string":
				return $value;
		}

		// Default to interpreting the entire input as a string
		return $input;
	}
}
