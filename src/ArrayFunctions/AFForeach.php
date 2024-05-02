<?php

namespace ArrayFunctions\ArrayFunctions;

use ArrayFunctions\Utils;
use PPNode;

/**
 * Implements the #af_foreach parser function.
 */
class AFForeach extends ArrayFunction {
	private const KWARG_DELIMITER = 'delimiter';

	/**
	 * @inheritDoc
	 */
	public static function getName(): string {
		return 'af_foreach';
	}

	/**
	 * @inheritDoc
	 */
	public static function getKeywordSpec(): array {
		return [
			self::KWARG_DELIMITER => [
				'default' => '',
				'type' => 'string',
				'description' => 'The delimiter to put between each result.'
			]
		];
	}

	/**
	 * @inheritDoc
	 */
	public function execute(
		array $array,
		?string $keyName = null,
		?string $valueName = null,
		?PPNode $body = null
	): array {
		$result = [];

		foreach ( $array as $key => $value ) {
			$args = $this->getFrame()->getArguments();

			if ( $keyName !== null ) {
				$args[$keyName] = $key;
			}

			if ( $valueName !== null ) {
				$args[$valueName] = Utils::export( $value );
			}

			$nodeArray = $this->getParser()->getPreprocessor()->newPartNodeArray( $args );
			$childFrame = $this->getFrame()->newChild( $nodeArray, $this->getFrame()->getTitle() );

			$result[] = $body !== null ?
				trim( $childFrame->expand( $body ) ) :
				'';
		}

		$delimiter = Utils::unescape( $this->getKeywordArg( self::KWARG_DELIMITER ) );

		return [ implode( $delimiter, $result ) ];
	}
}
