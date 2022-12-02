<?php

namespace ArrayFunctions;

use ArrayFunctions\ArrayFunctions\AFForeach;
use ArrayFunctions\ArrayFunctions\AFGet;
use ArrayFunctions\ArrayFunctions\AFIsarray;
use ArrayFunctions\ArrayFunctions\AFPrint;
use ArrayFunctions\ArrayFunctions\AFPush;
use ArrayFunctions\ArrayFunctions\ArrayFunction;

class ArrayFunctionRegistry {
	/**
	 * @var array Dictionary from parser function names to class names
	 */
	private array $functions;

	public function __construct() {
		$this->functions = $this->getDefaults();
	}

	/**
	 * Returns the defined array functions.
	 *
	 * @return array
	 */
	public function getFunctions(): array {
		return $this->functions;
	}

	/**
	 * Define or override an array function.
	 *
	 * @param string $name The name of the array function
	 * @param class-string<ArrayFunction> $function The class implementing the array function
	 */
	public function setFunction( string $name, string $function ): void {
		$this->functions[$name] = $function;
	}

	/**
	 * Returns the array functions available by default.
	 *
	 * @return array
	 */
	private static function getDefaults(): array {
		return [
			'af_foreach' => AFForeach::class,
			'af_isarray' => AFIsarray::class,
			'af_get' => AFGet::class,
			'af_print' => AFPrint::class,
			'af_push' => AFPush::class
		];
	}
}
