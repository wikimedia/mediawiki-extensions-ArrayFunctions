<?php

namespace ArrayFunctions\ArrayFunctions;

use ArrayFunctions\Utils;
use Parser;
use PPFrame;

/**
 * Implements the #af_push parser function.
 */
class AFPush implements ArrayFunction {
	/**
	 * @inheritDoc
	 */
	public function execute( Parser $parser, PPFrame $frame, array $args ): array {
		$array = Utils::import( Utils::expandNode( $args[0], $frame ) );

		if ( !is_array( $array ) ) {
			return [Utils::error( 'af_push', 'af-error-incorrect-type-expected-array', ['1', gettype( $array )] ), 'noparse' => false];
		}

		if ( count( $args ) !== 2 ) {
			return [Utils::error( 'af_push', 'af-error-incorrect-argument-count-exact', ['2', count( $args )] ), 'noparse' => false];
		}

		$array[] = Utils::import( Utils::expandNode( $args[1], $frame ) );

		return [Utils::export( $array )];
	}
}
