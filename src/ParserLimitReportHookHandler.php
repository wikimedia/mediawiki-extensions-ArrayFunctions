<?php

namespace ArrayFunctions;

use ArrayFunctions\ArrayFunctions\AFForeach;
use ArrayFunctions\ArrayFunctions\AFRange;
use Config;
use MediaWiki\Hook\ParserLimitReportPrepareHook;

class ParserLimitReportHookHandler implements ParserLimitReportPrepareHook {
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
	public function onParserLimitReportPrepare( $parser, $output ) {
		$foreachIterationCount = count( $output->getExtensionData( AFForeach::DATA_KEY_ITERATIONS ) ?? [] );

		$foreachIterationLimit = $this->config->get( AFForeach::CONFIG_FOREACH_ITERATION_LIMIT );
		$foreachIterationLimit = $foreachIterationLimit >= 0 ?
			$foreachIterationLimit :
			wfMessage( 'af-limitreport-unlimited-upper-bound' )->plain();

		$output->setLimitReportData(
			'limitreport-afforeachiterations',
			[ $foreachIterationCount, $foreachIterationLimit ]
		);

		$rangeSizeKeys = $output->getExtensionData( AFRange::DATA_KEY_SIZES ) ?? [];
		$largestRangeSize = array_reduce(
			array_keys( $rangeSizeKeys ),
			static function ( int $carry, string $key ) use ( $output ) {
				return max( $carry, $output->getExtensionData( $key ) ?? 0 );
			},
			0
		);

		$maxRangeSize = $this->config->get( AFRange::CONFIG_MAX_RANGE_SIZE );
		$maxRangeSize = $maxRangeSize >= 0 ?
			$maxRangeSize :
			wfMessage( 'af-limitreport-unlimited-upper-bound' )->plain();

		$output->setLimitReportData(
			'limitreport-afrangesize',
			[ $largestRangeSize, $maxRangeSize ]
		);
	}
}
