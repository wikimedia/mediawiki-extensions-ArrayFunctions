<?php

namespace ArrayFunctions;

use Config;
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
	 * @var Config
	 */
	private Config $config;

	/**
	 * @param ArrayFunctionFactory $factory
	 * @param ArrayFunctionRegistry $registry
	 */
	public function __construct( ArrayFunctionFactory $factory, ArrayFunctionRegistry $registry, Config $config ) {
		$this->factory = $factory;
		$this->registry = $registry;
		$this->config = $config;
	}

	/**
	 * @inheritDoc
	 * @throws MWException
	 */
	public function onParserFirstCallInit( $parser ): void {
		$enableErrorTracking = $this->config->get( 'ArrayFunctionsEnableErrorTracking' );

		foreach ( $this->registry->getFunctions() as $class ) {
			$parser->setFunctionHook(
				$class::getName(),
				[ new ArrayFunctionInvoker( $class, $enableErrorTracking, $this->factory ), "invoke" ],
				Parser::SFH_OBJECT_ARGS
			);

			foreach ( $class::getAliases() as $alias ) {
				$parser->setFunctionHook(
					$alias,
					[ new ArrayFunctionInvoker( $class, $enableErrorTracking, $this->factory ), "invoke" ],
					Parser::SFH_OBJECT_ARGS
				);
			}
		}
	}
}
