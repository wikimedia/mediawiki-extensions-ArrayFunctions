<?php

namespace ArrayFunctions\ArrayFunctions;

use ArrayFunctions\Exceptions\RuntimeException;

/**
 * Implements the #af_sort parser function.
 */
class AFSort extends ArrayFunction {
	private const KWARG_DESCENDING = 'descending';

	/**
	 * @inheritDoc
	 */
	public static function getName(): string {
		return 'af_sort';
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
	public function execute( array $array ): array {
		if ( $this->getKeywordArg( self::KWARG_DESCENDING ) ) {
			rsort( $array );
		} else {
			sort( $array );
		}

		return [ $array ];
	}
}
