<?php

namespace ArrayFunctions\ArrayFunctions;

/**
 * Implements the #af_flatten parser function.
 */
class AFFlatten extends ArrayFunction {
	/**
	 * @inheritDoc
	 */
	public static function getName(): string {
		return 'af_flatten';
	}

	/**
	 * @inheritDoc
	 */
	public function execute( array $array, ?int $depth = null ): array {
		return [ $this->flattenRecursively( $array, $depth ) ];
	}

	/**
	 * Flattens the given array up to $depth.
	 *
	 * @param array $array
	 * @param int|null $depth
	 * @return array
	 */
	private function flattenRecursively( array $array, ?int $depth ): array {
		if ( $depth !== null && $depth < 1 ) {
			return $array;
		}

		$newDepth = $depth !== null ? $depth - 1 : null;
		$result = [];

		foreach ( $array as $value ) {
			if ( is_array( $value ) ) {
				$result = array_merge( $result, $this->flattenRecursively( $value, $newDepth ) );
			} else {
				$result[] = $value;
			}
		}

		return $result;
	}
}
