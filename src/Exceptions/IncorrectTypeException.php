<?php

namespace ArrayFunctions\Exceptions;

use MWException;
use Throwable;

/**
 * Thrown when an invalid type is given.
 */
class IncorrectTypeException extends MWException {
	/**
	 * @var string The name of the actual type
	 */
	private string $actual;

	/**
	 * @var string The name of the expected type
	 */
	private string $expected;

	public function __construct(string $actual, string $expected, $message = "", $code = 0, Throwable $previous = null) {
		parent::__construct($message, $code, $previous);

		$this->actual = $actual;
		$this->expected = $expected;
	}

	/**
	 * Returns the name of the actual type.
	 *
	 * @return string
	 */
	public function getActual(): string {
		return $this->actual;
	}

	/**
	 * Returns the name of the expected type.
	 *
	 * @return string
	 */
	public function getExpected(): string {
		return $this->expected;
	}
}
