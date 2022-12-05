<?php

namespace ArrayFunctions\ArrayFunctions;

use PPNode;

/**
 * Implements the #af_isarray parser function.
 */
class AFIsarray extends ArrayFunction {
	/**
	 * @inheritDoc
	 */
	public static function getName(): string {
		return 'af_isarray';
	}

	/**
	 * @inheritDoc
	 */
	public function execute( $value, PPNode $consequent, ?PPNode $antecedent = null ): array {
		if ( is_array( $value ) ) {
			return [ trim( $this->getFrame()->expand( $consequent ) ) ];
		} elseif ( isset( $antecedent ) ) {
			return [ trim( $this->getFrame()->expand( $antecedent ) ) ];
		} else {
			return [ '' ];
		}
	}
}
