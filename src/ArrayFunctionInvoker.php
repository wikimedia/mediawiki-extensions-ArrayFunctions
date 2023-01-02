<?php

namespace ArrayFunctions;

use ArrayFunctions\ArrayFunctions\ArrayFunction;
use ArrayFunctions\Exceptions\MissingRequiredKeywordArgumentException;
use ArrayFunctions\Exceptions\TypeMismatchException;
use ArrayFunctions\Exceptions\RuntimeException;
use ArrayFunctions\Exceptions\TooFewArgumentsException;
use ArrayFunctions\Exceptions\TooManyArgumentsException;
use ArrayFunctions\Exceptions\UnexpectedKeywordArgument;
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
	 * @throws ReflectionException|MWException
	 */
	public function invoke( Parser $parser, PPFrame $frame, array $arguments ): array {
		/** @var ArrayFunction $instance */
		$instance = new $this->function( $parser, $frame );
		$preprocessor = new ArgumentPreprocessor( $instance, "execute" );

		try {
			list( $positionalArgs, $keywordArgs ) = $preprocessor->preprocess( $arguments, $instance, $frame, $parser );
		} catch ( TooFewArgumentsException $exception ) {
			return [Utils::error( $instance::getName(), wfMessage( "af-error-incorrect-argument-count-at-least", $exception->getExpected(), $exception->getActual() ) )];
		} catch ( TooManyArgumentsException $exception ) {
			return [Utils::error( $instance::getName(), wfMessage( "af-error-incorrect-argument-count-at-most", $exception->getExpected(), $exception->getActual() ) )];
		} catch ( TypeMismatchException $exception ) {
			return [Utils::error( $instance::getName(), wfMessage( "af-error-incorrect-type", $exception->getExpected(), $exception->getActual(), $exception->getName(), $exception->getValue() ) )];
		} catch ( MissingRequiredKeywordArgumentException $exception ) {
			return [Utils::error( $instance::getName(), wfMessage( "af-error-missing-required-keyword-argument", $exception->getKeyword() ) )];
		} catch ( UnexpectedKeywordArgument $exception ) {
			return [Utils::error( $instance::getName(), wfMessage( "af-error-unexpected-keyword-argument", $exception->getKeyword() ) )];
		}

		try {
			$instance->setKeywordArgs( $keywordArgs );

			$result = $instance->execute( ...$positionalArgs );
			$result[0] = Utils::export( $result[0] );
		} catch ( RuntimeException $exception ) {
			return [ Utils::error( $instance::getName(), $exception->getRuntimeMessage() ) ];
		}

		return $result;
	}
}
