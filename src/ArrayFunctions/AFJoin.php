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
	public function execute( array $array, string $glue = "" ): array {
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
	 * @param string $glue
	 * @return string
	 */
	private function unescapeGlue( string $glue ): string {
		$unescapedGlue = '';
		$escaped = false;

		foreach ( str_split( $glue ) as $char ) {
			if ( !$escaped ) {
				// If the next character is not escaped, we can simply add it
				if ( $char === '\\' ) {
					$escaped = true;
				} else {
					$unescapedGlue .= $char;
				}
			} else {
				// Otherwise, we replace the escape sequence
				switch ( $char ) {
					case 's':
						$unescapedGlue .= ' ';
						break;
					case 'n':
						$unescapedGlue .= "\n";
						break;
					case '\\':
						$unescapedGlue .= '\\';
						break;
					default:
						# If the escape sequence is not recognized, do not interpret the backslash as an escape and insert it again.
						$unescapedGlue .= '\\' . $char;
						break;
				}

				$escaped = false;
			}
		}

		return $unescapedGlue;
	}
}
