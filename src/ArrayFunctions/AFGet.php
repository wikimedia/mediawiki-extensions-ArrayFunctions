<?php

namespace ArrayFunctions\ArrayFunctions;

use ArrayFunctions\Utils;
use Parser;
use PPFrame;

/**
 * Implements the #af_get parser function.
 */
class AFGet implements ArrayFunction {
	/**
	 * @inheritDoc
	 */
	public function execute( Parser $parser, PPFrame $frame, array $args ): array {
		$value = Utils::import( Utils::expandNode( $args[0], $frame ) );

		foreach ( array_slice( $args, 1 ) as $key ) {
			$key = Utils::expandNode( $key, $frame );

			if ( !is_array( $value ) || !isset( $value[$key] ) ) {
				return [''];
			}

			$value = $value[$key];
		}

		return [Utils::export( $value )];
	}
}
