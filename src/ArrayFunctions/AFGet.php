<?php

namespace ArrayFunctions\ArrayFunctions;

/**
 * Implements the #af_get parser function.
 */
class AFGet extends ArrayFunction {
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
	 * Index the given array using the list of indices, with support for wildcards.
	 *
	 * @param mixed $value
	 * @param array $indices
	 * @return mixed
	 */
	private function index( $value, array $indices ) {
		if ( empty( $indices ) ) {
			return $value;
		}

		if ( !is_array( $value ) ) {
			// Not indexable
			return null;
		}

		$index = array_shift( $indices );

		if ( isset( $value[$index] ) ) {
			return $this->index( $value[$index], $indices );
		} else {
			return null;
		}
	}
}
