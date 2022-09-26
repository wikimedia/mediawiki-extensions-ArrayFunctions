<?php

namespace ArrayFunctions\ArrayFunctions;

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
			return [Utils::error( 'af_print', 'af-error-incorrect-argument-count-at-least', ['2', count( $args )] )];
		}

		if ( count( $args ) > 3 ) {
			return [Utils::error( 'af_print', 'af-error-incorrect-argument-count-at-least', ['3', count( $args )] )];
		}

		$value = Utils::import( Utils::expandNode( $args[0], $frame ) );

		if ( is_array( $value ) ) {
			return [Utils::expandNode( $args[1], $frame )];
		} elseif ( isset( $args[2] ) ) {
			return [Utils::expandNode( $args[2], $frame )];
		}

		return [''];
	}
}
