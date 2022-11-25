<?php

namespace ArrayFunctions\ArrayFunctions;

use ArrayFunctions\Exceptions\ImportException;
use ArrayFunctions\Utils;
use Parser;
use PPFrame;

/**
 * Implements the #af_isarray parser function.
 */
class AFIsarray implements ArrayFunction {
	/**
	 * @inheritDoc
	 */
	public function execute( Parser $parser, PPFrame $frame, array $args ): array {
		if ( count( $args ) < 2 ) {
			return [ Utils::error( 'af_isarray', 'af-error-incorrect-argument-count-at-least', [ '2', count( $args ) ] ) ];
		}

		if ( count( $args ) > 3 ) {
			return [ Utils::error( 'af_isarray', 'af-error-incorrect-argument-count-at-least', [ '3', count( $args ) ] ) ];
		}

		try {
			$value = Utils::import( Utils::expandNode( $args[0], $frame ) );
		} catch ( ImportException $exception ) {
			// If it is not valid input, it is definitely not a valid array
			return isset( $args[2] ) ? [ Utils::expandNode( $args[2], $frame ) ] : [ '' ];
		}

		if ( is_array( $value ) ) {
			return [ Utils::expandNode( $args[1], $frame ) ];
		} elseif ( isset( $args[2] ) ) {
			return [ Utils::expandNode( $args[2], $frame ) ];
		} else {
			return [ '' ];
		}
	}
}
