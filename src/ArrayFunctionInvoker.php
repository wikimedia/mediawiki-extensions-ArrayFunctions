<?php

namespace ArrayFunctions;

use ArrayFunctions\ArrayFunctions\ArrayFunction;
use ArrayFunctions\Exceptions\ImportException;
use ArrayFunctions\Exceptions\IncorrectTypeException;
use ArrayFunctions\Exceptions\TooFewArgumentsException;
use ArrayFunctions\Exceptions\TooManyArgumentsException;
use Parser;
use PPFrame;
use PPNode;
use ReflectionException;
use ReflectionMethod;
use ReflectionNamedType;
use TypeError;

/**
 * Performs pre- and postprocessing on array function arguments and return values.
 */
class ArrayFunctionInvoker {
	/**
	 * @var string Class name of ArrayFunction
	 */
	private string $function;

	public function __construct( string $function ) {
		$this->function = $function;
	}

	/**
	 * @throws ReflectionException
	 */
	public function invoke( Parser $parser, PPFrame $frame, array $arguments ) {
		/** @var ArrayFunction $instance */
		$instance = new $this->function( $parser, $frame );

		try {
			$result = $instance->execute( ...$this->preprocess( $frame, $arguments ) );
		} catch ( TooFewArgumentsException $exception ) {
			// TODO
			return 'too few arguments';
		} catch ( TooManyArgumentsException $exception ) {
			// TODO
			return 'too many arguments';
		} catch ( IncorrectTypeException $exception ) {
			// TODO
			return 'incorrect type';
		}

		return $result;
	}

	/**
	 * @param PPFrame $frame
	 * @param array $arguments
	 * @return array
	 * @throws TooFewArgumentsException|TooManyArgumentsException|ReflectionException|IncorrectTypeException|ImportException
	 */
	public function preprocess( PPFrame $frame, array $arguments ): array {
		$reflectionMethod = new ReflectionMethod( $this->function, 'execute' );

		$numArguments = count( $arguments );
		$result = [];

		foreach ( $reflectionMethod->getParameters() as $parameter ) {
			if ( !$parameter->isOptional() && empty( $arguments ) ) {
				// Required argument without a value
				throw new TooFewArgumentsException( $numArguments, $reflectionMethod->getNumberOfRequiredParameters() );
			} elseif ( $parameter->isOptional() && empty( $arguments ) ) {
				// Optional argument without a value
				$result[] = $parameter->getDefaultValue();
			} elseif ( $parameter->isVariadic() ) {
				// Variadic argument with values
				$values = [];
				while ( !empty( $arguments ) ) {
					$values[] = $this->preprocessArgument( array_shift( $arguments ), $parameter->getType(), $frame );
				}

				$result[] = $values;
			} else {
				// Non-variadic argument with values
				$result[] = $this->preprocessArgument( array_shift( $arguments ), $parameter->getType(), $frame );
			}
		}

		if ( !empty( $arguments ) ) {
			// All arguments must have been consumed at this point
			throw new TooManyArgumentsException( $numArguments, $reflectionMethod->getNumberOfParameters() );
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
	 * @return array|float|int|PPNode|string|null
	 * @throws IncorrectTypeException
	 * @throws ImportException
	 */
	private function preprocessArgument( $argument, ?ReflectionNamedType $type, PPFrame $frame ) {
		// No type given, so return the expanded argument as is
		if ( $type === null ) {
			return Utils::expandNode( $argument, $frame );
		}

		if ( $type->allowsNull() && Utils::expandNode( $argument, $frame, PPFrame::RECOVER_ORIG ) === '' ) {
			// If the parameter is *really* empty (that is, also does not contain wikitext that expands to the empty string), and the type
			// is nullable, then return NULL.
			return null;
		}

		// Trim the nullable question mark from the left when applicable
		$expectedType = ltrim( $type->getName(), '?' );

		if ( $expectedType === PPNode::class ) {
			// Return the unexpanded PPNode for more fine-grained control on the function level
			if ( is_string( $argument ) ) {
				// The first argument is expanded by default and cannot be a PPNode
				// Throw a TypeError here, since this is not the user's fault
				throw new TypeError( 'First argument cannot be a PPNode' );
			}

			return $argument;
		}

		$value = Utils::import( Utils::expandNode( $argument, $frame, PPFrame::RECOVER_ORIG ) );
		$actualType = gettype( $value );

		if ( $actualType !== $expectedType ) {
			throw new IncorrectTypeException( $actualType, $expectedType );
		}

		return $value;
	}
}
