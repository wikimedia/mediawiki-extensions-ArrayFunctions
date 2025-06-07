<?php

namespace ArrayFunctions\ArrayFunctions;

use MWException;

/**
 * Implements the #af_sort parser function.
 */
class AFSort extends ArrayFunction {
	private const KWARG_DESCENDING = 'descending';
	private const KWARG_CASEINSENSITIVE = 'caseinsensitive';

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
	 */
	public function execute( array $array ): array {
		usort( $array, [ $this, 'compare' ] );

		return [ $array ];
	}

	/**
	 * Compare two values.
	 *
	 * @param mixed $a
	 * @param mixed $b
	 * @return int
	 * @throws MWException
	 */
	private function compare( $a, $b ): int {
		if ( $this->getKeywordArg( self::KWARG_DESCENDING ) ) {
			$temp = $a;
			$a = $b;
			$b = $temp;
		}

		if ( is_string( $a ) && is_string( $b ) ) {
			return $this->getKeywordArg( self::KWARG_CASEINSENSITIVE ) ?
				strcasecmp( $a, $b ) :
				strcmp( $a, $b );
		}

		return $a <=> $b;
	}
}
