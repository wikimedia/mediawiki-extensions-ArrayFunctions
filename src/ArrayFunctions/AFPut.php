<?php

namespace ArrayFunctions\ArrayFunctions;

/**
 * Implements the #af_put parser function.
 */
class AFPut extends ArrayFunction {
	/**
	 * @inheritDoc
	 */
	public static function getName(): string {
		return 'af_put';
	}

	/**
	 * @inheritDoc
	 */
	public function execute( array $array, $value, string $key, string ...$keys ): array {
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
