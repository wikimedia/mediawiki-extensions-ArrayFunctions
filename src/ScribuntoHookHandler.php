<?php

namespace ArrayFunctions;

use ArrayFunctions\Scribunto\ArrayFunctionsLuaLibrary;

class ScribuntoHookHandler implements
	\MediaWiki\Extension\Scribunto\Hooks\ScribuntoExternalLibrariesHook
{
	/**
	 * Allow extensions to add libraries to Scribunto.
	 *
	 * @link https://www.mediawiki.org/wiki/Extension:Scribunto/Hooks/ScribuntoExternalLibraries
	 *
	 * @param string $engine
	 * @param array &$extraLibraries
	 * @return bool
	 */
	public function onScribuntoExternalLibraries( string $engine, array &$extraLibraries ) {
		if ( $engine !== 'lua' ) {
			// Don't mess with other engines
			return true;
		}

		$extraLibraries['af'] = ArrayFunctionsLuaLibrary::class;

		return true;
	}
}
