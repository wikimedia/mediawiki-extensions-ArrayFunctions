<?php

namespace ArrayFunctions\ArrayFunctions;

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
		return [ $this->index( $value, $indices ) ?? '' ];
	}

	/**
	 * Index the given array with the given list of indices.
	 *
	 * @param array $value
	 * @param array $indices
	 * @return array|null
	 */
	private function index( array $value, array $indices ) {
		foreach ( $indices as $index ) {
			if ( !is_array( $value ) ) {
				// Not indexable
				return null;
			}

			$handler = $this->indexHandler( $index );
			$value = $handler( $value );
		}

		return $value;
	}

	/**
	 * Return a callable that handles the given index.
	 *
	 * @param mixed $index
	 * @return callable
	 */
	private function indexHandler( $index ): callable {
		if ( $index === self::IDX_WILDCARD ) {
			// phpcs:ignore
			return fn ( $value ) => ( new AFWildcard( $this->getParser(), $this->getFrame() ) )->execute( $value )[0] ?? null;
		} elseif ( $index === self::IDX_REVERSE ) {
			// phpcs:ignore
			return fn ( $value ) => ( new AFReverse( $this->getParser(), $this->getFrame() ) )->execute( $value )[0] ?? null;
		} elseif ( $index === self::IDX_FLATTEN ) {
			// phpcs:ignore
			return fn ( $value ) => ( new AFFlatten( $this->getParser(), $this->getFrame() ) )->execute( $value, 1 )[0] ?? null;
		} elseif ( $index === self::IDX_FLATTEN_RECURSIVE ) {
			// phpcs:ignore
			return fn ( $value ) => ( new AFFlatten( $this->getParser(), $this->getFrame() ) )->execute( $value, null )[0] ?? null;
		} elseif ( $index === self::IDX_UNIQUE ) {
			// phpcs:ignore
			return fn ( $value ) => ( new AFUnique( $this->getParser(), $this->getFrame() ) )->execute( $value )[0] ?? null;
		} elseif ( preg_match( self::IDX_SLICE_REGEX, $index, $matches, PREG_UNMATCHED_AS_NULL ) === 1 ) {
			$offset = intval( $matches[1] ?? 0 );
			$length = !empty( $matches[2] ) ? intval( $matches[2] ) - $offset : null;
			// phpcs:ignore
			return fn ( $value ) => ( new AFSlice( $this->getParser(), $this->getFrame() ) )->execute( $value, $offset, $length )[0] ?? null;
		} else {
			// phpcs:ignore
			return fn ( $value ) => $value[$index] ?? null;
		}
	}
}
