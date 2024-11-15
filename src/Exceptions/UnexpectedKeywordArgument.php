<?php

namespace ArrayFunctions\Exceptions;

use MWException;
use Throwable;

/**
 * Thrown when an unexpected keyword argument is passed.
 */
class UnexpectedKeywordArgument extends MWException {
	/**
	 * @var string The name of the keyword argument that was omitted
	 */
	private string $keyword;

	/**
	 * @param string $keyword The name of the unexpected keyword argument
	 * @param Throwable|null $previous
	 */
	public function __construct( string $keyword, ?Throwable $previous = null ) {
		parent::__construct( '', 0, $previous );

		$this->keyword = $keyword;
	}

	/**
	 * Returns the name of the unexpected keyword argument.
	 *
	 * @return string
	 */
	public function getKeyword(): string {
		return $this->keyword;
	}
}
