<?php

namespace ArrayFunctions;

use ArrayFunctions\ArrayFunctions\ArrayFunction;
use ArrayFunctions\Exceptions\TypeMismatchException;
use ArrayFunctions\Exceptions\RuntimeException;
use ArrayFunctions\Exceptions\TooFewArgumentsException;
use ArrayFunctions\Exceptions\TooManyArgumentsException;
use MWException;
use Parser;
use PPFrame;
use ReflectionException;

/**
 * Performs pre- and postprocessing on array function arguments and return values.
 */
class ArrayFunctionInvoker {
	/**
	 * @var string The class string of an ArrayFunction class
	 */
	private string $function;

	/**
	 * @param class-string<ArrayFunction> $function The class string of an ArrayFunction class
	 */
	public function __construct( string $function ) {
		$this->function = $function;
	}

	/**
	 * Invoke the function.
	 *
	 * @throws ReflectionException
	 */
	public function invoke( Parser $parser, PPFrame $frame, array $arguments ): array {
		/** @var ArrayFunction $instance */
		$instance = new $this->function( $parser, $frame );
		$preprocessor = new ArgumentPreprocessor( $instance, "execute" );

		try {
			$arguments = $preprocessor->preprocess( $arguments, $frame );
		} catch ( TooFewArgumentsException $exception ) {
			return [Utils::error( $instance::getName(), wfMessage( "af-error-incorrect-argument-count-at-least", $exception->getExpected(), $exception->getActual() ) )];
		} catch ( TooManyArgumentsException $exception ) {
			return [Utils::error( $instance::getName(), wfMessage( "af-error-incorrect-argument-count-at-most", $exception->getExpected(), $exception->getActual() ) )];
		} catch ( TypeMismatchException $exception ) {
			return [Utils::error( $instance::getName(), wfMessage( "af-error-incorrect-type", $exception->getExpected(), $exception->getActual(), $exception->getIndex(), $exception->getValue() ) )];
		}

		try {
			$result = $instance->execute( ...$arguments );
			$result[0] = Utils::export( $result[0] );
		} catch ( RuntimeException $exception ) {
			return [Utils::error( $instance::getName(), $exception->getRuntimeMessage() ) ];
		}

		return $result;
	}
}
