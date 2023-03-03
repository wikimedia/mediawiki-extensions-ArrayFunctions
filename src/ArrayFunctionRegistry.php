<?php

namespace ArrayFunctions;

use ArrayFunctions\ArrayFunctions\AFBool;
use ArrayFunctions\ArrayFunctions\AFCount;
use ArrayFunctions\ArrayFunctions\AFExists;
use ArrayFunctions\ArrayFunctions\AFFloat;
use ArrayFunctions\ArrayFunctions\AFForeach;
use ArrayFunctions\ArrayFunctions\AFGet;
use ArrayFunctions\ArrayFunctions\AFIf;
use ArrayFunctions\ArrayFunctions\AFInt;
use ArrayFunctions\ArrayFunctions\AFIntersect;
use ArrayFunctions\ArrayFunctions\AFIsarray;
use ArrayFunctions\ArrayFunctions\AFJoin;
use ArrayFunctions\ArrayFunctions\AFKeysort;
use ArrayFunctions\ArrayFunctions\AFList;
use ArrayFunctions\ArrayFunctions\AFMap;
use ArrayFunctions\ArrayFunctions\AFMerge;
use ArrayFunctions\ArrayFunctions\AFObject;
use ArrayFunctions\ArrayFunctions\AFPrint;
use ArrayFunctions\ArrayFunctions\AFPush;
use ArrayFunctions\ArrayFunctions\AFReduce;
use ArrayFunctions\ArrayFunctions\AFSet;
use ArrayFunctions\ArrayFunctions\AFSlice;
use ArrayFunctions\ArrayFunctions\AFSort;
use ArrayFunctions\ArrayFunctions\AFSplit;
use ArrayFunctions\ArrayFunctions\AFTemplate;
use ArrayFunctions\ArrayFunctions\AFUnique;
use ArrayFunctions\ArrayFunctions\AFUnset;
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
			AFBool::class,
			AFCount::class,
			AFExists::class,
			AFList::class,
			AFFloat::class,
			AFForeach::class,
			AFGet::class,
			AFIf::class,
			AFInt::class,
			AFIntersect::class,
			AFIsarray::class,
			AFJoin::class,
			AFKeysort::class,
			AFMap::class,
			AFMerge::class,
			AFObject::class,
			AFPrint::class,
			AFPush::class,
			AFReduce::class,
			AFSet::class,
			AFSlice::class,
			AFSort::class,
			AFSplit::class,
			AFTemplate::class,
			AFUnique::class,
			AFUnset::class
		];
	}
}
