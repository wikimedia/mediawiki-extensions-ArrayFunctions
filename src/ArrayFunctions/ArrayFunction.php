<?php

namespace ArrayFunctions\ArrayFunctions;

use Parser;
use PPFrame;

/**
 * Interface implemented by all array parser functions.
 *
 * @method execute() Function with dynamic parameters that must be implemented by all array functions. Argument validation is done
 *                   automatically by the ArrayFunctionInvoker class.
 */
abstract class ArrayFunction {
	private Parser $parser;
	private PPFrame $frame;

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
	 * Returns a dictionary of keyword names to specification. The specification should follow the following schema:
	 *
	 * {
	 *     "$schema": "https://json-schema.org/draft-04/schema#",
	 *     "title": "Keyword argument specification",
	 *     "type": "object",
	 *     "properties": {
	 *         "required": {
	 *             "type": "boolean",
	 *             "default": false,
	 *             "description": "Whether this keyword argument is required."
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
	 * Whether to allow arbitrary keyword arguments, or only those specified in $this->getKeywordSpec().
	 *
	 * @return bool
	 */
	public static function allowArbitraryKeywordArgs(): bool {
		return false;
	}

	final public function __construct( Parser $parser, PPFrame $frame ) {
		$this->parser = $parser;
		$this->frame = $frame;
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
	 * Returns the keyword arguments that were passed.
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
}
