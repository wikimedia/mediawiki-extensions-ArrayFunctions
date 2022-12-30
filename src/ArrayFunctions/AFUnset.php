<?php

namespace ArrayFunctions\ArrayFunctions;

/**
 * Implements the #af_unset parser function.
 */
class AFUnset extends ArrayFunction {
	/**
	 * @inheritDoc
	 */
	public static function getName(): string {
		return 'af_unset';
	}

	/**
	 * @inheritDoc
	 */
	public function execute( array $array, string ...$keys ): array {
		if ( count( $keys ) === 0 ) {
			// Unset the entire array if no specific keys are given
			return [ [] ];
		}

		$pointer = &$array;
		$end = array_pop($keys);

		foreach ( $keys as $key ) {
			if ( !isset( $pointer[$key] ) ) {
				// If the subarray is not found, there is nothing to unset, and we can just return the original array
				return [ $array ];
			}

			$pointer = &$pointer[$key];
		}

		unset( $pointer[$end] );

		return [ $array ];
	}
}
