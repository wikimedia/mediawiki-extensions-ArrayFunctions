<?php

namespace ArrayFunctions\ArrayFunctions;

/**
 * Implements the #af_bool parser function.
 */
class AFBool extends ArrayFunction {
	/**
	 * @inheritDoc
	 */
	public static function getName(): string {
		return 'af_bool';
	}

	/**
	 * @inheritDoc
	 */
	public function execute( bool $value ): array {
		return [ $value ];
	}
}
