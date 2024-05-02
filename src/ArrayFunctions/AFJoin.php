<?php

namespace ArrayFunctions\ArrayFunctions;

use ArrayFunctions\Utils;

/**
 * Implements the #af_join parser function.
 */
class AFJoin extends ArrayFunction {
	/**
	 * @inheritDoc
	 */
	public static function getName(): string {
		return 'af_join';
	}

	/**
	 * @inheritDoc
	 */
	public function execute( array $array, string $glue = "" ): array {
		return [ $this->recursiveJoin( $glue, $array ) ];
	}

	/**
	 * Recursively join the values in the given array.
	 *
	 * @param string $glue
	 * @param array $array
	 * @return string
	 */
	private function recursiveJoin( string $glue, array $array ): string {
		return implode(
			$glue,
			array_map(
				fn ( $value ) => is_array( $value ) ?
					$this->recursiveJoin( $glue, $value ) :
					Utils::stringify( $value ),
				$array
			)
		);
	}
}
