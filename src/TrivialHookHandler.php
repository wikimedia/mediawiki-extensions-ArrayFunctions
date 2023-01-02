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
		$variableIDs['af_empty'] = MAG_AF_EMPTY;
		$variableIDs['af_params'] = MAG_AF_PARAMS;
	}

	/**
	 * @inheritDoc
	 */
	public function onParserGetVariableValueSwitch( $parser, &$variableCache, $magicWordId, &$ret, $frame ) {
		if ( $magicWordId === MAG_AF_EMPTY ) {
			$ret = Utils::export( [] );
			$variableCache[$magicWordId] = $ret;
		} elseif ( $magicWordId === MAG_AF_PARAMS ) {
			$args = $frame->getArguments();
			$ret = Utils::export( $args );
		}

		return true;
	}
}
