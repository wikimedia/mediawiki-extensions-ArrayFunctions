<?php

namespace ArrayFunctions\ArrayFunctions;

/**
 * Implements the #af_reverse parser function.
 */
class AFReverse extends ArrayFunction {
	/**
	 * @inheritDoc
	 */
	public static function getName(): string {
		return 'af_reverse';
	}

	/**
	 * @inheritDoc
	 */
	public function execute( array $array ): array {
		return [ array_reverse( $array ) ];
	}
}
