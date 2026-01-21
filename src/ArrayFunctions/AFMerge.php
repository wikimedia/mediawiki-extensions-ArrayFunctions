<?php

namespace ArrayFunctions\ArrayFunctions;

/**
 * Implements the #af_merge parser function.
 */
class AFMerge extends ArrayFunction {
	/**
	 * @inheritDoc
	 */
	public static function skipEmptyFirstArg(): bool {
		return true;
	}

	/**
	 * @inheritDoc
	 */
	public static function getName(): string {
		return 'af_merge';
	}

	/**
	 * @inheritDoc
	 */
	public function execute( array $array, array ...$arrays ): array {
		return [ array_merge( $array, ...$arrays ) ];
	}
}
