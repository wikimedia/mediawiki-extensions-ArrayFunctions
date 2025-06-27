<?php

namespace ArrayFunctions\Exceptions;

use MWException;
use Throwable;

/**
 * Thrown when a parser function was an error as an argument.
 */
class ArgumentPropagateException extends MWException {
	/**
	 * @var int|string The name of the argument that was passed the error
	 */
	private $argName;

	/**
	 * @var string The ID of the error
	 */
	private string $errorId;

	/**
	 * @var array The message array of the error
	 */
	private array $messageArray;

	/**
	 * @param string|int $argName
	 * @param string $errorId
	 * @param array $messageArray
	 * @param Throwable|null $previous
	 */
	public function __construct( $argName, string $errorId, array $messageArray, ?Throwable $previous = null ) {
		parent::__construct( '', 0, $previous );

		$this->argName = $argName;
		$this->errorId = $errorId;
		$this->messageArray = $messageArray;
	}

	/**
	 * Return the ame of the argument that was passed the error.
	 *
	 * @return int|string
	 */
	public function getArgName() {
		return $this->argName;
	}

	/**
	 * Return the ID of the error.
	 *
	 * @return string
	 */
	public function getErrorId(): string {
		return $this->errorId;
	}

	/**
	 * Return the message of the error.
	 *
	 * @return array
	 */
	public function getMessageArray(): array {
		return $this->messageArray;
	}
}
