<?php

namespace ArrayFunctions\ArrayFunctions;

use ArrayFunctions\Exceptions\RuntimeException;
use ArrayFunctions\Utils;

/**
 * Implements the #af_show parser function.
 */
class AFShow extends ArrayFunction {
	/**
	 * @inheritDoc
	 */
	public static function getName(): string {
		return 'af_show';
	}

	/**
	 * @inheritDoc
	 * @throws RuntimeException
	 */
	public function execute( $value ): array {
		if ( $value === null ) {
			$value = '';
		}

		if ( !$this->isShowable( $value ) ) {
			throw new RuntimeException( wfMessage( 'af-error-value-not-showable', Utils::export( $value ) ) );
		}

		return [ Utils::stringify( $value ) ];
	}

	/**
	 * Returns true if and only if the value is showable.
	 *
	 * @param mixed $value
	 * @return bool
	 */
	private function isShowable( $value ): bool {
		switch ( true ) {
			case is_string( $value ):
			case is_int( $value ):
			case is_float( $value ):
			case is_bool( $value ):
				return true;
			default:
				return false;
		}
	}
}
