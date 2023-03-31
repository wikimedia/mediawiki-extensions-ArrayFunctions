<?php

/**
 * This file is loaded by MediaWiki\MediaWikiServices::getInstance() during the
 * bootstrapping of the dependency injection framework.
 *
 * @file
 */

use ArrayFunctions\ArrayEnvironment;
use ArrayFunctions\ArrayFunctionRegistry;
use MediaWiki\MediaWikiServices;

// PHP unit does not understand code coverage for this file
// as the @covers annotation cannot cover a specific file
// This is fully tested in ServiceWiringTest.php
// @codeCoverageIgnoreStart

return [
	"ArrayFunctions.ArrayEnvironment" => static function ( MediaWikiServices $services ): ArrayEnvironment {
		return new ArrayEnvironment( $services->getMainConfig()->get( 'ArrayFunctionsMaxReferenceCount' ) );
	},
	"ArrayFunctions.ArrayFunctionRegistry" => static function (): ArrayFunctionRegistry {
		return new ArrayFunctionRegistry();
	}
];

// @codeCoverageIgnoreEnd
