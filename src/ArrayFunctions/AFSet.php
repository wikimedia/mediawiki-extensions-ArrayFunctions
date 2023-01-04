<?php

namespace ArrayFunctions\ArrayFunctions;

/**
 * Implements the #af_set parser function.
 */
class AFSet extends ArrayFunction {
	/**
	 * @inheritDoc
	 */
	public static function getName(): string {
		return 'af_set';
	}

	/**
	 * @inheritDoc
	 */
	public function execute( $value, array $array, string $key, string ...$keys ): array {
		array_unshift( $keys, $key );

		$pointer = &$array;

		foreach ( $keys as $key ) {
			if ( !isset( $pointer[$key] ) ) {
				$pointer[$key] = [];
			}

			$pointer = &$pointer[$key];
		}

		$pointer = $value;

		return [ $array ];
	}
}
