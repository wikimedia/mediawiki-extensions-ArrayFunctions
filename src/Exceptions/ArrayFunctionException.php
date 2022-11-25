<?php

namespace ArrayFunctions\Exceptions;

use ArrayFunctions\Utils;
use Message;
use MWException;

abstract class ArrayFunctionException extends MWException {
	/**
	 * Returns this exception as wikitext to be returned directly by a parser function.
	 *
	 * @param string $function The name of the parser function that caused the error
	 * @param array $args The arguments to pass to the message (depends on the exception type)
	 *
	 * @return array
	 */
	public function getWikitextError( string $function, array $args = [] ): array {
		return [ Utils::error( $function, $this->getWikitextErrorMessage(), $args ), 'noparse' => false ];
	}

	/**
	 * Returns the system message to display to the user of the parser function.
	 *
	 * @return Message|string
	 */
	abstract protected function getWikitextErrorMessage();
}
