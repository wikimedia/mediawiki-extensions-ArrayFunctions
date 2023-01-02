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
	public static function getName(): string {
		return 'af_foreach';
	}

	/**
	 * @inheritDoc
	 */
	public function execute( array $array, ?string $keyName = null, ?string $valueName = null, ?PPNode $body = null ): array {
		if ( $body === null ) {
			return [''];
		}

		$result = '';

		foreach ( $array as $key => $value ) {
			$args = $this->getFrame()->getArguments();

			if ( $keyName !== null ) {
				$args[$keyName] = $key;
			}

			if ( $valueName !== null ) {
				$args[$valueName] = Utils::export( $value );
			}

			$nodeArray = $this->getParser()->getPreprocessor()->newPartNodeArray( $args );
			$childFrame = $this->getFrame()->newChild( $nodeArray, $this->getFrame()->getTitle() );

			$result .= trim( $childFrame->expand( $body ) );
		}

		return [$result];
	}
}
