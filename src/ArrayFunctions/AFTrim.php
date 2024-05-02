<?php

namespace ArrayFunctions\ArrayFunctions;

/**
 * Implements the #af_trim parser function.
 */
class AFTrim extends ArrayFunction {
	/**
	 * @inheritDoc
	 */
	public static function getName(): string {
		return 'af_trim';
	}

	/**
	 * @inheritDoc
	 */
	public function execute( ?string $string, string $characters ): array {
		return [ trim( $string ?? '', $characters ) ];
	}
}
