<?php

namespace ArrayFunctions\ArrayFunctions;

use Parser;
use PPFrame;

/**
 * Interface implemented by all array parser functions.
 *
 * @method execute() Function with dynamic parameters that must be implemented by all array functions. Argument validation is done
 *                           automatically by the ArrayFunctionInvoker class.
 */
abstract class ArrayFunction {
	private Parser $parser;
	private PPFrame $frame;

	final public function __construct( Parser $parser, PPFrame $frame ) {
		$this->parser = $parser;
		$this->frame = $frame;
	}

	/**
	 * Returns the injected parser.
	 *
	 * @return Parser
	 */
	protected function getParser(): Parser {
		return $this->parser;
	}

	/**
	 * Returns the injected frame.
	 *
	 * @return PPFrame
	 */
	protected function getFrame(): PPFrame {
		return $this->frame;
	}
}
