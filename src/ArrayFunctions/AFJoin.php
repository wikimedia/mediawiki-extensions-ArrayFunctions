<?php

namespace ArrayFunctions\ArrayFunctions;

use ArrayFunctions\Utils;

/**
 * Implements the #af_join parser function.
 */
class AFJoin extends ArrayFunction {
	/**
	 * @inheritDoc
	 */
	public static function getName(): string {
		return 'af_join';
	}

	/**
	 * @inheritDoc
	 */
	public function execute( ?string $glue, array $array ): array {
		return [$this->recursiveJoin( $this->unescapeGlue( $glue ), $array )];
	}

	/**
	 * Recursively join the values in the given array.
	 *
	 * @param string $glue
	 * @param array $array
	 * @return string
	 */
	private function recursiveJoin( string $glue, array $array ): string {
		return implode( $glue, array_map( fn ( $value ) => is_array( $value ) ? $this->recursiveJoin( $glue, $value ) : Utils::stringify( $value ), $array ) );
	}

	/**
	 * Transforms escape sequences in $glue.
	 *
	 * @param string|null $glue
	 * @return string
	 */
	private function unescapeGlue( ?string $glue ): string {
		if ( $glue === null ) {
			return "";
		}

		return str_replace( ['\s', '\n'], [' ', "\n"], $glue );
	}
}