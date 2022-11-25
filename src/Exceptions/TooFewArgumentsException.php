<?php

namespace ArrayFunctions\Exceptions;

use MWException;
use Throwable;

/**
 * Thrown when too few arguments are passed to an array function.
 */
class TooFewArgumentsException extends MWException {
	/**
	 * @var int The number of arguments that was given
	 */
	private int $actual;

	/**
	 * @var int The number of arguments that was expected
	 */
	private int $expected;

	public function __construct(int $actual, int $expected, $message = "", $code = 0, Throwable $previous = null) {
		parent::__construct($message, $code, $previous);

		$this->actual = $actual;
		$this->expected = $expected;
	}

	/**
	 * Returns the number of arguments that was given.
	 *
	 * @return int
	 */
	public function getActual(): int {
		return $this->actual;
	}

	/**
	 * Returns the number of arguments that was expected.
	 *
	 * @return int
	 */
	public function getExpected(): int {
		return $this->expected;
	}
}
