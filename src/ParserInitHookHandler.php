<?php

namespace ArrayFunctions;

use MediaWiki\Hook\ParserFirstCallInitHook;
use MWException;
use Parser;

/**
 * Hook handler for the ParserFirstCallInit hook. Responsible for registering the array functions with the parser.
 */
class ParserInitHookHandler implements ParserFirstCallInitHook {
	/**
	 * @var ArrayFunctionFactory
	 */
	private ArrayFunctionFactory $factory;

	/**
	 * @var ArrayFunctionRegistry
	 */
	private ArrayFunctionRegistry $registry;

	/**
	 * @param ArrayFunctionFactory $factory
	 * @param ArrayFunctionRegistry $registry
	 */
	public function __construct( ArrayFunctionFactory $factory, ArrayFunctionRegistry $registry ) {
		$this->factory = $factory;
		$this->registry = $registry;
	}

	/**
	 * @inheritDoc
	 * @throws MWException
	 */
	public function onParserFirstCallInit( $parser ): void {
		foreach ( $this->registry->getFunctions() as $class ) {
			$parser->setFunctionHook(
				$class::getName(),
				[ new ArrayFunctionInvoker( $class, $this->factory ), "invoke" ],
				Parser::SFH_OBJECT_ARGS
			);

			foreach ( $class::getAliases() as $alias ) {
				$parser->setFunctionHook(
					$alias,
					[ new ArrayFunctionInvoker( $class, $this->factory ), "invoke" ],
					Parser::SFH_OBJECT_ARGS
				);
			}
		}
	}
}
