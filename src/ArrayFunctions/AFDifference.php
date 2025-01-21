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
			$result = array_udiff( $array, [], fn ( $a, $b ): int => $a <=> $b );
		} else {
			// We use `call_user_func_array` instead of calling `array_udiff` directly to avoid
			// the error "Cannot use positional argument after argument unpacking".
			$args = [ $array, ...$arrays, fn ( $a, $b ): int => $a <=> $b ];
			$result = call_user_func_array( 'array_udiff', $args );
		}

		return [ $result ];
	}
}
