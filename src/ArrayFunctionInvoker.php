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
		$parser->addTrackingCategory( "af-tracking-category" );

		$instance = $this->factory->createArrayFunction( $this->function, $parser, $frame );
		$preprocessor = new ArgumentPreprocessor( $instance, "execute" );

		try {
			[ $positionalArgs, $keywordArgs ] = $preprocessor->preprocess( $arguments, $instance, $parser, $frame );
		} catch ( TooFewArgumentsException $exception ) {
			return [ $this->handleError(
				$parser,
				Utils::createMessageArray( 'af-error', [
					$instance::getName(),
					Utils::createMessageArray(
						'af-error-incorrect-argument-count-at-least',
						[ $exception->getExpected(), $exception->getActual() ]
					)
				] )
			) ];
		} catch ( TooManyArgumentsException $exception ) {
			return [ $this->handleError(
				$parser,
				Utils::createMessageArray( 'af-error', [
					$instance::getName(),
					Utils::createMessageArray(
						'af-error-incorrect-argument-count-at-most',
						[ $exception->getExpected(), $exception->getActual() ]
					)
				] )
			) ];
		} catch ( TypeMismatchException $exception ) {
			return [ $this->handleError(
				$parser,
				Utils::createMessageArray( 'af-error', [
					$instance::getName(),
					Utils::createMessageArray(
						'af-error-incorrect-type',
						[
							$exception->getExpected(),
							$exception->getActual(),
							$exception->getName(),
							$exception->getValue()
						]
					)
				] )
			) ];
		} catch ( MissingRequiredKeywordArgumentException $exception ) {
			return [ $this->handleError(
				$parser,
				Utils::createMessageArray( 'af-error', [
					$instance::getName(),
					Utils::createMessageArray(
						'af-error-missing-required-keyword-argument',
						[ $exception->getKeyword() ]
					)
				] )
			) ];
		} catch ( UnexpectedKeywordArgument $exception ) {
			return [ $this->handleError(
				$parser,
				Utils::createMessageArray( 'af-error', [
					$instance::getName(),
					Utils::createMessageArray(
						'af-error-unexpected-keyword-argument',
						[ $exception->getKeyword() ]
					)
				] )
			) ];
		} catch ( ArgumentPropagateException $exception ) {
			// We parse the message, because otherwise we would pass the entire message specifier to each nested
			// error with a complexity of O(n^2) (since each nested error would parse everything below it). By
			// parsing it here, we only convert the message specifier once for each message, which is O(n).
			$message = $this->convertMessageArrayToMessage( $parser, $exception->getMessageArray() )->parse();

			return [ $this->handleError(
				$parser,
				Utils::createMessageArray( 'af-nested-error', [
					$message,
					$exception->getArgName(),
					$instance::getName()
				] )
			) ];
		}

		try {
			$instance->setKeywordArgs( $keywordArgs );

			$result = $instance->execute( ...$positionalArgs );
			$result[0] = Utils::export( $result[0] );
		} catch ( RuntimeException $exception ) {
			return [ $this->handleError(
				$parser,
				Utils::createMessageArray( 'af-error', [ $instance::getName(), $exception->getMessageArray() ] )
			) ];
		}

		return $result;
	}

	/**
	 * @param Parser $parser
	 * @param array $messageArray
	 * @return string
	 */
	private function handleError( Parser $parser, array $messageArray ): string {
		$parser->addTrackingCategory( 'af-error-category' );

		if ( $this->enableErrorTracking ) {
			$uuid = Utils::newRandomID( 18, '' );
			$errorId = self::DATA_KEY_PREFIX_ERROR . $uuid;

			$parserOutput = $parser->getOutput();
			$parserOutput->setExtensionData( $errorId, $messageArray );

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

		return $this->createErrorString( $parser, $uuid, $messageArray );
	}

	/**
	 * Create an error string.
	 *
	 * @param Parser $parser
	 * @param string|null $uuid
	 * @param array $messageArray
	 * @return string
	 */
	private function createErrorString( Parser $parser, ?string $uuid, array $messageArray ): string {
		$message = $this->convertMessageArrayToMessage( $parser, $messageArray )->parse();

		if ( $uuid ) {
			return '<span class="error af-error-' . $uuid . '">' . $message . '</span>';
		} else {
			return '<span class="error">' . $message . '</span>';
		}
	}

	/**
	 * Converts an array of the form `{messageKey: string, messageArgs: array}` to a
	 * Message object.
	 *
	 * @param Parser $parser
	 * @param array $messageArray
	 * @return Message
	 */
	private function convertMessageArrayToMessage( Parser $parser, array $messageArray ): Message {
		$messageKey = $messageArray['messageKey'];
		$messageArgs = $messageArray['messageArgs'];

		return Utils::msg(
			$parser,
			$messageKey,
			array_map(
				function ( $messageArg ) use ( $parser ) {
					if ( is_array( $messageArg ) ) {
						return $this->convertMessageArrayToMessage( $parser, $messageArg );
					}

					return $messageArg;
				},
				$messageArgs
			)
		);
	}
}
