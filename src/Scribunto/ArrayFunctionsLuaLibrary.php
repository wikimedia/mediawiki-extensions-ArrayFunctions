<?php

namespace ArrayFunctions\Scribunto;

use ArrayFunctions\Utils;
use Scribunto_LuaLibraryBase;

class ArrayFunctionsLuaLibrary extends Scribunto_LuaLibraryBase {
	/**
	 * @inheritDoc
	 */
	public function register(): void {
		$interfaceFuncs = [
			'export' => [ $this, 'export' ]
		];

		$this->getEngine()->registerInterface( __DIR__ . '/' . 'mw.af.lua', $interfaceFuncs, [] );
	}

	/**
	 * Exports the given table as base64 encoded JSON.
	 *
	 * @param array $table
	 * @return array
	 */
	public function export( array $table ): array {
		return [Utils::export( $table )];
	}
}
