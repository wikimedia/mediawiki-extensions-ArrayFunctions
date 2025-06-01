<?php

namespace ArrayFunctions\ArrayFunctions\Formatters;

interface Formatter {
	/**
	 * Format the given value, or return NULL if the value is not formattable by the formatter.
	 *
	 * @param mixed $value
	 * @return string|null The formatter value, or NULL if the value is not formattable
	 */
	public function format( $value ): ?string;
}
