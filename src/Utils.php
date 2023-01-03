<?php

namespace ArrayFunctions;

use FormatJson;
use Message;

/**
 * Utility class containing functions used by multiple array functions.
 */
class Utils {
	/**
	 * Exports the given value.
	 *
	 * @param array|int|float|string|bool $value The value to export
	 * @return string The resulting exported string
	 */
	public static function export( $value ): string {
		if ( is_string( $value ) ) {
			// Strings are the default type
			return $value;
		}

		$type = gettype( $value );

		if ( $type === "double" ) {
			$type = "float";
		}

		if ( $type === "array" ) {
			$value = base64_encode( FormatJson::encode( $value ) );
		}

		if ( $type === "boolean" ) {
			$value = $value ? 1 : 0;
		}

		return $type . '__^__' . $value;
	}

	/**
	 * Imports the given string and converts it to the correct type when appropriate.
	 *
	 * @param string $input The value to import
	 * @return array|int|string|bool|float The parsed value
	 */
	public static function import( string $input ) {
		if ( strpos( $input, '__^__' ) === false ) {
			$type = "string";
			$value = $input;
		} else {
			list( $type, $value ) = explode( '__^__', $input, 2 );
		}

		// Handle any non-string type
		switch ( $type ) {
			case "boolean":
				$result = filter_var( $value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE );

				if ( $result !== null ) {
					return $result;
				}

				break;
			case "float":
				$result = filter_var( $value, FILTER_VALIDATE_FLOAT, FILTER_NULL_ON_FAILURE );

				if ( $result !== null ) {
					return $result;
				}

				break;
			case "integer":
				$result = filter_var( $value, FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE );

				if ( $result !== null ) {
					return $result;
				}

				break;
			case "array":
				// Try decoding and parsing to see if it was a base64 encoded JSON string
				$maybeJson = base64_decode( $value );

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
				return $value;
		}

		// Default to interpreting the entire input as a string
		return $input;
	}

	/**
	 * Stringifies the given value.
	 *
	 * @param string|float|bool|int $value
	 * @return string
	 */
	public static function stringify( $value ): string {
		if ( is_bool( $value ) ) {
			return $value ? "true" : "false";
		}

		return sprintf( "%s", $value );
	}

	/**
	 * Returns an error.
	 *
	 * @param string $function The name of the parser function that caused the error
	 * @param string|Message $message A message key
	 * @param array $args The arguments to pass to the message
	 * @return Message
	 */
	public static function error( string $function, $message, array $args = [] ): Message {
		if ( !$message instanceof Message ) {
			$message = wfMessage( $message );
		}

		$message->params( $args );

		return wfMessage( 'af-error', $function, $message );
	}
}
