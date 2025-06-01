<?php

namespace ArrayFunctions\ArrayFunctions\Formatters;

use ArrayFunctions\Utils;

class SimpleFormatter implements Formatter {
	/**
	 * @inheritDoc
	 */
	public function format( $value ): ?string {
		switch ( true ) {
			case is_string( $value ):
			case is_int( $value ):
			case is_float( $value ):
			case is_bool( $value ):
				return Utils::stringify( $value );
			default:
				return null;
		}
	}
}
