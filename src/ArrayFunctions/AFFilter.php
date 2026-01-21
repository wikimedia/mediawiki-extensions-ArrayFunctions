<?php

namespace ArrayFunctions\ArrayFunctions;

use ArrayFunctions\Exceptions\RuntimeException;
use ArrayFunctions\Utils;
use PPNode;

/**
 * Implements the #af_filter parser function.
 */
class AFFilter extends ArrayFunction {
	/**
	 * @inheritDoc
	 */
	public static function getName(): string {
		return 'af_filter';
	}

	/**
	 * @inheritDoc
	 */
	public function execute( array $array, ?string $valueName = null, ?PPNode $callback = null ): array {
		if ( $callback && !$valueName ) {
			throw new RuntimeException(
				Utils::createMessageArray( 'af-error-value-name-not-specified' )
			);
		}

		return [ array_filter( $array, function ( $value ) use ( $valueName, $callback ) {
			if ( !$callback ) {
				return !$this->isEmpty( $value );
			}

			$args = $this->getFrame()->getArguments();
			$args[$valueName] = Utils::export( $value );

			$nodeArray = $this->getParser()->getPreprocessor()->newPartNodeArray( $args );
			$childFrame = $this->getFrame()->newChild( $nodeArray, $this->getFrame()->getTitle() );
			$expandedFrame = trim( $childFrame->expand( $callback ) );

			return $expandedFrame !== ''
				&& filter_var( $expandedFrame, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE ) !== false
				&& Utils::import( $expandedFrame ) !== false;
		} ) ];
	}

	/**
	 * Returns true if and only if the value is considered empty.
	 *
	 * @param mixed $value
	 * @return bool
	 */
	private function isEmpty( $value ): bool {
		return $value === null || ( is_string( $value ) && trim( $value ) === '' );
	}
}
