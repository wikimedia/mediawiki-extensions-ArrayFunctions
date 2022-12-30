<?php

namespace ArrayFunctions\Tests\PHPUnit\Unit\ArrayFunctions;

use ArrayFunctions\ArrayFunctions\AFBool;
use MediaWikiUnitTestCase;
use Parser;
use PPFrame;

/**
 * @group ArrayFunctions
 * @covers \ArrayFunctions\ArrayFunctions\AFBool
 */
class AFBoolTest extends MediaWikiUnitTestCase {
	protected function setUp(): void {
		parent::setUp();
		$frame = $this->createStub( PPFrame::class );
		$parser = $this->createStub( Parser::class );

		$this->function = new AFBool( $parser, $frame );
	}

	public function testTrue(): void {
		$actual = $this->function->execute( true );

		$this->assertSame( [ true ], $actual );
	}

	public function testFalse(): void {
		$actual = $this->function->execute( false );

		$this->assertSame( [ false ], $actual );
	}
}
