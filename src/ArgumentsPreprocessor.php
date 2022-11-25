<?php

namespace ArrayFunctions;

use ArrayFunctions\ArrayFunctions\ArrayFunction;
use ArrayFunctions\Exceptions\TooFewArgumentsException;
use ArrayFunctions\Exceptions\TooManyArgumentsException;
use Parser;
use PPFrame;
use ReflectionException;
use ReflectionMethod;

/**
 * Performs simple preprocessing on the given parser function arguments. The preprocessing is based on type-hints given in the signature of
 * the "execute" function of the given ArrayFunction class.
 */
class ArgumentsPreprocessor {
	/**
	 * @var ReflectionMethod
	 */
	private ReflectionMethod $method;

	/**
	 * @throws ReflectionException
	 */
	public function __construct( ArrayFunction $function ) {
		$this->method = new ReflectionMethod( $function, 'execute' );
	}

	/**
	 * @param Parser $parser
	 * @param PPFrame $frame
	 * @param array $args
	 * @return array
	 * @throws TooFewArgumentsException|TooManyArgumentsException
	 */
	public function preprocess( Parser $parser, PPFrame $frame, array $args ): array {
		$result = [];

		$parameters = $this->method->getParameters();
		$numArgs = count( $args );

		foreach ( $parameters as $parameter ) {
			if ( !$parameter->isOptional() && empty( $args ) ) {
				throw new TooFewArgumentsException( $numArgs, $this->method->getNumberOfRequiredParameters() );
			}

			if ( $parameter->isOptional() && empty( $args ) ) {
				// TODO
				break;
			}

			if ( $parameter->isVariadic() ) {
				// Consume the remaining arguments, and break

			}
		}

		if ( !empty( $args ) ) {
			throw new TooManyArgumentsException( $numArgs, $this->method->getNumberOfParameters() );
		}

		return $result;
	}
}
