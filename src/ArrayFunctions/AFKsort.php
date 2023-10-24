<?php

namespace ArrayFunctions\ArrayFunctions;

use MWException;

/**
 * Implements the #af_ksort parser function.
 */
class AFKsort extends ArrayFunction {
	private const KWARG_DESCENDING = 'descending';
	private const KWARG_CASEINSENSITIVE = 'caseinsensitive';

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
	 * @throws MWException
	 */
	public function execute( array $array ): array {
		$flags = SORT_STRING;

		if ( $this->getKeywordArg( self::KWARG_CASEINSENSITIVE ) ) {
			$flags |= SORT_FLAG_CASE;
		}

		if ( $this->getKeywordArg( self::KWARG_DESCENDING ) ) {
			krsort( $array, $flags );
		} else {
			ksort( $array, $flags );
		}

		return [ $array ];
	}
}
