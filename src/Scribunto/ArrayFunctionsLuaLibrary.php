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
	 * Exports the given value.
	 *
	 * @param array|int|float|string|bool $value
	 * @return array
	 */
	public function export( $value ): array {
		return [ Utils::export( $value ) ];
	}
}
