<?php

namespace ArrayFunctions\ArrayFunctions;

/**
 * Implements the #af_intersect parser function.
 */
class AFIntersect extends ArrayFunction {
	/**
	 * @inheritDoc
	 */
	public static function getName(): string {
		return 'af_intersect';
	}

	/**
	 * @inheritDoc
	 */
	public function execute( array $array, array ...$arrays ): array {
		if ( empty( $arrays ) ) {
			// PHP <= 7.4 requires two parameters
			$result = array_uintersect( $array, $array, static fn ( $a, $b ) => $a <=> $b );
		} else {
			$args = [ $array, ...$arrays, static fn ( $a, $b ) => $a <=> $b ];
			$result = call_user_func_array( 'array_uintersect', $args );
		}

		return [ $result ];
	}
}
