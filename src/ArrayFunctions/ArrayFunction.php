<?php

namespace ArrayFunctions\ArrayFunctions;

use Message;
use Parser;
use PPFrame;

/**
 * Interface implemented by all array parser functions.
 */
interface ArrayFunction {
	/**
	 * Executes this array parser function.
	 *
	 * @param Parser $parser The parser instance to which this function was registered
	 * @param PPFrame $frame The current parser frame
	 * @param array $args The arguments passed to the function call
	 *
	 * @return array The result of the parser function
	 */
	public function execute( Parser $parser, PPFrame $frame, array $args ): array;
}
