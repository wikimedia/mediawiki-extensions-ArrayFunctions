<?php

namespace ArrayFunctions\ArrayFunctions;

/**
 * Implements the #af_search parser function.
 */
class AFSearch extends ArrayFunction {
	/**
	 * @inheritDoc
	 */
	public static function getName(): string {
		return 'af_search';
	}

	/**
	 * @inheritDoc
	 */
	public function execute( array $array, $value ): array {
		$search = array_search( $value, $array, true );

		if ( $search === false ) {
			return [ '' ];
		}

		return [ $search ];
	}
}
