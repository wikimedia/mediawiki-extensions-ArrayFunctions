<?php

namespace ArrayFunctions\ArrayFunctions;

use ArrayFunctions\Exceptions\ImportException;
use ArrayFunctions\Utils;
use MWException;
use Parser;
use PPFrame;

/**
 * Implements the #af_get parser function.
 */
class AFGet extends ArrayFunction {
	/**
	 * @inheritDoc
	 * @throws MWException
	 */
	public function execute( Parser $parser, PPFrame $frame, array $args ): array {
		try {
			$value = Utils::import( Utils::expandNode( $args[0], $frame ) );
		} catch ( ImportException $exception ) {
			return $exception->getWikitextError( 'af_get', [ '1' ] );
		}

		foreach ( array_slice( $args, 1 ) as $key ) {
			$key = Utils::expandNode( $key, $frame );

			if ( !is_array( $value ) || !isset( $value[$key] ) ) {
				return [ '' ];
			}

			$value = $value[$key];
		}

		return [ Utils::export( $value ) ];
	}
}
