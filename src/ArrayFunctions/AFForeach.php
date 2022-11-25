<?php

namespace ArrayFunctions\ArrayFunctions;

use ArrayFunctions\Exceptions\ImportException;
use ArrayFunctions\Utils;
use Parser;
use PPFrame;

/**
 * Implements the #af_foreach parser function.
 */
class AFForeach implements ArrayFunction {
	/**
	 * @inheritDoc
	 */
	public function execute( Parser $parser, PPFrame $frame, array $args ): array {
		try {
			$array = Utils::import( Utils::expandNode( $args[0], $frame ) );
		} catch ( ImportException $exception ) {
			return $exception->getWikitextError( 'af_foreach', [ '1' ] );
		}

		if ( !is_array( $array ) ) {
			return [ Utils::error( 'af_foreach', 'af-error-incorrect-type-expected-array', [ '1', gettype( $array ) ] ), 'noparse' => false ];
		}

		if ( count( $args ) !== 4 ) {
			return [ Utils::error( 'af_foreach', 'af-error-incorrect-argument-count-exact', [ '4', count( $args ) ] ), 'noparse' => false ];
		}

		$keyName = Utils::expandNode( $args[1], $frame, PPFrame::RECOVER_ORIG );
		$valueName = Utils::expandNode( $args[2], $frame, PPFrame::RECOVER_ORIG );
		$bodyNode = $args[3];

		$result = '';

		foreach ( $array as $key => $value ) {
			$childArgs = array_merge( $frame->getArguments(), [ $keyName => $key, $valueName => Utils::export( $value ) ] );
			$childFrame = $frame->newChild( $parser->getPreprocessor()->newPartNodeArray( $childArgs ), $frame->getTitle() );

			$result .= trim( $childFrame->expand( $bodyNode ) );
		}

		return [ $result ];
	}
}
