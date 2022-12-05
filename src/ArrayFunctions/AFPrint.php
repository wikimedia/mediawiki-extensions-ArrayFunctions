<?php

namespace ArrayFunctions\ArrayFunctions;

use Xml;

/**
 * Implements the #af_print parser function.
 */
class AFPrint extends ArrayFunction {
	/**
	 * @inheritDoc
	 */
	public static function getName(): string {
		return 'af_print';
	}

	/**
	 * @inheritDoc
	 */
	public function execute( $value ): array {
		if ( is_array( $value ) ) {
			$result = $this->prettyPrint( $value );
		} else {
			$result = $this->armourWikitext( $value );
		}

		return [ $result, 'noparse' => false ];
	}

	/**
	 * Prints an array as wikitext.
	 *
	 * @param array $value The array to print
	 * @param int $depth The current depth (used in the recursive call)
	 * @return string
	 */
	private function prettyPrint( array $value, int $depth = 0 ): string {
		$result = '';

		foreach ( $value as $k => $v ) {
			// Print the key
			$result .= str_repeat( '*', $depth + 1 ) . " $k";

			if ( is_array( $v ) ) {
				// Recursively print the array
				$result .= "\n" . $this->prettyPrint( $v, $depth + 1 );
			} else {
				// Print the value
				$result .= ": " . $this->armourWikitext( $v ) . "\n";
			}
		}

		return $result;
	}

	/**
	 * Armour the given wikitext against transformation.
	 *
	 * @param string $wikitext
	 * @return string
	 */
	private function armourWikitext( string $wikitext ): string {
		return Xml::element( 'nowiki', [], $wikitext );
	}
}
