<?php

namespace ArrayFunctions\ArrayFunctions;

use ArrayFunctions\Exceptions\RuntimeException;

/**
 * Implements the #af_keysort parser function.
 */
class AFKeysort extends ArrayFunction {
	private const KWARG_DESCENDING = 'descending';
	private const KWARG_CASEINSENSITIVE = 'caseinsensitive';

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
			],
			self::KWARG_CASEINSENSITIVE => [
				'default' => false,
				'type' => 'boolean',
				'description' => 'Whether to ignore case when sorting.'
			]
		];
	}

	/**
	 * @inheritDoc
	 * @throws RuntimeException
	 */
	public function execute( array $array, string $key ): array {
		usort( $array, function ( $l, $r ) use ( $key ) {
			// Inverse if $descending
			$multiplier = $this->getKeywordArg( self::KWARG_DESCENDING ) ? -1 : 1;

			if ( !is_array( $l ) && !is_array( $r ) ) {
				// Regular values are not sorted
				return 0;
			}

			if ( !is_array( $r ) ) {
				// Left is bigger
				return $multiplier;
			}

			if ( !is_array( $l ) ) {
				// Right is bigger
				return -$multiplier;
			}

			$l = $l[$key] ?? null;
			$r = $r[$key] ?? null;

			if ( $l === null && $r === null ) {
				return 0;
			}

			if ( $r === null ) {
				// Left is bigger
				return $multiplier;
			}

			if ( $l === null ) {
				// Right is bigger
				return -$multiplier;
			}

			$caseInsensitive = $this->getKeywordArg( self::KWARG_CASEINSENSITIVE );

			// Do a spaceship comparison
			if ( $caseInsensitive ) {
				return $multiplier * ( mb_strtolower( $l ) <=> mb_strtolower( $r ) );
			} else {
				return $multiplier * ( $l <=> $r );
			}
		} );

		return [ $array ];
	}
}
