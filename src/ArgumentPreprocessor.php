<?php

namespace ArrayFunctions;

use ArrayFunctions\Exceptions\TypeMismatchException;
use ArrayFunctions\Exceptions\TooFewArgumentsException;
use ArrayFunctions\Exceptions\TooManyArgumentsException;
use FormatJson;
use PPFrame;
use PPNode;
use ReflectionException;
use ReflectionMethod;
use ReflectionNamedType;
use TypeError;

/**
 * Class to preprocess arguments using reflection.
 */
class ArgumentPreprocessor {
	/**
	 * @var ReflectionMethod The method for which to preprocess arguments
	 */
	private ReflectionMethod $method;

	/**
	 * @param string|object $objectOrMethod Classname, object (instance of the class) that contains the method or class name and method name
	 *                                      delimited by ::.
	 * @param string|null $method Name of the method if the first argument is a classname or an object.
	 * @throws ReflectionException
	 */
	public function __construct( $objectOrMethod, string $method = null ) {
		$this->method = new ReflectionMethod( $objectOrMethod, $method );
	}

	/**
	 * Preprocesses the given arguments.
	 *
	 * @param array $arguments The arguments to preprocess
	 * @param PPFrame $frame The current frame
	 *
	 * @return array The preprocessed arguments
	 *
	 * @throws TypeMismatchException
	 * @throws ReflectionException
	 * @throws TooFewArgumentsException
	 * @throws TooManyArgumentsException
	 */
	public function preprocess( array $arguments, PPFrame $frame ): array {
		$numArguments = count( $arguments );
		$result = [];

		foreach ( $this->method->getParameters() as $i => $parameter ) {
			if ( $parameter->isVariadic() ) {
				while ( !empty( $arguments ) ) {
					$result[] = $this->preprocessArgument( array_shift( $arguments ), $parameter->getType(), $frame, $i + 1 );
				}
			} elseif ( !$parameter->isOptional() && empty( $arguments ) ) {
				// Required argument without a value
				throw new TooFewArgumentsException( $numArguments, $this->method->getNumberOfRequiredParameters() );
			} elseif ( $parameter->isOptional() && empty( $arguments ) ) {
				// Optional argument without a value
				$result[] = $parameter->getDefaultValue();
			} else {
				// Non-variadic argument with values
				$result[] = $this->preprocessArgument( array_shift( $arguments ), $parameter->getType(), $frame, $i + 1 );
			}
		}

		if ( !empty( $arguments ) ) {
			// All arguments must have been consumed at this point
			throw new TooManyArgumentsException( $numArguments, $this->method->getNumberOfParameters() );
		}

		return $result;
	}

	/**
	 * Tries to preprocess the given argument into the given type and returns the result, or throws an IncorrectTypeException if this is not
	 * possible.
	 *
	 * @param PPNode|string $argument The argument to preprocess
	 * @param ReflectionNamedType|null $type The type of the argument, or NULL for "mixed"
	 * @param PPFrame $frame The frame used for argument expansion
	 * @param int $index The parameter index
	 * @return array|float|int|PPNode|string|null
	 * @throws TypeMismatchException
	 */
	private function preprocessArgument( $argument, ?ReflectionNamedType $type, PPFrame $frame, int $index ) {
		// Trim the nullable question mark from the left when applicable
		$expectedType = $type !== null ? $this->normalizeType( $type->getName() ) : null;

		if ( $expectedType === PPNode::class ) {
			// Return the unexpanded PPNode for more fine-grained control on the function level
			if ( is_string( $argument ) ) {
				// The first argument is expanded by default and cannot be a PPNode
				// Throw a TypeError here, since this is not the user's fault, but rather a fault in the implementation
				throw new TypeError( 'First argument cannot be a PPNode' );
			}

			return $argument;
		}

		$argument = trim( $frame->expand( $argument ) );

		if ( $argument === '' ) {
			// Although the parameter was *explicitly* given, it parsed to the empty string
			if ( !isset( $expectedType ) || $type->allowsNull() ) {
				// The type is either "mixed" or nullable, so just pass null
				return null;
			}

			// The type is not nullable
			throw new TypeMismatchException( "empty", $expectedType, $argument, $index );
		}

		$value = $this->import( $argument );

		if ( !isset( $expectedType ) ) {
			return $value;
		}

		return $this->tryCoalesce( $value, $expectedType, $argument, $index );
	}

	/**
	 * Tries to coalesce the given value into the given type.
	 *
	 * @param array|int|string|bool|float $value The value to coalesce
	 * @param string $wantedType The type to coalesce the value into
	 * @param string $raw The un-imported raw value (used for error reporting)
	 * @param int $index The number of the parameter (used for error reporting)
	 * @return array|int|string|bool|float
	 * @throws TypeMismatchException If coalescing is not possible
	 */
	private function tryCoalesce( $value, string $wantedType, string $raw, int $index ) {
		$actualType = gettype( $value );

		if ( $actualType === $wantedType ) {
			// The value is already of the correct form
			return $value;
		}

		if ( $actualType !== 'string' ) {
			// Refusing to coalesce explicitly typed values
			throw new TypeMismatchException( $actualType, $wantedType, $raw, $index );
		}

		// At this point, $actualType is a string and $wantedType is not a string
		switch( $wantedType ) {
			case 'float':
			case 'double':
				if ( preg_match( '/^-?\d*\.\d+$/', $value ) || preg_match( '/^-?\d+$/', $value ) ) {
					return floatval( $value );
				}

				break;
			case 'int':
				if ( preg_match( '/^-?\d+$/', $value ) ) {
					return intval( $value );
				}

				break;
			case 'boolean':
				$filtered = filter_var( $value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE );
				if ( $filtered !== null ) {
					return $filtered;
				}

				break;
		}

		// Coalescing failed
		throw new TypeMismatchException( $actualType, $wantedType, $raw, $index );
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
				$filtered = filter_var( $value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE );

				if ( $filtered !== null ) {
					return $filtered;
				}

				break;
			case "float":
				if ( preg_match( '/^-?\d*\.\d+$/', $value ) || preg_match( '/^-?\d+$/', $value ) ) {
					return floatval( $value );
				}

				break;
			case "integer":
				if ( preg_match( '/^-?\d+$/', $value ) ) {
					return intval( $value );
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
		}

		// Default to interpreting the entire input as a string
		return $input;
	}

	/**
	 * Normalizes the given type name (from ReflectionNamedType) so that it can safely be compared with values returned from "gettype".
	 *
	 * @param string $type
	 * @return string
	 */
	private function normalizeType( string $type ): string {
		// Remove nullable type hint
		$type = ltrim( $type, '?' );

		switch ( $type ) {
			case 'bool':
				return 'boolean';
			case 'float':
				return 'double';
			case 'int':
				return 'integer';
		}

		return $type;
	}
}
