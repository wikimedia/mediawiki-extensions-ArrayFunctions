<?php

namespace ArrayFunctions;

use ArrayFunctions\Scribunto\ArrayFunctionsLuaLibrary;

/**
 * Responsible for handling legacy hooks that do not yet implement a hook interface.
 */
class LegacyHookHandler {
	/**
	 * Allow extensions to add libraries to Scribunto.
	 *
	 * @link https://www.mediawiki.org/wiki/Extension:Scribunto/Hooks/ScribuntoExternalLibraries
	 *
	 * @param string $engine
	 * @param array $extraLibraries
	 * @return bool
	 */
	public static function onScribuntoExternalLibraries( string $engine, array &$extraLibraries ): bool {
		if ( $engine !== 'lua' ) {
			// Don't mess with other engines
			return true;
		}

		$extraLibraries['af'] = ArrayFunctionsLuaLibrary::class;

		return true;
	}

}
