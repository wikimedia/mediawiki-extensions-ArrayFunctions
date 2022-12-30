<?php

namespace ArrayFunctions\ArrayFunctions;

/**
 * Implements the #af_float parser function.
 */
class AFFloat extends ArrayFunction {
	/**
	 * @inheritDoc
	 */
	public static function getName(): string {
		return 'af_float';
	}

	/**
	 * @inheritDoc
	 */
	public function execute( float $value ): array {
		if ( $value > PHP_FLOAT_MAX)

		return [ $value ];
	}
}
