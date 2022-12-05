<?php

namespace ArrayFunctions\ArrayFunctions;

/**
 * Implements the #af_push parser function.
 */
class AFPush extends ArrayFunction {
	/**
	 * @inheritDoc
	 */
	public static function getName(): string {
		return 'af_push';
	}

	/**
	 * @inheritDoc
	 */
	public function execute( array $array, $value ): array {
		$array[] = $value;
		return [ $array ];
	}
}
