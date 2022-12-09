<?php

namespace ArrayFunctions\ArrayFunctions;

/**
 * Implements the #af_sort parser function.
 */
class AFSort extends ArrayFunction {
	/**
	 * @inheritDoc
	 */
	public static function getName(): string {
		return 'af_sort';
	}

	/**
	 * @inheritDoc
	 */
	public function execute( array $array, bool $descending = false ): array {
		if ( $descending ) {
			arsort( $array );
		} else {
			asort( $array );
		}

		return [ $array ];
	}
}
