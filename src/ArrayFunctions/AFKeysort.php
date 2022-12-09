<?php

namespace ArrayFunctions\ArrayFunctions;

/**
 * Implements the #af_keysort parser function.
 */
class AFKeysort extends ArrayFunction {
	/**
	 * @inheritDoc
	 */
	public static function getName(): string {
		return 'af_keysort';
	}

	/**
	 * @inheritDoc
	 */
	public function execute( array $array, string $key, bool $descending = false ): array {
		usort( $array, function ( $l, $r ) use ( $key, $descending ) {
			$l = $l[$key] ?? null;
			$r = $r[$key] ?? null;

			// Inverse if $descending
			$multiplier = $descending ? -1 : 1;

			return $multiplier * ($l <=> $r);
		} );

		return [ $array ];
	}
}
