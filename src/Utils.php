<?php

namespace ArrayFunctions;

use FormatJson;
use Message;
use PPFrame;
use PPNode;

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

		return $type . '__^__' . $value;
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
