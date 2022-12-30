<?php

namespace ArrayFunctions\Tests\PHPUnit\Unit\ArrayFunctions;

use ArrayFunctions\ArrayFunctions\AFBool;
use ArrayFunctions\ArrayFunctions\AFCount;
use MediaWikiUnitTestCase;
use Parser;
use PPFrame;

/**
 * @group ArrayFunctions
 * @covers \ArrayFunctions\ArrayFunctions\AFCount
 */
class AFCountTest extends MediaWikiUnitTestCase {
	protected function setUp(): void {
		parent::setUp();
		$frame = $this->createStub( PPFrame::class );
		$parser = $this->createStub( Parser::class );

		$this->function = new AFCount( $parser, $frame );
		$this->function->setKeywordArgs( [
			'recursive' => false
		] );
	}

	/**
	 * @dataProvider provideCountOneDimensionalData
	 */
	public function testCountOneDimensional( array $array, int $expected ): void {
		$actual = $this->function->execute( $array );

		$this->assertSame( [ $expected ], $actual );
	}

	/**
	 * @dataProvider provideCountMultiDimensionalData
	 */
	public function testCountMultiDimensional( array $array, int $expected ): void {
		$actual = $this->function->execute( $array );

		$this->assertSame( [ $expected ], $actual );
	}

	/**
	 * @dataProvider provideCountRecursiveData
	 */
	public function testCountRecursive( array $array, int $expected ): void {
		$this->function->setKeywordArgs( [ 'recursive' => true ] );
		$actual = $this->function->execute( $array );

		$this->assertSame( [ $expected ], $actual );
	}

	public function provideCountOneDimensionalData(): array {
		return [
			[[], 0],
			[['a'], 1],
			[['a', 'b', 'c', 'd'], 4],
			[[0, 0, 0], 3]
		];
	}

	public function provideCountMultiDimensionalData(): array {
		return [
			[[], 0],
			[[[]], 1],
			[['a', ['b', 'c']], 2],
		];
	}

	public function provideCountRecursiveData(): array {
		return [
			[[], 0],
			[[[]], 1],
			[['a', ['b', 'c']], 4],
			[['a', ['b'], ['c']], 5]
		];
	}
}
