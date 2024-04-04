<?php

namespace ArrayFunctions\ArrayFunctions;

use ArrayFunctions\Utils;
use PPNode;

/**
 * Implements the #af_reduce parser function.
 */
class AFReduce extends ArrayFunction {
	/**
	 * @inheritDoc
	 */
	public static function getName(): string {
		return 'af_reduce';
	}

	/**
	 * @inheritDoc
	 */
	public function execute(
		array $array,
		string $carryName,
		string $itemName,
		?PPNode $callable = null,
		?PPNode $initial = null
	): array {
		if ( $callable === null ) {
			return [ '' ];
		}

		if ( $initial !== null ) {
			$initial = trim( $this->getFrame()->expand( $initial ) );
		} else {
			$initial = '';
		}

		$args = $this->getFrame()->getArguments();

		$result = array_reduce(
			$array,
			function ( string $carry, $item ) use ( $carryName, $itemName, $args, $callable ) {
				$args[$carryName] = $carry;
				$args[$itemName] = Utils::export( $item );

				$nodeArray = $this->getParser()->getPreprocessor()->newPartNodeArray( $args );
				$childFrame = $this->getFrame()->newChild( $nodeArray, $this->getFrame()->getTitle() );

				return trim( $childFrame->expand( $callable ) );
			},
			$initial
		);

		return [ $result ];
	}
}
