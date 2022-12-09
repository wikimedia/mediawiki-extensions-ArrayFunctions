<?php

namespace ArrayFunctions\ArrayFunctions;

/**
 * Implements the #af_count parser function.
 */
class AFCount extends ArrayFunction {
	/**
	 * @inheritDoc
	 */
	public static function getName(): string {
		return 'af_count';
	}

	/**
	 * @inheritDoc
	 */
	public function execute( array $array, bool $recursive = false ): array {
		return [ $recursive ? count( $array, COUNT_RECURSIVE ) : count( $array ) ];
	}
}
