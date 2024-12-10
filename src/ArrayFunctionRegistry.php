<?php

namespace ArrayFunctions;

use ArrayFunctions\ArrayFunctions\AFBool;
use ArrayFunctions\ArrayFunctions\AFCount;
use ArrayFunctions\ArrayFunctions\AFDifference;
use ArrayFunctions\ArrayFunctions\AFExists;
use ArrayFunctions\ArrayFunctions\AFFlatten;
use ArrayFunctions\ArrayFunctions\AFFloat;
use ArrayFunctions\ArrayFunctions\AFForeach;
use ArrayFunctions\ArrayFunctions\AFGet;
use ArrayFunctions\ArrayFunctions\AFIf;
use ArrayFunctions\ArrayFunctions\AFInt;
use ArrayFunctions\ArrayFunctions\AFIntersect;
use ArrayFunctions\ArrayFunctions\AFIsarray;
use ArrayFunctions\ArrayFunctions\AFJoin;
use ArrayFunctions\ArrayFunctions\AFKeysort;
use ArrayFunctions\ArrayFunctions\AFKsort;
use ArrayFunctions\ArrayFunctions\AFList;
use ArrayFunctions\ArrayFunctions\AFMap;
use ArrayFunctions\ArrayFunctions\AFMerge;
use ArrayFunctions\ArrayFunctions\AFObject;
use ArrayFunctions\ArrayFunctions\AFPrint;
use ArrayFunctions\ArrayFunctions\AFPush;
use ArrayFunctions\ArrayFunctions\AFReduce;
use ArrayFunctions\ArrayFunctions\AFReverse;
use ArrayFunctions\ArrayFunctions\AFSearch;
use ArrayFunctions\ArrayFunctions\AFSet;
use ArrayFunctions\ArrayFunctions\AFShow;
use ArrayFunctions\ArrayFunctions\AFSlice;
use ArrayFunctions\ArrayFunctions\AFSort;
use ArrayFunctions\ArrayFunctions\AFSplit;
use ArrayFunctions\ArrayFunctions\AFStringmap;
use ArrayFunctions\ArrayFunctions\AFTemplate;
use ArrayFunctions\ArrayFunctions\AFTrim;
use ArrayFunctions\ArrayFunctions\AFUnique;
use ArrayFunctions\ArrayFunctions\AFUnset;
use ArrayFunctions\ArrayFunctions\AFWildcard;
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
			AFDifference::class,
			AFExists::class,
			AFList::class,
			AFFlatten::class,
			AFFloat::class,
			AFForeach::class,
			AFGet::class,
			AFIf::class,
			AFInt::class,
			AFIntersect::class,
			AFIsarray::class,
			AFJoin::class,
			AFKeysort::class,
			AFKsort::class,
			AFMap::class,
			AFMerge::class,
			AFObject::class,
			AFPrint::class,
			AFPush::class,
			AFReduce::class,
			AFReverse::class,
			AFSearch::class,
			AFSet::class,
			AFShow::class,
			AFSlice::class,
			AFSort::class,
			AFSplit::class,
			AFStringmap::class,
			AFTemplate::class,
			AFTrim::class,
			AFUnique::class,
			AFUnset::class,
			AFWildcard::class,
		];
	}
}
