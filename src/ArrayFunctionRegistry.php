<?php

namespace ArrayFunctions;

use ArrayFunctions\ArrayFunctions\AFPrint;
use ArrayFunctions\ArrayFunctions\AFPush;
use ArrayFunctions\ArrayFunctions\ArrayFunction;
use ArrayFunctions\ArrayFunctions\AFForeach;
use ArrayFunctions\ArrayFunctions\AFGet;
use ArrayFunctions\ArrayFunctions\AFIsarray;

class ArrayFunctionRegistry {
	/**
	 * @var array Dictionary from parser function names to instances of ArrayFunction
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
	 * @param ArrayFunction $function The class implementing the array function
	 */
	public function setFunction( string $name, ArrayFunction $function ): void {
		$this->functions[$name] = $function;
	}

	/**
	 * Returns the array functions available by default.
	 *
	 * @return array
	 */
	private static function getDefaults(): array {
		return [
			'af_foreach' => new AFForeach(),
			'af_isarray' => new AFIsarray(),
			'af_get' => new AFGet(),
			'af_print' => new AFPrint(),
			'af_push' => new AFPush()
		];
	}
}
