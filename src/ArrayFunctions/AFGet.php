<?php

namespace ArrayFunctions\ArrayFunctions;

use ArrayFunctions\ArrayFunctionFactory;
use MediaWiki\MediaWikiServices;

/**
 * Implements the #af_get parser function.
 */
class AFGet extends ArrayFunction {
	private const IDX_WILDCARD = '*';
	private const IDX_REVERSE = '<-';
	private const IDX_FLATTEN = '><';
	private const IDX_FLATTEN_RECURSIVE = '>><<';
	private const IDX_UNIQUE = '#';
	private const IDX_SLICE_REGEX = '/^(\d+)\.\.(\d*)$/';

	/**
	 * @inheritDoc
	 */
	public static function getName(): string {
		return 'af_get';
	}

	/**
	 * @inheritDoc
	 */
	public function execute( array $value, string ...$indices ): array {
		return [ $this->indexList( $value, $indices ) ?? '' ];
	}

	/**
	 * Index the given array with the given list of indices.
	 *
	 * @param array $value
	 * @param array $indices
	 * @return array|null
	 */
	private function indexList( array $value, array $indices ) {
		foreach ( $indices as $index ) {
			if ( !is_array( $value ) ) {
				// Not indexable
				return null;
			}

			$value = $this->index( $value, $index );
		}

		return $value;
	}

	/**
	 * Index the given array with the given index.
	 *
	 * @param array $array
	 * @param string $index
	 * @return mixed
	 */
	private function index( array $array, string $index ) {
		// FIXME (2025-05-27): Implement proper DI for ArrayFunctions.
		/** @var ArrayFunctionFactory $factory */
		$factory = MediaWikiServices::getInstance()->get( 'ArrayFunctions.ArrayFunctionFactory' );

		if ( isset( $array[ $index ] ) ) {
			return $array[ $index ];
		} elseif ( $index === self::IDX_WILDCARD ) {
			return $factory
				->createArrayFunction( AFGroup::class, $this->getParser(), $this->getFrame() )
				->execute( $array )[0] ?? null;
		} elseif ( $index === self::IDX_REVERSE ) {
			return $factory
				->createArrayFunction( AFReverse::class, $this->getParser(), $this->getFrame() )
				->execute( $array )[0] ?? null;
		} elseif ( $index === self::IDX_FLATTEN ) {
			return $factory
				->createArrayFunction( AFFlatten::class, $this->getParser(), $this->getFrame() )
				->execute( $array, 1 )[0] ?? null;
		} elseif ( $index === self::IDX_FLATTEN_RECURSIVE ) {
			return $factory
				->createArrayFunction( AFFlatten::class, $this->getParser(), $this->getFrame() )
				->execute( $array, null )[0] ?? null;
		} elseif ( $index === self::IDX_UNIQUE ) {
			return $factory
				->createArrayFunction( AFUnique::class, $this->getParser(), $this->getFrame() )
				->execute( $array )[0] ?? null;
		} elseif ( preg_match( self::IDX_SLICE_REGEX, $index, $matches, PREG_UNMATCHED_AS_NULL ) === 1 ) {
			$offset = intval( $matches[1] ?? 0 );
			$length = !empty( $matches[2] ) ? intval( $matches[2] ) - $offset : null;
			return $factory
				->createArrayFunction( AFSlice::class, $this->getParser(), $this->getFrame() )
				->execute( $array, $offset, $length )[0] ?? null;
		} else {
			return null;
		}
	}
}
