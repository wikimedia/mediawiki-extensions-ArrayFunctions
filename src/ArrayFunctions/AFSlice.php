<?php

namespace ArrayFunctions\ArrayFunctions;

/**
 * Implements the #af_slice parser function.
 */
class AFSlice extends ArrayFunction {
	/**
	 * @inheritDoc
	 */
	public static function getName(): string {
		return 'af_slice';
	}

	/**
	 * @inheritDoc
	 */
	public function execute( array $array, int $offset, ?int $length = null ): array {
		return [ array_slice( $array, $offset, $length ) ];
	}
}
