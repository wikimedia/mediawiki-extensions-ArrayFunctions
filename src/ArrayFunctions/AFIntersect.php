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
			$result = array_intersect( $array, $array );
		} else {
			$result = array_intersect( $array, ...$arrays );
		}

		return [ $result ];
	}
}
