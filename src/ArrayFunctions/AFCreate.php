<?php

namespace ArrayFunctions\ArrayFunctions;

/**
 * Implements the #af_create parser function.
 */
class AFCreate extends ArrayFunction {
	/**
	 * @inheritDoc
	 */
	public static function getName(): string {
		return 'af_create';
	}

	/**
	 * @inheritDoc
	 */
	public function execute( ...$values ): array {
		return [ $values ];
	}
}
