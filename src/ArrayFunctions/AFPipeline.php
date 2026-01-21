<?php

namespace ArrayFunctions\ArrayFunctions;

use ArrayFunctions\Exceptions\RuntimeException;
use ArrayFunctions\Utils;
use PPNode;

/**
 * Implements the #af_pipeline parser function.
 */
class AFPipeline extends ArrayFunction {
	public const DATA_KEY_LENGTHS = 'ArrayFunctions__af_pipeline_lengths';
	public const DATA_KEY_PREFIX_LENGTH = 'ArrayFunctions__af_pipeline_length_';
	public const CONFIG_MAX_PIPELINE_LENGTH = 'ArrayFunctionsMaxPipelineLength';

	private const KWARG_PARAMETER = 'parameter';

	/**
	 * @inheritDoc
	 */
	public static function skipEmptyFirstArg(): bool {
		return true;
	}

	/**
	 * @inheritDoc
	 */
	public static function getName(): string {
		return 'af_pipeline';
	}

	public static function getRequiredConfigVariables(): array {
		return [ self::CONFIG_MAX_PIPELINE_LENGTH ];
	}

	/**
	 * @inheritDoc
	 */
	public static function getKeywordSpec(): array {
		return [
			self::KWARG_PARAMETER => [
				'default' => 'prev',
				'type' => 'string',
				'description' => 'The name to use for the previous value.'
			]
		];
	}

	/**
	 * @inheritDoc
	 */
	public function execute( $initial, ?PPNode ...$steps ): array {
		$actualPipelineLength = count( $steps ) + 1;
		$maxPipelineLength = $this->getConfigValue( self::CONFIG_MAX_PIPELINE_LENGTH );

		$randomID = Utils::newRandomID( 18, self::DATA_KEY_PREFIX_LENGTH );
		$parserOutput = $this->getParser()->getOutput();
		$parserOutput->setExtensionData( $randomID, $actualPipelineLength );

		if ( method_exists( $parserOutput, 'appendExtensionData' ) ) {
			// MediaWiki >= 1.38
			$parserOutput->appendExtensionData( self::DATA_KEY_LENGTHS, $randomID );
		} else {
			$lengths = $parserOutput->getExtensionData( self::DATA_KEY_LENGTHS ) ?? [];
			$lengths[$randomID] = true;
			$parserOutput->setExtensionData( self::DATA_KEY_LENGTHS, $lengths );
		}

		if ( $maxPipelineLength >= 0 && $actualPipelineLength > $maxPipelineLength ) {
			throw new RuntimeException( Utils::createMessageArray( 'af-error-max-pipeline-length-exceeded' ) );
		}

		$parameter = $this->getKeywordArg( self::KWARG_PARAMETER );
		$result = $initial;

		foreach ( $steps as $step ) {
			$args = $this->getFrame()->getArguments();
			$args[$parameter] = Utils::export( $result );

			$nodeArray = $this->getParser()->getPreprocessor()->newPartNodeArray( $args );
			$childFrame = $this->getFrame()->newChild( $nodeArray, $this->getFrame()->getTitle() );

			$result = trim( $childFrame->expand( $step ) );
		}

		return [ $result ];
	}
}
