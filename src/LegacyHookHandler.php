<?php

namespace ArrayFunctions;

use ArrayFunctions\Cargo\ArrayFunctionsFormat;
use ArrayFunctions\Scribunto\ArrayFunctionsLuaLibrary;
use ArrayFunctions\SemanticMediaWiki\ArrayFunctionsResultPrinter;

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
	 * @param array &$extraLibraries
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

	/**
	 * Allow extensions to add Cargo result formats.
	 *
	 * @link https://www.mediawiki.org/wiki/Extension:Cargo/Hooks/CargoSetFormatClasses
	 *
	 * @param array &$formatClasses
	 * @return bool
	 */
	public static function onCargoSetFormatClasses( array &$formatClasses ): bool {
		$formatClasses['arrayfunctions'] = ArrayFunctionsFormat::class;

		return true;
	}

	/**
	 * Called after the extension has been registered.
	 *
	 * @link https://www.mediawiki.org/wiki/Manual:Extension.json/Schema#callback
	 *
	 * @return bool
	 */
	public static function onExtensionRegistration(): bool {
		define( 'MAG_AF_EMPTY', 'MAG_AF_EMPTY' );

		self::registerArrayFunctionsResultPrinter();

		return true;
	}

	/**
	 * Registers the `arrayfunctions` Semantic MediaWiki result printer.
	 *
	 * @return void
	 */
	private static function registerArrayFunctionsResultPrinter() {
		$GLOBALS['smwgResultFormats']['arrayfunctions'] = ArrayFunctionsResultPrinter::class;
	}
}
