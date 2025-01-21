<?php

namespace ArrayFunctions\ArrayFunctions;

use ArrayFunctions\Exceptions\RuntimeException;

/**
 * Implements the #af_range parser function.
 */
class AFRange extends ArrayFunction {
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
}
