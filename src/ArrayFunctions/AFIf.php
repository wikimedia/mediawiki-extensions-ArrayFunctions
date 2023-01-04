<?php

namespace ArrayFunctions\ArrayFunctions;

use ArrayFunctions\Utils;
use PPNode;

/**
 * Implements the #af_if parser function.
 */
class AFIf extends ArrayFunction {
	/**
	 * @inheritDoc
	 */
	public static function getName(): string {
		return 'af_if';
	}

	/**
	 * @inheritDoc
	 */
	public function execute( bool $predicate, PPNode $consequent, ?PPNode $alternative = null ): array {
		$alternative = $alternative ?? "";
		$result = $predicate ?
			Utils::import( trim( $this->getFrame()->expand( $consequent ) ) ) :
			Utils::import( trim( $this->getFrame()->expand( $alternative ) ) );

		return [ $result ];
	}
}
