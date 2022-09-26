<?php

/**
 * This file is loaded by MediaWiki\MediaWikiServices::getInstance() during the
 * bootstrapping of the dependency injection framework.
 *
 * @file
 */

use ArrayFunctions\ArrayFunctionRegistry;

return [
	"ArrayFunctions.ArrayFunctionRegistry" => static function (): ArrayFunctionRegistry {
		return new ArrayFunctionRegistry();
	}
];
