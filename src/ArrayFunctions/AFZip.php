<?php

namespace ArrayFunctions\ArrayFunctions;

/**
 * Implements the #af_zip parser function.
 */
class AFZip extends ArrayFunction {
	/**
	 * @inheritDoc
	 */
	public static function getName(): string {
		return 'af_zip';
	}

	/**
	 * @inheritDoc
	 */
	public function execute( array $array, array ...$arrays ): array {
		$sharedIndices = $this->calculateSharedIndices( $array, $arrays );

		$result = [];

		foreach ( $sharedIndices as $sharedIndex ) {
			$result[$sharedIndex] = array_column( [ $array, ...$arrays ], $sharedIndex );
		}

		return [ $result ];
	}

	/**
	 * Returns the indices shared by all the given arrays.
	 *
	 * @param array $array The first array to get the shared indices of
	 * @param array $arrays The other arrays to get the shared indices of
	 * @return array The list of shared indices
	 */
	private function calculateSharedIndices( array $array, array $arrays ): array {
		if ( empty( $arrays ) ) {
			return array_keys( $array );
		} else {
			return array_intersect( array_keys( $array ), ...array_map( 'array_keys', $arrays ) );
		}
	}
}
