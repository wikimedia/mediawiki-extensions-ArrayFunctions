<?php

namespace ArrayFunctions\Exceptions;

use MWException;
use PPNode;
use Throwable;

/**
 * Thrown when an invalid type is given.
 */
class TypeMismatchException extends MWException {
	/**
	 * @var string The name of the actual type
	 */
	private string $actual;

	/**
	 * @var string The name of the expected type
	 */
	private string $expected;

	/**
	 * @var int The parameter index
	 */
	private int $index;

	/**
	 * @var string The parameter value
	 */
	private string $value;

	/**
	 * @param string $actual The actual type
	 * @param string $expected The expected type
	 * @param string $value The value of the parameter (un-imported)
	 * @param int $index The parameter index in which the mismatch occurred
	 * @param Throwable|null $previous
	 */
	public function __construct(string $actual, string $expected, string $value, int $index, Throwable $previous = null ) {
		parent::__construct( '', 0, $previous );

		$this->actual = $actual;
		$this->expected = $expected;
		$this->value = $value;
		$this->index = $index;
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

	/**
	 * Returns the parameter value.
	 *
	 * @return string
	 */
	public function getValue(): string {
		return $this->value;
	}

	/**
	 * Returns the parameter index.
	 *
	 * @return int
	 */
	public function getIndex(): int {
		return $this->index;
	}
}
