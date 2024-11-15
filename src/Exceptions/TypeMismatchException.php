<?php

namespace ArrayFunctions\Exceptions;

use MWException;
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
	 * @var int|string The argument name or index
	 */
	private $name;

	/**
	 * @var string The argument value
	 */
	private string $value;

	/**
	 * @param string $actual The actual type
	 * @param string $expected The expected type
	 * @param string $value The value of the argument (un-imported)
	 * @param int|string $name The name or index of the argument in which the mismatch occurred
	 * @param Throwable|null $previous
	 */
	public function __construct( string $actual, string $expected, string $value, $name, ?Throwable $previous = null ) {
		parent::__construct( '', 0, $previous );

		$this->actual = $actual;
		$this->expected = $expected;
		$this->value = $value;
		$this->name = $name;
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
	 * Returns the argument value.
	 *
	 * @return string
	 */
	public function getValue(): string {
		return $this->value;
	}

	/**
	 * Returns the argument name or index.
	 *
	 * @return int|string
	 */
	public function getName() {
		return $this->name;
	}
}
