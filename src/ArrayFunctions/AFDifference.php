<?php

namespace ArrayFunctions\ArrayFunctions;

/**
 * Implements the #af_difference parser function.
 */
class AFDifference extends ArrayFunction {
	/**
	 * @inheritDoc
	 */
	public static function getName(): string {
		return 'af_difference';
	}

	/**
	 * @inheritDoc
	 */
	public function execute( array $array, array ...$arrays ): array {
		if ( empty( $arrays ) ) {
			// PHP <= 7.4 requires two parameters
			$result = array_diff( $array, [] );
		} else {
			$result = array_diff( $array, ...$arrays );
		}

		return [ $result ];
	}
}
