<?php

namespace ArrayFunctions\ArrayFunctions;

use ArrayFunctions\Exceptions\RuntimeException;
use ArrayFunctions\Utils;

/**
 * Implements the #af_range parser function.
 */
class AFRange extends ArrayFunction {
	public const DATA_KEY_SIZES = 'ArrayFunctions__af_range_sizes';
	public const DATA_KEY_PREFIX_SIZE = 'ArrayFunctions__af_range_size_';
	public const CONFIG_MAX_RANGE_SIZE = 'ArrayFunctionsMaxRangeSize';

	/**
	 * @inheritDoc
	 */
	public static function getRequiredConfigVariables(): array {
		return [ self::CONFIG_MAX_RANGE_SIZE ];
	}

	/**
	 * @inheritDoc
	 */
	public static function getName(): string {
		return 'af_range';
	}

	/**
	 * @inheritDoc
	 * @throws RuntimeException
	 */
	public function execute( int $start, ?int $stop = null, int $step = 1 ): array {
		if ( $step === 0 ) {
			throw new RuntimeException( wfMessage( 'af-error-range-zero-step' ) );
		}

		if ( $stop === null ) {
			$stop = $start;
			$start = 0;
		}

		$actualRangeSize = $this->computeRangeSize( $start, $stop, $step );
		$randomID = Utils::newRandomID( 18, self::DATA_KEY_PREFIX_SIZE );
		$maxRangeSize = $this->getConfigValue( self::CONFIG_MAX_RANGE_SIZE );

		if ( $maxRangeSize >= 0 && $actualRangeSize > $maxRangeSize ) {
			throw new RuntimeException( wfMessage( 'af-error-max-range-size-exceeded' ) );
		}

		$parserOutput = $this->getParser()->getOutput();
		$parserOutput->setExtensionData( $randomID, $actualRangeSize );

		if ( method_exists( $parserOutput, 'appendExtensionData' ) ) {
			// MediaWiki >= 1.38
			$parserOutput->appendExtensionData( self::DATA_KEY_SIZES, $randomID );
		} else {
			$sizes = $parserOutput->getExtensionData( self::DATA_KEY_SIZES ) ?? [];
			$sizes[$randomID] = true;
			$parserOutput->setExtensionData( self::DATA_KEY_SIZES, $sizes );
		}

		if ( ( $start < $stop && $step < 0 ) || ( $start > $stop && $step > 0 ) || $start === $stop ) {
			return [ [] ];
		}

		if ( abs( $step ) > abs( $stop - $start ) ) {
			return [ [ $start ] ];
		}

		// Make the range non-inclusive
		$stop = $step > 0 ? $stop - 1 : $stop + 1;
		$range = range( $start, $stop, $step );

		return [ $range ];
	}

	/**
	 * Computes the size of the range.
	 *
	 * @param int $start
	 * @param int $stop
	 * @param int $step
	 * @return int
	 */
	private function computeRangeSize( int $start, int $stop, int $step ): int {
		if ( $step > 0 ) {
			$low = $start;
			$high = $stop;
		} else {
			$low = $stop;
			$high = $start;
		}

		$step = abs( $step );

		if ( $low >= $high ) {
			return 0;
		} else {
			return floor( ( $high - $low - 1 ) / $step + 1 );
		}
	}
}
