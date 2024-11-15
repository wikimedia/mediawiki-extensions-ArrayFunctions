<?php

namespace ArrayFunctions\Exceptions;

use Message;
use MWException;
use Throwable;

/**
 * Thrown when something went wrong during the execution of an array function.
 */
class RuntimeException extends MWException {
	/**
	 * @var Message Returns a descriptive message of the runtime error.
	 */
	private Message $runtimeMessage;

	/**
	 * @param Message $runtimeMessage A descriptive message of the runtime error.
	 * @param Throwable|null $previous
	 */
	public function __construct( Message $runtimeMessage, ?Throwable $previous = null ) {
		parent::__construct( '', 0, $previous );

		$this->runtimeMessage = $runtimeMessage;
	}

	/**
	 * Returns a descriptive message of the runtime error.
	 *
	 * @return Message
	 */
	public function getRuntimeMessage(): Message {
		return $this->runtimeMessage;
	}
}
