<?php

namespace ArrayFunctions\ArrayFunctions;

use MWException;
use Parser;
use PPFrame;

/**
 * Interface implemented by all array parser functions.
 *
 * @method execute() Function with dynamic parameters that must be implemented by all array functions. Argument
 * 					 validation is done automatically by the ArrayFunctionInvoker class.
 */
abstract class ArrayFunction {
	/**
	 * @var Parser The current parser
	 */
	private Parser $parser;

	/**
	 * @var PPFrame The current frame
	 */
	private PPFrame $frame;

	/**
	 * @var array<string, mixed> The values of configuration variables specified by self::getRequiredConfigVariables()
	 */
	private array $config;

	/**
	 * @var array The list of passed keyword arguments
	 */
	private array $keywordArgs;

	/**
	 * Returns the name of this parser function.
	 *
	 * @return string
	 */
	abstract public static function getName(): string;

	/**
	 * Returns the aliases of this parser function.
	 *
	 * @return array
	 */
	public static function getAliases(): array {
		return [];
	}

	/**
	 * Returns a dictionary of keyword names to specification. The specification should follow the following schema:
	 *
	 * {
	 *     "$schema": "https://json-schema.org/draft-04/schema#",
	 *     "title": "Keyword argument specification",
	 *     "type": "object",
	 *     "properties": {
	 *         "default": {
	 *             "description": "A default value to assign to the argument."
	 *         },
	 *         "type": {
	 *              "enum": ["boolean", "double", "integer", "string", "array", "mixed"],
	 *              "default": "mixed",
	 *              "description": "The type of this keyword argument, or nothing to allow any type."
	 *         },
	 *         "description": {
	 *              "type": "string",
	 *              "default": "",
	 *              "description": "A description of the keyword argument."
	 *         }
	 *     }
	 * }
	 *
	 * @return array
	 */
	public static function getKeywordSpec(): array {
		return [];
	}

	/**
	 * Returns a list of configuration variable names that should be available.
	 *
	 * @return array
	 */
	public static function getRequiredConfigVariables(): array {
		return [];
	}

	/**
	 * Whether to allow arbitrary keyword arguments, or only those specified in $this->getKeywordSpec().
	 *
	 * @return bool
	 */
	public static function allowArbitraryKeywordArgs(): bool {
		return false;
	}

	/**
	 * @param Parser $parser
	 * @param PPFrame $frame
	 * @param array $config
	 */
	final public function __construct( Parser $parser, PPFrame $frame, array $config ) {
		$this->parser = $parser;
		$this->frame = $frame;
		$this->config = $config;
	}

	/**
	 * Sets the keyword arguments to pass.
	 *
	 * @param array $keywordArgs
	 * @return void
	 */
	final public function setKeywordArgs( array $keywordArgs ): void {
		$this->keywordArgs = $keywordArgs;
	}

	/**
	 * Returns the keyword argument with the given name, or throws an exception if it does not exist.
	 *
	 * @param string $keyword The name of the keyword argument to get
	 * @return mixed
	 * @throws MWException
	 */
	final protected function getKeywordArg( string $keyword ) {
		if ( !isset( $this->keywordArgs[$keyword] ) ) {
			throw new MWException( "Missing required keyword argument " . $keyword );
		}

		return $this->keywordArgs[$keyword];
	}

	/**
	 * Returns all passed keyword arguments.
	 *
	 * @return array
	 */
	final protected function getKeywordArgs(): array {
		return $this->keywordArgs;
	}

	/**
	 * Returns the injected parser.
	 *
	 * @return Parser
	 */
	final protected function getParser(): Parser {
		return $this->parser;
	}

	/**
	 * Returns the injected frame.
	 *
	 * @return PPFrame
	 */
	final protected function getFrame(): PPFrame {
		return $this->frame;
	}

	/**
	 * Returns the injected config.
	 *
	 * @return array
	 */
	final protected function getConfig(): array {
		return $this->config;
	}

	/**
	 * Returns the value of the configuration variable with the given name, or throws an exception if it
	 * does not exist (e.g. is not listed in self::getRequiredConfigVariables()).
	 *
	 * @param string $keyword The name of the configuration variable to get the value for
	 * @return mixed
	 * @throws MWException
	 */
	final protected function getConfigValue( string $keyword ) {
		if ( !isset( $this->config[ $keyword ] ) ) {
			throw new MWException( "Missing required configuration " . $keyword );
		}

		return $this->config[$keyword];
	}
}
