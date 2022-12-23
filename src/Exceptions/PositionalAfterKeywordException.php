<?php

namespace ArrayFunctions\Exceptions;

use MWException;

/**
 * Thrown when a positional argument is passed after a keyword argument.
 */
class PositionalAfterKeywordException extends MWException {
}
