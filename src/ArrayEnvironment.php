<?php

namespace ArrayFunctions;

use ArrayFunctions\Exceptions\RuntimeException;

class ArrayEnvironment {
	/**
	 * @var array<string, array> Hashtable of array references
	 */
	private array $arrays = [];

	/**
	 * @var int The total number of alive references
	 */
	private int $referenceCount = 0;

	/**
	 * @var int The maximum number of alive references
	 */
	private int $maxReferenceCount;

	public function __construct( int $maxReferenceCount = 1024 ) {
		$this->maxReferenceCount = $maxReferenceCount;
	}

	/**
	 * Stores the given array and returns the reference to it.
	 *
	 * @param array $value The array to store
	 * @return string The reference
	 */
	public function store( array $value ): string {
		if ( $this->referenceCount >= $this->maxReferenceCount ) {
			throw new RuntimeException( wfMessage( 'af-max-reference-count-reached' ) );
		}

		$reference = $this->newReference();

		$this->arrays[$reference] = $value;
		$this->referenceCount++;

		return $reference;
	}

	/**
	 * Fetches the given reference. Returns NULL if the reference does not point to an existing array.
	 *
	 * @param string $reference The reference to dereference
	 * @return array|null The value, or NULL if the reference does not point to an existing array
	 */
	public function dereference( string $reference ): ?array {
		return $this->arrays[$reference] ?? null;
	}

	/**
	 * Deletes the given reference. Used for garbage collection.
	 *
	 * @param string $reference The reference to delete
	 * @return void
	 */
	public function delete( string $reference ): void {
		if ( !isset( $this->arrays[$reference] ) ) {
			return;
		}

		unset( $this->arrays[$reference] );
		$this->referenceCount--;
	}

	/**
	 * Returns the number of alive references.
	 *
	 * @return int
	 */
	public function getReferenceCount(): int {
		return $this->referenceCount;
	}

	/**
	 * Returns a new reference that is guaranteed to be unique.
	 *
	 * @note Theoretically, this function would fail after being called 1.84 * 10^19 times, but
	 *       you would run out of memory long before that happens.
	 *
	 * @return string
	 */
	private function newReference(): string {
		do {
			// Generate a 16-character random string
			$reference = bin2hex( random_bytes( 8 ) );
		} while ( isset( $this->arrays[$reference] ) );

		return $reference;
	}
}
