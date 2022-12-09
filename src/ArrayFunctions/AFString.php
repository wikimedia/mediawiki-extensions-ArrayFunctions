<?php

namespace ArrayFunctions\ArrayFunctions;

/**
 * Implements the #af_string parser function.
 */
class AFString extends ArrayFunction {
	/**
	 * @inheritDoc
	 */
	public static function getName(): string {
		return 'af_string';
	}

	/**
	 * @inheritDoc
	 */
	public function execute( string $value ): array {
		return [ $value ];
	}
}
