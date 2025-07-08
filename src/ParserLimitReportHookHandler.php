<?php

namespace ArrayFunctions;

use ArrayFunctions\ArrayFunctions\AFForeach;
use ArrayFunctions\ArrayFunctions\AFPipeline;
use ArrayFunctions\ArrayFunctions\AFRange;
use Config;
use MediaWiki\Hook\ParserLimitReportFormatHook;
use MediaWiki\Hook\ParserLimitReportPrepareHook;

class ParserLimitReportHookHandler implements ParserLimitReportFormatHook, ParserLimitReportPrepareHook {
	/**
	 * @var Config The current MediaWiki configuration
	 */
	private Config $config;

	/**
	 * @param Config $config The current MediaWiki configuration
	 */
	public function __construct( Config $config ) {
		$this->config = $config;
	}

	/**
	 * @inheritDoc
	 */
	public function onParserLimitReportFormat( $key, &$value, &$report, $isHTML, $localize ) {
		switch ( $key ) {
			case 'limitreport-afforeachiterations':
			case 'limitreport-afrangesize':
			case 'limitreport-afpipelinelength':
				if ( count( $value ) !== 2 ) {
					return true;
				}

				[ $count, $limit ] = $value;

				if ( $limit < 0 ) {
					$limit = wfMessage( 'af-limitreport-unlimited-upper-bound' )->plain();
				}

				$value = [ $count, $limit ];
		}

		return true;
	}

	/**
	 * @inheritDoc
	 */
	public function onParserLimitReportPrepare( $parser, $output ) {
		// #af_foreach iteration limits
		$foreachIterationCount = count( $output->getExtensionData( AFForeach::DATA_KEY_ITERATIONS ) ?? [] );
		$foreachIterationLimit = $this->config->get( AFForeach::CONFIG_FOREACH_ITERATION_LIMIT );
		$output->setLimitReportData(
			'limitreport-afforeachiterations',
			[ $foreachIterationCount, $foreachIterationLimit ]
		);

		// #af_range size limits
		$rangeSizeKeys = $output->getExtensionData( AFRange::DATA_KEY_SIZES ) ?? [];
		$largestRangeSize = array_reduce(
			array_keys( $rangeSizeKeys ),
			static function ( int $carry, string $key ) use ( $output ) {
				return max( $carry, $output->getExtensionData( $key ) ?? 0 );
			},
			0
		);
		$maxRangeSize = $this->config->get( AFRange::CONFIG_MAX_RANGE_SIZE );
		$output->setLimitReportData(
			'limitreport-afrangesize',
			[ $largestRangeSize, $maxRangeSize ]
		);

		// #af_pipeline length limits
		$pipelineLengthKeys = $output->getExtensionData( AFPipeline::DATA_KEY_LENGTHS ) ?? [];
		$longestPipelineLength = array_reduce(
			array_keys( $pipelineLengthKeys ),
			static function ( int $carry, string $key ) use ( $output ) {
				return max( $carry, $output->getExtensionData( $key ) ?? 0 );
			},
			0
		);
		$maxPipelineLength = $this->config->get( AFPipeline::CONFIG_MAX_PIPELINE_LENGTH );
		$output->setLimitReportData(
			'limitreport-afpipelinelength',
			[ $longestPipelineLength, $maxPipelineLength ]
		);
	}
}
