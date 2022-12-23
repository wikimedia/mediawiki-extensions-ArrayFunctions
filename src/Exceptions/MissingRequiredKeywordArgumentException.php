<?php

namespace ArrayFunctions\Exceptions;

use MWException;
use Throwable;

/**
 * Thrown when a required keyword argument is omitted.
 */
class MissingRequiredKeywordArgumentException extends MWException {
	/**
	 * @var string The name of the keyword argument that was omitted
	 */
	private string $keyword;

	/**
	 * @param string $keyword The name of the keyword argument that was omitted
	 * @param Throwable|null $previous
	 */
	public function __construct( string $keyword, Throwable $previous = null ) {
		parent::__construct('', 0, $previous);

		$this->keyword = $keyword;
	}

	/**
	 * Returns the name of the keyword argument that was omitted.
	 *
	 * @return string
	 */
	public function getKeyword(): string {
		return $this->keyword;
	}
}
