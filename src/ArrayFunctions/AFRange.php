<?php

namespace ArrayFunctions\ArrayFunctions;

use ArrayFunctions\Exceptions\RuntimeException;

/**
 * Implements the #af_range parser function.
 */
class AFRange extends ArrayFunction {
	private const CONFIG_MAX_RANGE_SIZE = 'ArrayFunctionsMaxRangeSize';

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

		$maxRangeSize = $this->getConfigValue( self::CONFIG_MAX_RANGE_SIZE );

		if ( $maxRangeSize >= 0 && $this->computeRangeSize( $start, $stop, $step ) > $maxRangeSize ) {
			throw new RuntimeException( wfMessage( 'af-error-max-range-size-exceeded' ) );
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
