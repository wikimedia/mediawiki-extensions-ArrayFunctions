<?php

namespace ArrayFunctions;

use FormatJson;
use PPFrame;
use PPNode;
use Xml;

/**
 * Utility class containing functions used by multiple array functions.
 */
class Utils {
	/**
	 * Imports the given string.
	 *
	 * @param string $input The value to import
	 * @return array|int|string The parsed value
	 * @see Utils::export() for the inverse of this method
	 */
	public static function import( string $input ) {
		// Try decoding and parsing to see if it was a base64 encoded JSON string
		$maybeJson = base64_decode( $input );

		if ( $maybeJson !== false ) {
			if ( $maybeJson === '{}' ) {
				// Short-circuit for empty objects, since FormatJson does not handle them correctly
				return [];
			}

			$status = FormatJson::parse( $maybeJson, FormatJson::FORCE_ASSOC | FormatJson::TRY_FIXING | FormatJson::STRIP_COMMENTS );

			if ( $status->isGood() ) {
				return $status->getValue();
			}
		}

		return $input;
	}

	/**
	 * Exports the given value.
	 *
	 * @param array|int|string $value The value to export
	 * @return string The resulting string
	 * @see Utils::import() for the inverse of this method
	 */
	public static function export( $value ): string {
		if ( is_array( $value ) ) {
			return base64_encode( FormatJson::encode( $value ) );
		}

		return strval( $value );
	}

	/**
	 * Expands the given PPNode and does some post-processing.
	 *
	 * @param PPNode|string $node The node to expand, or a string
	 * @param PPFrame $frame The frame to use for expansion
	 * @param int $flags The flags to pass
	 * @return string The expanded node
	 */
	public static function expandNode( $node, PPFrame $frame, int $flags = 0 ): string {
		return trim( $frame->expand( $node, $flags ) );
	}

	/**
	 * Returns an error.
	 *
	 * @param string $function The name of the parser function that caused the error
	 * @param string $message A message key
	 * @param array $args The arguments to pass to the message
	 * @return string
	 */
	public static function error( string $function, string $message, array $args = [] ): string {
		return Xml::element(
			'strong',
			[ 'class' => 'error' ],
			wfMessage( 'af-error', $function, wfMessage( $message, ...$args ) )->parse()
		);
	}
}
