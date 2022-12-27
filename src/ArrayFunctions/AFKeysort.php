<?php

namespace ArrayFunctions\ArrayFunctions;

use ArrayFunctions\Exceptions\RuntimeException;

/**
 * Implements the #af_keysort parser function.
 */
class AFKeysort extends ArrayFunction {
	private const KWARG_DESCENDING = 'descending';

	/**
	 * @inheritDoc
	 */
	public static function getName(): string {
		return 'af_keysort';
	}

	/**
	 * @inheritDoc
	 */
	public static function getKeywordSpec(): array {
		return [
			self::KWARG_DESCENDING => [
				'default' => false,
				'type' => 'boolean',
				'description' => 'Whether to sort items in a descending order.'
			]
		];
	}

	/**
	 * @inheritDoc
	 * @throws RuntimeException
	 */
	public function execute( array $array, string $key ): array {
		usort( $array, function ( $l, $r ) use ( $key ) {
			$l = $l[$key] ?? null;
			$r = $r[$key] ?? null;

			// Inverse if $descending
			$multiplier = $this->getKeywordArg( self::KWARG_DESCENDING ) ? -1 : 1;

			return $multiplier * ($l <=> $r);
		} );

		return [ $array ];
	}
}
