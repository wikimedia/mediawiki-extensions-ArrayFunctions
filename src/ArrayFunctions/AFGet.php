<?php

namespace ArrayFunctions\ArrayFunctions;

/**
 * Implements the #af_get parser function.
 */
class AFGet extends ArrayFunction {
	/**
	 * @inheritDoc
	 */
	public static function getName(): string {
		return 'af_get';
	}

	/**
	 * @inheritDoc
	 */
	public function execute( array $value, string ...$indices ): array {
		foreach ( $indices as $index ) {
			if ( !is_array( $value ) || !isset( $value[$index] ) ) {
				return [ '' ];
			}

			$value = $value[$index];
		}

		return [ $value ];
	}
}
