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
	public function execute( array $array, string $key ): array {
		return [ isset( $array[$key] ) ];
	}
}
