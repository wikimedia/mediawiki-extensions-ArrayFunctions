<?php

namespace ArrayFunctions\ArrayFunctions;

use ArrayFunctions\Utils;

/**
 * Implements the #af_split parser function.
 */
class AFSplit extends ArrayFunction {
	/**
	 * @inheritDoc
	 */
	public static function getName(): string {
		return 'af_split';
	}

	/**
	 * @inheritDoc
	 */
	public function execute( ?string $string, ?string $separator = ',' ): array {
		if ( $string === null ) {
			return [ [] ];
		}

		return [ explode( Utils::unescape( $separator ?? ',' ), $string ) ];
	}
}
