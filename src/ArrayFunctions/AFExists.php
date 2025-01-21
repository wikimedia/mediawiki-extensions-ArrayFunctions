<?php

namespace ArrayFunctions\ArrayFunctions;

/**
 * Implements the #af_exists parser function.
 */
class AFExists extends ArrayFunction {
	/**
	 * @inheritDoc
	 */
	public static function getName(): string {
		return 'af_exists';
	}

	/**
	 * @inheritDoc
	 */
	public function execute( array $array, string ...$keys ): array {
		return [ $this->arrayKeyExistsList( $array, $keys ) ];
	}

	/**
	 * Index the given array with the given list of indices.
	 *
	 * @param array $value
	 * @param array $keys
	 * @return bool
	 */
	private function arrayKeyExistsList( array $value, array $keys ): bool {
		foreach ( $keys as $key ) {
			if ( !is_array( $value ) || !array_key_exists( $key, $value ) ) {
				return false;
			}

			$value = $value[$key];
		}

		return true;
	}
}
