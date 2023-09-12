<?php

namespace ArrayFunctions\ArrayFunctions;

use ArrayFunctions\Utils;
use PPNode;

/**
 * Implements the #af_stringmap parser function.
 */
class AFStringmap extends ArrayFunction {
	/**
	 * @inheritDoc
	 */
	public static function getName(): string {
		return 'af_stringmap';
	}

	/**
	 * @inheritDoc
	 */
	public function execute(
		?string $value,
		?string $delimiter,
		string $valueName,
		PPNode $callback,
		?string $newDelimiter = ', ',
		?string $conjunction = null
	): array {
		if ( empty( $value ) ) {
			return [ '' ];
		}

		$delimiter = $delimiter ? Utils::unescape( $delimiter ) : ',';

		$list = array_filter( explode( $delimiter, $value ), fn ( string $item ): string => !empty( $item ) );
		$result = array_map( function ( $value ) use ( $valueName, $callback ) {
			$args = $this->getFrame()->getArguments();
			$args[$valueName] = Utils::export( $value );

			$nodeArray = $this->getParser()->getPreprocessor()->newPartNodeArray( $args );
			$childFrame = $this->getFrame()->newChild( $nodeArray, $this->getFrame()->getTitle() );

			return Utils::import( trim( $childFrame->expand( $callback ) ) );
		}, $list );

		// If no new delimiter is given, the default is ", " (see signature). However, if the new delimiter is
		// explicitly set to the empty string, NULL gets passed, and we use the empty string.
		$newDelimiter = $newDelimiter !== null ? Utils::unescape( $newDelimiter ) : '';
		$conjunction = empty( $conjunction ) ? $newDelimiter : ' ' . Utils::unescape( $conjunction ) . ' ';

		$last = array_pop( $result );

		// In theory, if $result is empty, $lastElement would be NULL. However, since the result is built up from
		// an explode (which can never return an empty array), this is safe.
		return empty( $result ) ?
			[ $last ] :
			[ implode( $newDelimiter, $result ) . $conjunction . $last ];
	}
}
