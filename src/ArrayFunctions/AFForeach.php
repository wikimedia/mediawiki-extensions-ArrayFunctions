<?php

namespace ArrayFunctions\ArrayFunctions;

use ArrayFunctions\Utils;
use PPNode;

/**
 * Implements the #af_foreach parser function.
 */
class AFForeach extends ArrayFunction {
	/**
	 * @inheritDoc
	 */
	public function execute( array $array, string $keyName, string $valueName, PPNode $body ): array {
		$result = '';

		foreach ( $array as $key => $value ) {
			$childArgs = array_merge( $this->getFrame()->getArguments(), [ $keyName => $key, $valueName => Utils::export( $value ) ] );
			$childFrame = $this->getFrame()->newChild( $this->getParser()->getPreprocessor()->newPartNodeArray( $childArgs ), $this->getFrame()->getTitle() );

			$result .= trim( $childFrame->expand( $body ) );
		}

		return [ $result ];
	}
}
