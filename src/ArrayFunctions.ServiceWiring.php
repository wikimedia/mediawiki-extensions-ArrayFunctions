<?php

/**
 * This file is loaded by MediaWiki\MediaWikiServices::getInstance() during the
 * bootstrapping of the dependency injection framework.
 *
 * @file
 */

use ArrayFunctions\ArrayFunctionFactory;
use ArrayFunctions\ArrayFunctionRegistry;
use ArrayFunctions\FormatterFactory;
use MediaWiki\MediaWikiServices;

// PHP unit does not understand code coverage for this file
// as the @covers annotation cannot cover a specific file
// This is fully tested in ServiceWiringTest.php
// @codeCoverageIgnoreStart

return [
	"ArrayFunctions.ArrayFunctionFactory" => static function ( MediaWikiServices $services ): ArrayFunctionFactory {
		return new ArrayFunctionFactory( $services->getMainConfig() );
	},
	"ArrayFunctions.ArrayFunctionRegistry" => static function (): ArrayFunctionRegistry {
		return new ArrayFunctionRegistry();
	},
	"ArrayFunctions.FormatterFactory" => static function (): FormatterFactory {
		return new FormatterFactory();
	},
];

// @codeCoverageIgnoreEnd
