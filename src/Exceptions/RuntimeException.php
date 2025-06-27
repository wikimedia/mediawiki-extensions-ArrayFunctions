<?php

namespace ArrayFunctions\Exceptions;

use MWException;
use Throwable;

/**
 * Thrown when something went wrong during the execution of an array function.
 */
class RuntimeException extends MWException {
	/**
	 * @var array A message array describing the runtime error
	 */
	private array $messageArray;

	/**
	 * @param array $messageArray A message array describing the runtime error
	 * @param Throwable|null $previous
	 */
	public function __construct( array $messageArray, ?Throwable $previous = null ) {
		parent::__construct( '', 0, $previous );

		$this->messageArray = $messageArray;
	}

	/**
	 * Returns a message array describing the runtime error.
	 *
	 * @return array
	 */
	public function getMessageArray(): array {
		return $this->messageArray;
	}
}
