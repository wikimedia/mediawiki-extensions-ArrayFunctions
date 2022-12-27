<?php

namespace ArrayFunctions\ArrayFunctions;

use PPNode;

/**
 * Implements the #af_map parser function.
 */
class AFMap extends ArrayFunction {
	/**
	 * @inheritDoc
	 */
	public static function getName(): string {
		return 'af_map';
	}

	/**
	 * @inheritDoc
	 */
	public function execute( array $array, string $valueName, PPNode $callback ): array {
		return [array_map( function ( $value ) use ( $valueName, $callback ) {
			$args = $this->getFrame()->getArguments();
			$args[$valueName] = $value;

			$nodeArray = $this->getParser()->getPreprocessor()->newPartNodeArray( $args );
			$childFrame = $this->getFrame()->newChild( $nodeArray, $this->getFrame()->getTitle() );

			return trim( $childFrame->expand( $callback ) );
		}, $array )];
	}
}
