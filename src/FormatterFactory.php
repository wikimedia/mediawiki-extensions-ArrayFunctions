<?php

namespace ArrayFunctions;

use ArrayFunctions\ArrayFunctions\Formatters\Formatter;
use ArrayFunctions\ArrayFunctions\Formatters\SimpleFormatter;
use ArrayFunctions\ArrayFunctions\Formatters\TableFormatter;

class FormatterFactory {
	public const DEFAULT_FORMATTERS = [
		'simple' => SimpleFormatter::class,
		'table' => TableFormatter::class
	];

	/**
	 * Constructs a new formatter fo the given format name. If the formatter does not
	 * exist, is returns null.
	 *
	 * @param string $formatName
	 * @return Formatter|null
	 */
	public function newFormatter( string $formatName ): ?Formatter {
		if ( !isset( self::DEFAULT_FORMATTERS[ $formatName ] ) ) {
			return null;
		}

		$formatter = self::DEFAULT_FORMATTERS[ $formatName ];

		return new $formatter();
	}
}
