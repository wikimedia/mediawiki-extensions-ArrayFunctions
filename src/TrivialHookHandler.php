<?php

namespace ArrayFunctions;

use MediaWiki\Hook\GetMagicVariableIDsHook;
use MediaWiki\Hook\ParserGetVariableValueSwitchHook;

/**
 * Hook handler for a number of hooks that do not have any dependencies.
 */
class TrivialHookHandler implements GetMagicVariableIDsHook, ParserGetVariableValueSwitchHook {
	/**
	 * @inheritDoc
	 */
	public function onGetMagicVariableIDs( &$variableIDs ) {
		$variableIDs[] = MAG_AF_EMPTY;
	}

	/**
	 * @inheritDoc
	 */
	public function onParserGetVariableValueSwitch( $parser, &$variableCache, $magicWordId, &$ret, $frame ) {
		if ( $magicWordId === MAG_AF_EMPTY ) {
			$parser->addTrackingCategory( "af-tracking-category" );

			$ret = Utils::export( [] );
			$variableCache[$magicWordId] = $ret;
		}

		return true;
	}
}
