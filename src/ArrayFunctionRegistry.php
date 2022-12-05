<?php

namespace ArrayFunctions;

use ArrayFunctions\ArrayFunctions\AFCombine;
use ArrayFunctions\ArrayFunctions\AFCreate;
use ArrayFunctions\ArrayFunctions\AFForeach;
use ArrayFunctions\ArrayFunctions\AFGet;
use ArrayFunctions\ArrayFunctions\AFIsarray;
use ArrayFunctions\ArrayFunctions\AFJoin;
use ArrayFunctions\ArrayFunctions\AFMap;
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
	 * Define an array function.
	 *
	 * @param class-string<ArrayFunction> $function The class implementing the array function
	 */
	public function setFunction( string $function ): void {
		$this->functions[] = $function;
	}

	/**
	 * Returns the array functions available by default.
	 *
	 * @return array
	 */
	private static function getDefaults(): array {
		return [
			AFCreate::class,
			AFForeach::class,
			AFGet::class,
			AFIsarray::class,
			AFJoin::class,
			AFMap::class,
			AFPrint::class,
			AFPush::class
		];
	}
}
