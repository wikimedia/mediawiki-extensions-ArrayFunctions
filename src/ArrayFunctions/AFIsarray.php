<?php

namespace ArrayFunctions\ArrayFunctions;

/**
 * Implements the #af_isarray parser function.
 */
class AFIsarray extends ArrayFunction {
	/**
	 * @inheritDoc
	 */
	public static function getName(): string {
		return 'af_isarray';
	}

	/**
	 * @inheritDoc
	 */
	public function execute( $value ): array {
		return [ is_array( $value ) ];
	}
}
