<?php

/**
 * Copy of CentralAuth's CentralAuthServiceWiringTest.php
 * used to test the ArrayFunctions.ServiceWiring.php file.
 */

namespace ArrayFunctions\Tests;

use MediaWiki\MediaWikiServices;
use MediaWikiIntegrationTestCase;

/**
 * Tests ArrayFunctions.ServiceWiring.php
 *
 * @coversNothing PHPUnit does not support covering annotations for files
 * @group ArrayFunctions
 */
class ServiceWiringTest extends MediaWikiIntegrationTestCase {
	/**
	 * @dataProvider provideService
	 */
	public function testService( string $name ) {
		MediaWikiServices::getInstance()->get( $name );
		$this->addToAssertionCount( 1 );
	}

	public static function provideService() {
		$wiring = require __DIR__ . '/../../src/ArrayFunctions.ServiceWiring.php';
		foreach ( $wiring as $name => $_ ) {
			yield $name => [ $name ];
		}
	}
}
