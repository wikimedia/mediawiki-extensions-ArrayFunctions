<?php

namespace ArrayFunctions\ArrayFunctions;

use ArrayFunctions\Exceptions\RuntimeException;

/**
 * Implements the #af_ksort parser function.
 */
class AFKsort extends ArrayFunction {
	private const KWARG_DESCENDING = 'descending';

	/**
	 * @inheritDoc
	 */
	public static function getName(): string {
		return 'af_ksort';
	}

	/**
	 * @inheritDoc
	 */
	public static function getKeywordSpec(): array {
		return [
			self::KWARG_DESCENDING => [
				'default' => false,
				'type' => 'boolean',
				'description' => 'Whether to sort keys in a descending order.'
			]
		];
	}

	/**
	 * @inheritDoc
	 * @throws RuntimeException
	 */
	public function execute( array $array ): array {
		if ( $this->getKeywordArg( self::KWARG_DESCENDING ) ) {
			krsort( $array );
		} else {
			ksort( $array );
		}

		return [ $array ];
	}
}
