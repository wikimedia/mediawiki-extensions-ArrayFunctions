<?php

namespace ArrayFunctions\Cargo;

use ArrayFunctions\Utils;
use CargoDisplayFormat;

class ArrayFunctionsFormat extends CargoDisplayFormat {
	public static function allowedParameters(): array {
		return [];
	}

	/**
	 * @param array $valuesTable
	 * @param array $formattedValuesTable unused
	 * @param array $fieldDescriptions
	 * @param array $displayParams unused
	 * @return string ArrayFunctions export
	 */
	public function display( $valuesTable, $formattedValuesTable, $fieldDescriptions, $displayParams ): string {
		// Turn "List" fields into arrays
		foreach ( $fieldDescriptions as $alias => $fieldDescription ) {
			if ( $fieldDescription->mIsList ) {
				$delimiter = $fieldDescription->getDelimiter();
				for ( $i = 0; $i < count( $valuesTable ); $i++ ) {
					$curValue = $valuesTable[$i][$alias];
					if ( !is_array( $curValue ) ) {
						$valuesTable[$i][$alias] = explode( $delimiter, $curValue );
					}
				}
			}
		}

		return Utils::export( $valuesTable );
	}
}
