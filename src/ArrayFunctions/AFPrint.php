<?php

namespace ArrayFunctions\ArrayFunctions;

use ArrayFunctions\Exceptions\RuntimeException;
use ArrayFunctions\Utils;
use Xml;

/**
 * Implements the #af_print parser function.
 */
class AFPrint extends ArrayFunction {
	private const KWARG_END = 'end';

	/**
	 * @inheritDoc
	 */
	public static function getName(): string {
		return 'af_print';
	}

	/**
	 * @inheritDoc
	 */
	public static function getKeywordSpec(): array {
		return [
			self::KWARG_END => [
				'default' => '',
				'type' => 'string',
				'description' => 'String to append to the end of each printed value.'
			]
		];
	}

	/**
	 * @inheritDoc
	 * @throws RuntimeException
	 */
	public function execute( ...$values ): array {
		$result = '';
		$end = Utils::unescape( $this->getKeywordArg( self::KWARG_END ) );

		foreach ( $values as $value ) {
			if ( is_array( $value ) ) {
				$result .= $this->formatArray( $value ) . $end;
			} else {
				$result .= $this->armourWikitext( Utils::stringify( $value ) ) . $end;
			}
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
	private function formatArray( array $value, int $depth = 0 ): string {
		$result = '';

		foreach ( $value as $k => $v ) {
			// Print the key
			$result .= str_repeat( '*', $depth + 1 ) . " $k";

			if ( is_array( $v ) ) {
				// Recursively print the array
				$result .= "\n" . $this->formatArray( $v, $depth + 1 );
			} else {
				// Print the value
				$result .= ": " . $this->armourWikitext( Utils::stringify( $v ) ) . "\n";
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
