<?php

namespace ArrayFunctions;

use ArrayFunctions\Exceptions\ImportException;
use FormatJson;
use Message;
use MWException;
use PPFrame;
use PPNode;
use Xml;

/**
 * Utility class containing functions used by multiple array functions.
 */
class Utils {
	private const EXPORTABLE_TYPES = ["float", "integer", "string", "array"];
	private const TYPE_SEPARATOR = '__^__';

	/**
	 * Imports the given string. The domain of this function is any string that matches the following BNF-grammar:
	 *
	 *  <input> ::= <type> <type separator> <value>
	 *  <value> ::= <any character> | <value> <any character>
	 *  <type> ::= "float" | "integer" | "string" | "array"
	 *  <type separator> = "__-__"
	 *
	 * @param string $input The value to import
	 * @return array|int|string The parsed value
	 *
	 * @throws ImportException When a type mismatch occurred
	 *
	 * @see Utils::export() for the inverse of this method
	 */
	public static function import( string $input ) {
		$inputParts = explode( self::TYPE_SEPARATOR, $input, 2 );

		if ( count( $inputParts ) < 2 ) {
			throw new ImportException( "Missing required type annotation" );
		}

		list( $type, $input ) = $inputParts;

		switch ( $type ) {
			case "float":
				if ( preg_match( '/^-?\d*\.\d+$/', $input ) || preg_match( '/^-?\d+$/', $input ) ) {
					return floatval( $input );
				}

				break;
			case "integer":
				if ( preg_match( '/^-?\d+$/', $input ) ) {
					return intval( $input );
				}

				break;
			case "array":
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

				break;
			case "string":
				return $input;
			default:
				throw new ImportException( "The type annotation \"$type\" is not valid" );
		}

		throw new ImportException( "The type annotation \"$type\" does not match the actual type" );
	}

	/**
	 * Exports the given value.
	 *
	 * @param array|int|float|string $value The value to export
	 * @return string The resulting exported string
	 *
	 * @throws MWException When trying to export a value that cannot be exported
	 *
	 * @see Utils::import() for the inverse of this method
	 */
	public static function export( $value ): string {
		$type = gettype( $value );

		if ( $type === "double" ) {
			$type = "float";
		}

		if ( !in_array( $type, self::EXPORTABLE_TYPES, true ) ) {
			throw new MWException( "Only floats, strings, integers and arrays can be exported" );
		}

		if ( $type === "array" ) {
			$value = base64_encode( FormatJson::encode( $value ) );
		}

		return $type . self::TYPE_SEPARATOR . $value;
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
	 * @param string|Message $message A message key
	 * @param array $args The arguments to pass to the message
	 * @return string
	 */
	public static function error( string $function, $message, array $args = [] ): string {
		if ( !$message instanceof Message ) {
			$message = wfMessage( $message );
		}

		$message->params( $args );

		return Xml::element(
			'strong',
			[ 'class' => 'error' ],
			wfMessage( 'af-error', $function, $message )->parse()
		);
	}
}
