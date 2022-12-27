<?php

namespace ArrayFunctions\ArrayFunctions;

use ArrayFunctions\Exceptions\RuntimeException;

/**
 * Implements the #af_count parser function.
 */
class AFCount extends ArrayFunction {
	private const KWARG_RECURSIVE = 'recursive';

	/**
	 * @inheritDoc
	 */
	public static function getName(): string {
		return 'af_count';
	}

	/**
	 * @inheritDoc
	 */
	public static function getKeywordSpec(): array {
		return [
			self::KWARG_RECURSIVE => [
				'default' => false,
				'type' => 'boolean',
				'description' => 'Whether to count items recursively.'
			]
		];
	}

	/**
	 * @inheritDoc
	 * @throws RuntimeException
	 */
	public function execute( array $array ): array {
		return [ $this->getKeywordArg( self::KWARG_RECURSIVE ) ? count( $array, COUNT_RECURSIVE ) : count( $array ) ];
	}
}
