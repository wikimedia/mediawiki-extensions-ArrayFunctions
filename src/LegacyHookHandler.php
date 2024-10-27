<?php

namespace ArrayFunctions;

/**
 * Responsible for handling legacy hooks that do not yet implement a hook interface.
 */
class LegacyHookHandler {
	/**
	 * Called after the extension has been registered.
	 *
	 * @link https://www.mediawiki.org/wiki/Manual:Extension.json/Schema#callback
	 *
	 * @return bool
	 */
	public static function onExtensionRegistration(): bool {
		define( 'MAG_AF_EMPTY', 'MAG_AF_EMPTY' );

		return true;
	}
}
