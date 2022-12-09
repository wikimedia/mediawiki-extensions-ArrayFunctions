<?php

namespace ArrayFunctions\ArrayFunctions;

/**
 * Implements the #af_int parser function.
 */
class AFInt extends ArrayFunction {
	/**
	 * @inheritDoc
	 */
	public static function getName(): string {
		return 'af_int';
	}

	/**
	 * @inheritDoc
	 */
	public function execute( int $value ): array {
		return [ $value ];
	}
}
