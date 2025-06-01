<?php

namespace ArrayFunctions\ArrayFunctions\Formatters;

use ArrayFunctions\Utils;

class TableFormatter implements Formatter {
	/**
	 * @inheritDoc
	 */
	public function format( $value ): ?string {
		if ( !is_array( $value ) ) {
			return null;
		}

		if ( $this->isList( $value ) ) {
			if ( $this->allValuesLists( $value ) ) {
				return $this->formatListOfLists( $value );
			} elseif ( $this->allValuesObjects( $value ) ) {
				return $this->formatListOfObjects( $value );
			} else {
				return $this->formatListOfMixed( $value );
			}
		} else {
			if ( $this->allValuesLists( $value ) ) {
				return $this->formatObjectOfLists( $value );
			} elseif ( $this->allValuesObjects( $value ) ) {
				return $this->formatObjectOfObjects( $value );
			} else {
				return $this->formatObjectOfMixed( $value );
			}
		}
	}

	/**
	 * Format the given array as a list of mixed values.
	 *
	 * A list of mixed values is formatted as a single row.
	 *
	 * @param array $array
	 * @return string
	 */
	private function formatListOfMixed( array $array ): string {
		return $this->formatAsTable( [ $array ] );
	}

	/**
	 * Format the given array as a list of lists.
	 *
	 * A list of lists is formatted as a 2-dimensional table, where the rows are the nested lists.
	 *
	 * @param array $array
	 * @return string
	 */
	private function formatListOfLists( array $array ): string {
		$array = array_map( static fn ( $value ) => is_array( $value ) ? $value : [ $value ], $array );
		$longestRowLength = max( [ 0, ...array_values( array_map( 'count', $array ) ) ] );

		$tableArray = [];
		foreach ( $array as $row ) {
			$tableArray[] = array_pad( $row, $longestRowLength, '' );
		}

		return $this->formatAsTable( $tableArray );
	}

	/**
	 * Format the given array as a list of objects.
	 *
	 * A list of objects is formatted as a 2-dimensional table, where the columns are the keys
	 * of the objects and the rows are the values for each sub-object.
	 *
	 * @param array $array
	 * @return string
	 */
	private function formatListOfObjects( array $array ): string {
		$array = array_map( static fn ( $value ) => is_array( $value ) ? $value : [ $value ], $array );
		$colHeaders = array_unique( array_merge( ...array_values( array_map( 'array_keys', $array ) ) ) );

		$tableArray = [];
		foreach ( $array as $row ) {
			$rowArray = [];
			foreach ( $colHeaders as $key ) {
				$rowArray[] = $row[$key] ?? '';
			}

			$tableArray[] = $rowArray;
		}

		return $this->formatAsTable( $tableArray, $colHeaders );
	}

	/**
	 * Format the given array as an object of mixed values.
	 *
	 * An object of mixed values is formatted as a single row with headers corresponding to the
	 * keys of the object.
	 *
	 * @param array $array
	 * @return string
	 */
	private function formatObjectOfMixed( array $array ): string {
		$colHeaders = array_keys( $array );
		$tableArray = [ $array ];

		return $this->formatAsTable( $tableArray, $colHeaders );
	}

	/**
	 * Format the given array as an object of lists.
	 *
	 * An object of lists is formatted as a 2-dimensional table where each row starts with
	 * the key, and the values in the row are the values in the list.
	 *
	 * @param array $array
	 * @return string
	 */
	private function formatObjectOfLists( array $array ): string {
		$array = array_map( static fn ( $value ) => is_array( $value ) ? $value : [ $value ], $array );
		$rowHeaders = array_keys( $array );
		$longestRowLength = max( [ 0, ...array_map( 'count', $array ) ] );

		$tableArray = [];
		foreach ( $array as $row ) {
			$tableArray[] = array_pad( $row, $longestRowLength, '' );
		}

		return $this->formatAsTable( $tableArray, [], $rowHeaders );
	}

	/**
	 * Format the given array as an object of objects.
	 *
	 * An object of object is formatted as a 2-dimensional table, where the row headers are the keys
	 * of the outer object, the column headers are the keys of the inner objects and the values are
	 * at the intersection of these.
	 *
	 * @param array $array
	 * @return string
	 */
	private function formatObjectOfObjects( array $array ): string {
		$array = array_map( static fn ( $value ) => is_array( $value ) ? $value : [ $value ], $array );
		$colHeaders = array_unique( array_merge( ...array_values( array_map( 'array_keys', $array ) ) ) );
		$rowHeaders = array_keys( $array );

		$tableArray = [];
		foreach ( $array as $row ) {
			$rowArray = [];
			foreach ( $colHeaders as $key ) {
				$rowArray[] = $row[$key] ?? '';
			}

			$tableArray[] = $rowArray;
		}

		return $this->formatAsTable( $tableArray, $colHeaders, $rowHeaders );
	}

	/**
	 * Formats the given value for use in a table column.
	 *
	 * @param mixed $value
	 * @return string
	 */
	private function formatColumnValue( $value ): string {
		if ( is_array( $value ) ) {
			return $this->format( $value );
		}

		return Utils::stringify( $value );
	}

	/**
	 * Formats the given array as an HTML table.
	 *
	 * @param string[][] $array The array to format as an HTML table.
	 * @param string[] $colHeaders
	 * @param string[] $rowHeaders
	 * @return string
	 */
	public function formatAsTable( array $array, array $colHeaders = [], array $rowHeaders = [] ): string {
		$array = array_map( static fn ( $value ) => is_array( $value ) ? $value : [ $value ], $array );

		$wikitext = '<table class="wikitable">';

		if ( count( $colHeaders ) > 0 ) {
			$wikitext .= '<tr>';

			if ( count( $rowHeaders ) > 0 ) {
				$wikitext .= '<th></th>';
			}

			$wikitext .= implode( '', array_map( static fn ( $v ) => "<th>$v</th>", $colHeaders ) );
			$wikitext .= '</tr>';
		}

		foreach ( array_values( $array ) as $i => $row ) {
			$wikitext .= '<tr>';

			if ( isset( $rowHeaders[$i] ) ) {
				$wikitext .= '<th scope="col">';
				$wikitext .= $rowHeaders[$i];
				$wikitext .= '</th>';
			}

			$cols = array_map( fn ( $v ) => '<td>' . $this->formatColumnValue( $v ) . '</td>', $row );

			$wikitext .= implode( '', $cols );
			$wikitext .= '</tr>';
		}

		$wikitext .= '</table>';

		return $wikitext;
	}

	/**
	 * Whether the given array only contains objects as values.
	 *
	 * @param array $array
	 * @return bool
	 */
	private function allValuesObjects( array $array ): bool {
		return $this->all( $array, fn ( $v ) => $this->isObject( $v ) );
	}

	/**
	 * Whether the given array only contains lists as values.
	 *
	 * @param array $array
	 * @return bool
	 */
	private function allValuesLists( array $array ): bool {
		return $this->all( $array, fn ( $v ) => $this->isList( $v ) );
	}

	/**
	 * Whether the given value is an object.
	 *
	 * @param mixed $value
	 * @return bool
	 */
	private function isObject( $value ): bool {
		return is_array( $value ) && !$this->isList( $value );
	}

	/**
	 * Whether the given value is a list. This is a slightly looser definition than
	 * array_is_list(), because we do not require the keys to be in order. Instead,
	 * they only must be numeric.
	 *
	 * @param mixed $value
	 * @return bool
	 */
	private function isList( $value ): bool {
		if ( !is_array( $value ) ) {
			return false;
		}

		foreach ( array_keys( $value ) as $key ) {
			if ( !is_int( $key ) ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Whether the given predicate holds for all values in the array.
	 *
	 * @param array $array
	 * @param callable $predicate
	 * @return bool
	 */
	private function all( array $array, callable $predicate ): bool {
		foreach ( $array as $value ) {
			if ( !$predicate( $value ) ) {
				return false;
			}
		}

		return true;
	}
}
