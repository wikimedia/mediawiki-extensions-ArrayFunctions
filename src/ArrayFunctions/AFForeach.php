<?php

namespace ArrayFunctions\ArrayFunctions;

use ArrayFunctions\Exceptions\RuntimeException;
use ArrayFunctions\Utils;
use PPNode;

/**
 * Implements the #af_foreach parser function.
 */
class AFForeach extends ArrayFunction {
	public const DATA_KEY_ITERATIONS = 'ArrayFunctions__af_foreach_iterations';
	public const CONFIG_FOREACH_ITERATION_LIMIT = 'ArrayFunctionsForeachIterationLimit';

	private const KWARG_DELIMITER = 'delimiter';

	/**
	 * @inheritDoc
	 */
	public static function getName(): string {
		return 'af_foreach';
	}

	public static function getRequiredConfigVariables(): array {
		return [ self::CONFIG_FOREACH_ITERATION_LIMIT ];
	}

	/**
	 * @inheritDoc
	 */
	public static function getKeywordSpec(): array {
		return [
			self::KWARG_DELIMITER => [
				'default' => '',
				'type' => 'string',
				'description' => 'The delimiter to put between each result.'
			]
		];
	}

	/**
	 * @inheritDoc
	 */
	public function execute(
		array $array,
		?string $keyName = null,
		?string $valueName = null,
		?PPNode $body = null
	): array {
		$result = [];

		$maxIterationCount = $this->getConfigValue( self::CONFIG_FOREACH_ITERATION_LIMIT );

		foreach ( $array as $key => $value ) {
			$iterations = $this->getParser()->getOutput()->getExtensionData( self::DATA_KEY_ITERATIONS ) ?? [];
			if ( $maxIterationCount >= 0 && count( $iterations ) >= $maxIterationCount ) {
				throw new RuntimeException( Utils::createMessageArray( 'af-error-foreach-iteration-limit-reached' ) );
			}

			$parserOutput = $this->getParser()->getOutput();
			$randomID = Utils::newRandomID();

			if ( method_exists( $parserOutput, 'appendExtensionData' ) ) {
				// MediaWiki >= 1.38
				$parserOutput->appendExtensionData( self::DATA_KEY_ITERATIONS, $randomID );
			} else {
				$iterations = $parserOutput->getExtensionData( self::DATA_KEY_ITERATIONS ) ?? [];
				$iterations[$randomID] = true;
				$parserOutput->setExtensionData( self::DATA_KEY_ITERATIONS, $iterations );
			}

			$args = $this->getFrame()->getArguments();

			if ( $keyName !== null ) {
				$args[$keyName] = $key;
			}

			if ( $valueName !== null ) {
				$args[$valueName] = Utils::export( $value );
			}

			$nodeArray = $this->getParser()->getPreprocessor()->newPartNodeArray( $args );
			$childFrame = $this->getFrame()->newChild( $nodeArray, $this->getFrame()->getTitle() );

			$result[] = $body !== null ?
				trim( $childFrame->expand( $body ) ) :
				'';
		}

		$delimiter = $this->getKeywordArg( self::KWARG_DELIMITER );

		return [ implode( $delimiter, $result ) ];
	}
}
