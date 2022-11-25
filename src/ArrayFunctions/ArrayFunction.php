<?php

namespace ArrayFunctions\ArrayFunctions;

use Parser;
use PPFrame;

/**
 * Interface implemented by all array parser functions.
 *
 * @method execute(...$args) Function with dynamic parameters that must be implemented by all array functions. Argument validation is done
 *                           automatically through the ArgumentsPreprocessor class using reflection.
 */
interface ArrayFunction {
}
