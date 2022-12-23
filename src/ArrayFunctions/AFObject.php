<?php

namespace ArrayFunctions\ArrayFunctions;

use ArrayFunctions\Exceptions\RuntimeException;
use ArrayFunctions\Utils;

/**
 * Implements the #af_object parser function.
 */
class AFObject extends ArrayFunction {
	/**
	 * @inheritDoc
	 */
	public static function getName(): string {
		return 'af_object';
	}

	/**
	 * @inheritDoc
	 */
	public static function allowArbitraryKeywordArgs(): bool {
		return true;
	}

	/**
	 * @inheritDoc
	 */
	public function execute(): array {
		return [ $this->getKeywordArgs() ];
	}
}
