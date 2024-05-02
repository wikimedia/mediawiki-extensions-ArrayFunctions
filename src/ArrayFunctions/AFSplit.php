<?php

namespace ArrayFunctions\ArrayFunctions;

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

		return [ explode( $separator ?? ',', $string ) ];
	}
}
