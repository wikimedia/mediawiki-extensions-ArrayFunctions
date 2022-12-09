<?php

namespace ArrayFunctions\ArrayFunctions;

use ArrayFunctions\Exceptions\RuntimeException;
use ArrayFunctions\Utils;

/**
 * Implements the #af_object parser function.
 */
class AFObject extends ArrayFunction {
	/**
	 * @inheritDoc
	 */
	public static function getName(): string {
		return 'af_object';
	}

	/**
	 * @inheritDoc
	 * @throws RuntimeException
	 */
	public function execute( ...$values ): array {
		$buffer = [];

		foreach ( $values as $value ) {
			if ( !is_string( $value ) || strpos( $value, '=' ) === false ) {
				$buffer[] = $value;
			} else {
				list( $key, $value ) = explode( '=', $value, 2 );

				$importedKey = Utils::import( $key );

				if ( !is_string( $importedKey ) && !is_int( $importedKey ) ) {
					throw new RuntimeException( wfMessage( "af-error-invalid-key", gettype( $importedKey ), $importedKey ) );
				}

				$buffer[$importedKey] = Utils::import( $value );
			}
		}

		return [ $buffer ];
	}
}
