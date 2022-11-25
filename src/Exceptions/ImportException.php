<?php

namespace ArrayFunctions\Exceptions;

/**
 * Thrown when an invalid value is given to the import method.
 */
class ImportException extends ArrayFunctionException {
	/**
	 * @inheritDoc
	 */
	protected function getWikitextErrorMessage(): string {
		return 'af-error-invalid-input';
	}
}
