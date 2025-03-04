<?php

namespace ArrayFunctions\SemanticMediaWiki;

use ArrayFunctions\Utils;
use SMW\Query\PrintRequest;
use SMW\Query\Result\ResultArray;
use SMW\Query\ResultPrinters\ResultPrinter;
use SMWQueryResult as QueryResult;

/**
 * Defines a result format for ArrayFunctions for Semantic MediaWiki.
 *
 * This file was inspired by Semantic Scribunto's `LuaAskResultProcessor` class (GNU GPL-2.0).
 *
 * @link https://www.semantic-mediawiki.org/wiki/Help:Result_formats
 * @link https://github.com/SemanticMediaWiki/SemanticScribunto/blob/master/src/LuaAskResultProcessor.php
 */
class ArrayFunctionsResultPrinter extends ResultPrinter {
	/**
	 * @var string[] Array of strings that are considered "true" by Semantic MediaWiki.
	 */
	private array $trueStrings;

	/**
	 * @inheritDoc
	 */
	public function __construct( $format, $inline = true ) {
		parent::__construct( $format, $inline );

		$this->trueStrings = explode( ',', wfMessage( 'smw_true_words' )->text() . ',true,t,yes,y' );
	}

	/**
	 * @inheritDoc
	 */
	public function getName(): string {
		return wfMessage( 'af-smw-result-printer-name' )->text();
	}

	/**
	 * @inheritDoc
	 *
	 * @param \SMW\Query\QueryResult $res
	 */
	protected function getResultText( QueryResult $res, $outputMode ) {
		$result = [];

		// phpcs:ignore
		while ( $row = $res->getNext() ) {
			$result[] = $this->getDataFromResultRow( $row );
		}

		return Utils::export( $result );
	}

	/**
	 * Format the given result row.
	 *
	 * @param ResultArray[] $resultRow
	 * @return array
	 */
	private function getDataFromResultRow( array $resultRow ): array {
		$result = [];
		$index = 0;

		foreach ( $resultRow as $resultArray ) {
			$key = $this->getKeyFromResultArray( $resultArray, $index );
			$data = $this->getDataFromResultArray( $resultArray );

			$result[$key] = $data;
		}

		return $result;
	}

	/**
	 * Get the printout key for a result array.
	 *
	 * @param ResultArray $resultArray
	 * @param int &$index
	 * @return int|string
	 */
	private function getKeyFromResultArray( ResultArray $resultArray, int &$index ) {
		return $this->getKeyFromPrintRequest( $resultArray->getPrintRequest(), $index );
	}

	/**
	 * Get the data for a result array.
	 *
	 * @param ResultArray $resultArray
	 * @return array|mixed|null
	 */
	private function getDataFromResultArray( ResultArray $resultArray ) {
		$result = [];

		// phpcs:ignore
		while ( $dataValue = $resultArray->getNextDataValue() ) {
			$result[] = $this->getValueFromDataValue( $dataValue );
		}

		return count( $result ) === 1 ?
			array_shift( $result ) :
			$result;
	}

	/**
	 * Get the value for a data value.
	 *
	 * @param \SMWDataValue $dataValue
	 * @return bool|int|string
	 */
	private function getValueFromDataValue( \SMWDataValue $dataValue ) {
		switch ( $dataValue->getTypeID() ) {
			case '_boo':
				return in_array( strtolower( $dataValue->getWikiValue() ?? 'null' ), $this->trueStrings );
			case '_num':
				$value = $dataValue instanceof \SMWNumberValue ? $dataValue->getNumber() : 0;
				return $value == (int)$value ? intval( $value ) : $value;
			default:
				return $dataValue->getShortText( SMW_OUTPUT_WIKI );
		}
	}

	/**
	 * Get the key for a printout request.
	 *
	 * @param PrintRequest $printRequest
	 * @param int &$index
	 * @return int|string
	 */
	private function getKeyFromPrintRequest( PrintRequest $printRequest, int &$index ) {
		return $printRequest->getLabel() === '' ?
			$index++ :
			$printRequest->getText( SMW_OUTPUT_WIKI );
	}
}
