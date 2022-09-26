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
			'export' => [ Utils::class, 'export' ]
		];

		$this->getEngine()->registerInterface( __DIR__ . '/' . 'mw.af.lua', $interfaceFuncs, [] );
	}
}
