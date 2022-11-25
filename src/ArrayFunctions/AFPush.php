<?php

namespace ArrayFunctions\ArrayFunctions;

use ArrayFunctions\Exceptions\ImportException;
use ArrayFunctions\Utils;
use MWException;
use Parser;
use PPFrame;

/**
 * Implements the #af_push parser function.
 */
class AFPush implements ArrayFunction {
	/**
	 * @inheritDoc
	 * @throws MWException
	 */
	public function execute( Parser $parser, PPFrame $frame, array $args ): array {
		try {
			$value = Utils::import( Utils::expandNode( $args[0], $frame ) );
		} catch ( ImportException $exception ) {
			return $exception->getWikitextError( 'af_push', [ '1' ] );
		}

		if ( !is_array( $value ) ) {
			return [ Utils::error( 'af_push', 'af-error-incorrect-type-expected-array', [ '1', gettype( $array ) ] ), 'noparse' => false ];
		}

		if ( count( $args ) !== 2 ) {
			return [ Utils::error( 'af_push', 'af-error-incorrect-argument-count-exact', [ '2', count( $args ) ] ), 'noparse' => false ];
		}

		try {
			$value[] = Utils::import( Utils::expandNode( $args[1], $frame ) );
		} catch ( ImportException $exception ) {
			return $exception->getWikitextError( 'af_push', [ '2' ] );
		}

		return [ Utils::export( $value ) ];
	}
}
