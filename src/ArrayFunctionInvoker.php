<?php

namespace ArrayFunctions;

use ArrayFunctions\ArrayFunctions\ArrayFunction;
use ArrayFunctions\Exceptions\ArgumentPropagateException;
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
	public const DATA_KEY_ERRORS = 'ArrayFunctions__errors';
	public const DATA_KEY_PREFIX_ERROR = 'ArrayFunctions__error_';

	/**
	 * @var string The class string of an ArrayFunction class
	 */
	private string $function;

	/**
	 * @var bool Whether to track and propagate errors to parent function invocations
	 */
	private bool $enableErrorTracking;

	/**
	 * @var ArrayFunctionFactory The ArrayFunctionFactory singleton
	 */
	private ArrayFunctionFactory $factory;

	/**
	 * @param class-string<ArrayFunction> $function The class string of an ArrayFunction class
	 * @param bool $enableErrorTracking Whether to enable error tracking
	 * @param ArrayFunctionFactory $factory The ArrayFunctionFactory singleton
	 */
	public function __construct( string $function, bool $enableErrorTracking, ArrayFunctionFactory $factory ) {
		$this->function = $function;
		$this->enableErrorTracking = $enableErrorTracking;
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
			[ $positionalArgs, $keywordArgs ] = $preprocessor->preprocess( $arguments, $instance, $parser, $frame );
		} catch ( TooFewArgumentsException $exception ) {
			return [ $this->handleError(
				$parser,
				Utils::error( $instance::getName(), wfMessage(
					"af-error-incorrect-argument-count-at-least",
					$exception->getExpected(),
					$exception->getActual()
				) )
			) ];
		} catch ( TooManyArgumentsException $exception ) {
			return [ $this->handleError(
				$parser,
				Utils::error( $instance::getName(), wfMessage(
					"af-error-incorrect-argument-count-at-most",
					$exception->getExpected(),
					$exception->getActual()
				) )
			) ];
		} catch ( TypeMismatchException $exception ) {
			return [ $this->handleError(
				$parser,
				Utils::error( $instance::getName(), wfMessage(
					"af-error-incorrect-type",
					$exception->getExpected(),
					$exception->getActual(),
					$exception->getName(),
					$exception->getValue()
				) )
			) ];
		} catch ( MissingRequiredKeywordArgumentException $exception ) {
			return [ $this->handleError(
				$parser,
				Utils::error( $instance::getName(), wfMessage(
					"af-error-missing-required-keyword-argument",
					$exception->getKeyword()
				) )
			) ];
		} catch ( UnexpectedKeywordArgument $exception ) {
			return [ $this->handleError(
				$parser,
				Utils::error( $instance::getName(), wfMessage(
					"af-error-unexpected-keyword-argument",
					$exception->getKeyword()
				) )
			) ];
		} catch ( ArgumentPropagateException $exception ) {
			// We parse the exception first, instead of passing the Message object
			// to wfMessage(), because it is significantly (orders of magnitude)
			// faster for some reason.
			$parsedException = $exception->getErrorMessage()->parse();

			return [ $this->handleError(
				$parser,
				wfMessage( 'af-nested-error', $parsedException, $exception->getArgName(), $instance::getName() )
			) ];
		}

		try {
			$instance->setKeywordArgs( $keywordArgs );

			$result = $instance->execute( ...$positionalArgs );
			$result[0] = Utils::export( $result[0] );
		} catch ( RuntimeException $exception ) {
			return [ $this->handleError(
				$parser,
				Utils::error( $instance::getName(), $exception->getRuntimeMessage() )
			) ];
		}

		return $result;
	}

	/**
	 * @param Parser $parser
	 * @param string|Message $message A message key
	 * @return string
	 */
	private function handleError( Parser $parser, $message ): string {
		$parser->addTrackingCategory( 'af-error-category' );

		if ( $this->enableErrorTracking ) {
			$uuid = Utils::newRandomID( 18, '' );
			$errorId = self::DATA_KEY_PREFIX_ERROR . $uuid;

			$parserOutput = $parser->getOutput();
			$parserOutput->setExtensionData( $errorId, $message );

			if ( method_exists( $parserOutput, 'appendExtensionData' ) ) {
				// MediaWiki >= 1.38
				$parserOutput->appendExtensionData( self::DATA_KEY_ERRORS, $errorId );
			} else {
				$errors = $parserOutput->getExtensionData( self::DATA_KEY_ERRORS ) ?? [];
				$errors[$errorId] = true;
				$parserOutput->setExtensionData( self::DATA_KEY_ERRORS, $errors );
			}
		} else {
			$uuid = null;
		}

		return $this->createErrorString( $message, $uuid );
	}

	/**
	 * Create an error string.
	 *
	 * @param Message $message
	 * @param string|null $uuid
	 * @return string
	 */
	private function createErrorString( $message, ?string $uuid ): string {
		if ( $message instanceof Message ) {
			$message = $message->parse();
		}

		if ( $uuid ) {
			return '<span class="error af-error-' . $uuid . '">' . $message . '</span>';
		} else {
			return '<span class="error">' . $message . '</span>';
		}
	}
}
