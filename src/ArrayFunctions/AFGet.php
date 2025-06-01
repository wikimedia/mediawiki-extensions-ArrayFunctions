<?php

namespace ArrayFunctions\ArrayFunctions;

use MediaWiki\MediaWikiServices;

/**
 * Implements the #af_get parser function.
 */
class AFGet extends ArrayFunction {
	private const IDX_GROUP = '*';
	private const IDX_REVERSE = '<-';
	private const IDX_FLATTEN = '><';
	private const IDX_FLATTEN_RECURSIVE = '>><<';
	private const IDX_UNIQUE = '#';
	private const IDX_SHOW = '!';
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
			$value = $this->index( $value, $index );
		}

		return $value;
	}

	/**
	 * Index the given value with the given index.
	 *
	 * @param mixed $value
	 * @param string $index
	 * @return mixed
	 */
	private function index( $value, string $index ) {
		if ( is_array( $value ) && isset( $value[ $index ] ) ) {
			// If the given index is a *real* index, it always takes precedence.
			return $value[ $index ];
		}

		if ( $index === self::IDX_GROUP ) {
			return $this->indexGroup( $value );
		} elseif ( $index === self::IDX_REVERSE ) {
			return $this->indexReverse( $value );
		} elseif ( $index === self::IDX_FLATTEN ) {
			return $this->indexFlatten( $value );
		} elseif ( $index === self::IDX_FLATTEN_RECURSIVE ) {
			return $this->indexFlattenRecursive( $value );
		} elseif ( $index === self::IDX_UNIQUE ) {
			return $this->indexUnique( $value );
		} elseif ( preg_match( self::IDX_SLICE_REGEX, $index, $matches, PREG_UNMATCHED_AS_NULL ) === 1 ) {
			$offset = intval( $matches[1] ?? 0 );
			$length = !empty( $matches[2] ) ? intval( $matches[2] ) - $offset : null;
			return $this->indexSlice( $value, $offset, $length );
		} elseif ( $index === self::IDX_SHOW ) {
			return $this->indexShow( $value );
		} else {
			return null;
		}
	}

	/**
	 * Apply #af_group on the given value.
	 *
	 * @param mixed $value
	 * @return array|null
	 */
	private function indexGroup( $value ): ?array {
		if ( !is_array( $value ) ) {
			return null;
		}

		return MediaWikiServices::getInstance()
			->get( 'ArrayFunctions.ArrayFunctionFactory' )
			->createArrayFunction( AFGroup::class, $this->getParser(), $this->getFrame() )
			->execute( $value )[0] ?? null;
	}

	/**
	 * Apply #af_reverse on the given value.
	 *
	 * @param mixed $value
	 * @return array|null
	 */
	private function indexReverse( $value ): ?array {
		if ( !is_array( $value ) ) {
			return null;
		}

		return MediaWikiServices::getInstance()
			->get( 'ArrayFunctions.ArrayFunctionFactory' )
			->createArrayFunction( AFReverse::class, $this->getParser(), $this->getFrame() )
			->execute( $value )[0] ?? null;
	}

	/**
	 * Apply #af_flatten on the given value.
	 *
	 * @param mixed $value
	 * @return array|null
	 */
	private function indexFlatten( $value ): ?array {
		if ( !is_array( $value ) ) {
			return null;
		}

		return MediaWikiServices::getInstance()
			->get( 'ArrayFunctions.ArrayFunctionFactory' )
			->createArrayFunction( AFFlatten::class, $this->getParser(), $this->getFrame() )
			->execute( $value, 1 )[0] ?? null;
	}

	/**
	 * Apply #af_flatten recursively on the given value.
	 *
	 * @param mixed $value
	 * @return array|null
	 */
	private function indexFlattenRecursive( $value ): ?array {
		if ( !is_array( $value ) ) {
			return null;
		}

		return MediaWikiServices::getInstance()
			->get( 'ArrayFunctions.ArrayFunctionFactory' )
			->createArrayFunction( AFFlatten::class, $this->getParser(), $this->getFrame() )
			->execute( $value, null )[0] ?? null;
	}

	/**
	 * Apply #af_unique on the given value.
	 *
	 * @param mixed $value
	 * @return array|null
	 */
	private function indexUnique( $value ): ?array {
		if ( !is_array( $value ) ) {
			return null;
		}

		return MediaWikiServices::getInstance()
			->get( 'ArrayFunctions.ArrayFunctionFactory' )
			->createArrayFunction( AFUnique::class, $this->getParser(), $this->getFrame() )
			->execute( $value )[0] ?? null;
	}

	/**
	 * Apply #af_slice on the given value.
	 *
	 * @param mixed $value
	 * @param int $offset
	 * @param int|null $length
	 * @return array|null
	 */
	private function indexSlice( $value, int $offset, ?int $length ): ?array {
		if ( !is_array( $value ) ) {
			return null;
		}

		return MediaWikiServices::getInstance()
			->get( 'ArrayFunctions.ArrayFunctionFactory' )
			->createArrayFunction( AFSlice::class, $this->getParser(), $this->getFrame() )
			->execute( $value, $offset, $length )[0] ?? null;
	}

	/**
	 * Apply #af_show on the given value.
	 *
	 * @param mixed $value
	 * @return string|null
	 */
	private function indexShow( $value ): ?string {
		$show = MediaWikiServices::getInstance()
			->get( 'ArrayFunctions.ArrayFunctionFactory' )
			->createArrayFunction( AFShow::class, $this->getParser(), $this->getFrame() );

		$show->setKeywordArgs( [ 'format' => 'table, simple' ] );

		return $show->execute( $value )[0] ?? null;
	}
}
