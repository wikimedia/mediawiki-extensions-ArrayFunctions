<?php

namespace ArrayFunctions\ArrayFunctions;

/**
 * Implements the #af_unique parser function.
 */
class AFUnique extends ArrayFunction {
	/**
	 * @inheritDoc
	 */
	public static function getName(): string {
		return 'af_unique';
	}

	/**
	 * @inheritDoc
	 */
	public function execute( array $array ): array {
		return [ array_unique( $array ) ];
	}
}
