<?php

namespace ArrayFunctions;

use MediaWiki\Hook\ParserFirstCallInitHook;
use MWException;
use Parser;

/**
 * Hook handler for the ParserFirstCallInit hook. Responsible for registering the array functions with the parser.
 */
class ParserInitHookHandler implements ParserFirstCallInitHook {
	private ArrayFunctionRegistry $registry;

	public function __construct( ArrayFunctionRegistry $registry ) {
		$this->registry = $registry;
	}

	/**
	 * @inheritDoc
	 * @throws MWException
	 */
	public function onParserFirstCallInit( $parser ): void {
		foreach ( $this->registry->getFunctions() as $name => $instance ) {
			$parser->setFunctionHook( $name, [$instance, "execute"], Parser::SFH_OBJECT_ARGS );
		}
	}
}
