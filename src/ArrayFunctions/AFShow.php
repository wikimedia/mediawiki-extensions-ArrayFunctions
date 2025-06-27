<?php

namespace ArrayFunctions\ArrayFunctions;

use ArrayFunctions\ArrayFunctions\Formatters\Formatter;
use ArrayFunctions\Exceptions\RuntimeException;
use ArrayFunctions\FormatterFactory;
use ArrayFunctions\Utils;
use MediaWiki\MediaWikiServices;

/**
 * Implements the #af_show parser function.
 */
class AFShow extends ArrayFunction {
	private const KWARG_FORMAT = 'format';

	/**
	 * @inheritDoc
	 */
	public static function getName(): string {
		return 'af_show';
	}

	/**
	 * @inheritDoc
	 */
	public static function getKeywordSpec(): array {
		return [
			self::KWARG_FORMAT => [
				'default' => 'simple',
				'type' => 'string',
				'description' => 'The format to use for display.'
			]
		];
	}

	/**
	 * @inheritDoc
	 * @throws RuntimeException
	 */
	public function execute( $value ): array {
		if ( $value === null ) {
			$value = '';
		}

		$formatters = $this->getFormatters();

		foreach ( $formatters as $formatter ) {
			$result = $formatter->format( $value );

			if ( $result !== null ) {
				return [ $result ];
			}
		}

		throw new RuntimeException(
			Utils::createMessageArray( 'af-error-value-not-showable', [ Utils::export( $value ) ] )
		);
	}

	/**
	 * Returns the formatters to use.
	 *
	 * @return Formatter[]
	 */
	private function getFormatters(): array {
		$formatterNames = $this->getKeywordArg( 'format' );
		$formatterNames = explode( ',', $formatterNames );
		$formatterNames = array_map( 'trim', $formatterNames );
		$formatterNames = array_filter( $formatterNames );

		/** @var FormatterFactory $formatterFactory */
		$formatterFactory = MediaWikiServices::getInstance()->get( 'ArrayFunctions.FormatterFactory' );

		$formatters = [];

		foreach ( $formatterNames as $formatterName ) {
			$formatter = $formatterFactory->newFormatter( $formatterName );

			if ( $formatter === null ) {
				throw new RuntimeException(
					Utils::createMessageArray( 'af-error-unknown-format', [ $formatterName ] )
				);
			}

			$formatters[] = $formatter;
		}

		// Always add 'simple' as the fallback formatter
		$formatters[] = $formatterFactory->newFormatter( 'simple' );

		return $formatters;
	}
}
