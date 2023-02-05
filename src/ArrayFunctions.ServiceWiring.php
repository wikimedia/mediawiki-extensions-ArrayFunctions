<?php

/**
 * This file is loaded by MediaWiki\MediaWikiServices::getInstance() during the
 * bootstrapping of the dependency injection framework.
 *
 * @file
 */
use ArrayFunctions\ArrayFunctionRegistry;

// PHP unit does not understand code coverage for this file
// as the @covers annotation cannot cover a specific file
// This is fully tested in ServiceWiringTest.php
// @codeCoverageIgnoreStart

return [
	"ArrayFunctions.ArrayFunctionRegistry" => static function (): ArrayFunctionRegistry {
		return new ArrayFunctionRegistry();
	}
];

// @codeCoverageIgnoreEnd
