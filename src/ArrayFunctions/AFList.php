<?php

namespace ArrayFunctions\ArrayFunctions;

/**
 * Implements the #af_list parser function.
 */
class AFList extends ArrayFunction {
	/**
	 * @inheritDoc
	 */
	public static function getName(): string {
		return 'af_list';
	}

	/**
	 * @inheritDoc
	 */
	public function execute( ...$values ): array {
		return [ $values ];
	}
}
