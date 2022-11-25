<?php

namespace ArrayFunctions\Tests\PHPUnit\Unit;

use ArrayFunctions\Utils;
use MediaWikiUnitTestCase;

/**
 * @covers \ArrayFunctions\Utils
 */
class UtilsTest extends MediaWikiUnitTestCase {
	/**
	 * @dataProvider provideExportIsInverseOfImportData
	 */
	public function testExportIsInverseOfImport( string $value ): void {
		$this->assertSame( $value, Utils::export( Utils::import( $value ) ) );
	}

	/**
	 * @dataProvider provideImportIsInverseOfExportData
	 */
	public function testImportIsInverseOfExport( $value ): void {
		$this->assertSame( $value, Utils::import( Utils::export( $value ) ) );
	}

	/**
	 * @return string[][]
	 */
	public function provideExportIsInverseOfImportData(): array {
		return [
			['float__^__10'],
			['float__^__1'],
			['float__^__0'],
			['float__^__1.21'],
			['float__^__10'],
			['integer__^__10'],
			['integer__^__0'],
			['string__^__hello world'],
			['string__^__hello'],
			['string__^__'],
			['string__^__ '],
			['array__^__WyJmb28iXQ==']
		];
	}

	/**
	 * @return array[]
	 */
	public function provideImportIsInverseOfExportData(): array {
		return [
			[[]],
			[['foo']],
			[['foo' => 'bar']],
			[[[]]],
			['foo'],
			['bar'],
			[''],
			['0'],
			[0],
			[10],
			[-103],
			[10.239],
			[0.1],
			[0.0],
			['[]'],
			['["foo"]'],
			['{}'],
			['{"foo"}'],
			['{"foo": "bar"}']
		];
	}
}
