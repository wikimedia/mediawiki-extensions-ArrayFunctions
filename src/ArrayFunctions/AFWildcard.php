<?php

namespace ArrayFunctions\ArrayFunctions;

/**
 * Implements the #af_wildcard parser function.
 */
class AFWildcard extends ArrayFunction {
	/**
	 * @inheritDoc
	 */
	public static function getName(): string {
		return 'af_wildcard';
	}

	/**
	 * @inheritDoc
	 */
	public function execute( array $array ): array {
		return [ $this->createWildcardArray( $array ) ?? '' ];
	}

	/**
	 * Create an array that, when indexed with `k`, will return the list of values that you would
	 * get if you were to loop over the original given array, index all sub-arrays with `k` and
	 * collect the results.
	 *
	 * @param array $array
	 * @return mixed
	 */
	private function createWildcardArray( array $array ) {
		if ( empty( $array ) ) {
			return null;
		}

		if ( count( $array ) === 1 ) {
			$firstKey = array_key_first( $array );
			$firstElement = $array[$firstKey];

			if ( is_array( $firstElement ) ) {
				return $firstElement;
			} else {
				return null;
			}
		}

		$prefix = '__array_functions_';
		$array = $this->prefixKeys( $array, $prefix );
		$array = array_filter( $array, fn ( $value ) => is_array( $value ) );
		$array = array_values( $array );
		$array = array_merge_recursive( ...$array );

		return $this->unprefixKeys( $array, $prefix );
	}

	/**
	 * Add the given prefix to all keys in the given array, except for the leaf arrays.
	 *
	 * @param array $array
	 * @param string $prefix
	 * @return array
	 */
	private function prefixKeys( array $array, string $prefix ): array {
		$result = [];

		foreach ( $array as $key => $value ) {
			$prefixedKey = $prefix . $key;
			$result[$prefixedKey] = is_array( $value ) ?
				$this->prefixKeys( $value, $prefix ) :
				$value;
		}

		return $result;
	}

	/**
	 * Remove the given prefix from all keys in the given array, except for the leaf arrays.
	 *
	 * @param array $array
	 * @param string $prefix
	 * @return array
	 */
	private function unprefixKeys( array $array, string $prefix ): array {
		$result = [];

		foreach ( $array as $key => $value ) {
			$originalKey = substr( $key, 0, strlen( $prefix ) ) == $prefix ?
				substr( $key, strlen( $prefix ) ) :
				$key;
			$result[$originalKey] = is_array( $value ) ?
				$this->unprefixKeys( $value, $prefix ) :
				$value;
		}

		return $result;
	}
}
