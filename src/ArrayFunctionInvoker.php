<?php

namespace ArrayFunctions;

use ArrayFunctions\ArrayFunctions\ArrayFunction;
use ArrayFunctions\Exceptions\MissingRequiredKeywordArgumentException;
use ArrayFunctions\Exceptions\RuntimeException;
use ArrayFunctions\Exceptions\TooFewArgumentsException;
use ArrayFunctions\Exceptions\TooManyArgumentsException;
use ArrayFunctions\Exceptions\TypeMismatchException;
use ArrayFunctions\Exceptions\UnexpectedKeywordArgument;
use Message;
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
	 * @var ArrayFunctionFactory The ArrayFunctionFactory singleton
	 */
	private ArrayFunctionFactory $factory;

	/**
	 * @param class-string<ArrayFunction> $function The class string of an ArrayFunction class
	 * @param ArrayFunctionFactory $factory The ArrayFunctionFactory singleton
	 */
	public function __construct( string $function, ArrayFunctionFactory $factory ) {
		$this->function = $function;
		$this->factory = $factory;
	}

	/**
	 * Invoke the function.
	 *
	 * @param Parser $parser The current parser
	 * @param PPFrame $frame The current frame
	 * @param array $arguments The arguments passed to the function
	 * @return array
	 *
	 * @throws ReflectionException|MWException
	 */
	public function invoke( Parser $parser, PPFrame $frame, array $arguments ): array {
		$instance = $this->factory->createArrayFunction( $this->function, $parser, $frame );
		$preprocessor = new ArgumentPreprocessor( $instance, "execute" );

		try {
			[ $positionalArgs, $keywordArgs ] = $preprocessor->preprocess( $arguments, $instance, $frame, $parser );
		} catch ( TooFewArgumentsException $exception ) {
			return [ $this->handleError(
				$parser,
				$instance::getName(),
				wfMessage(
					"af-error-incorrect-argument-count-at-least",
					$exception->getExpected(),
					$exception->getActual()
				)
			) ];
		} catch ( TooManyArgumentsException $exception ) {
			return [ $this->handleError(
				$parser,
				$instance::getName(),
				wfMessage(
					"af-error-incorrect-argument-count-at-most",
					$exception->getExpected(),
					$exception->getActual()
				)
			) ];
		} catch ( TypeMismatchException $exception ) {
			return [ $this->handleError(
				$parser,
				$instance::getName(),
				wfMessage(
					"af-error-incorrect-type",
					$exception->getExpected(),
					$exception->getActual(),
					$exception->getName(),
					$exception->getValue()
				)
			) ];
		} catch ( MissingRequiredKeywordArgumentException $exception ) {
			return [ $this->handleError(
				$parser,
				$instance::getName(),
				wfMessage(
					"af-error-missing-required-keyword-argument",
					$exception->getKeyword()
				)
			) ];
		} catch ( UnexpectedKeywordArgument $exception ) {
			return [ $this->handleError(
				$parser,
				$instance::getName(),
				wfMessage(
					"af-error-unexpected-keyword-argument",
					$exception->getKeyword()
				)
			) ];
		}

		try {
			$instance->setKeywordArgs( $keywordArgs );

			$result = $instance->execute( ...$positionalArgs );
			$result[0] = Utils::export( $result[0] );
		} catch ( RuntimeException $exception ) {
			return [ $this->handleError( $parser, $instance::getName(), $exception->getRuntimeMessage() ) ];
		}

		return $result;
	}

	/**
	 * @param Parser $parser
	 * @param string $function
	 * @param string|Message $message A message key
	 * @return string
	 */
	private function handleError( Parser $parser, string $function, $message ): string {
		$parser->addTrackingCategory( 'af-error-category' );

		return Utils::error( $function, $message );
	}
}
