<?php

namespace ArrayFunctions\ArrayFunctions;

use ArrayFunctions\Utils;
use PPNode;

/**
 * Implements the #af_pipeline parser function.
 */
class AFPipeline extends ArrayFunction {
	private const KWARG_PARAMETER = 'parameter';

	/**
	 * @inheritDoc
	 */
	public static function getName(): string {
		return 'af_pipeline';
	}

	/**
	 * @inheritDoc
	 */
	public static function getKeywordSpec(): array {
		return [
			self::KWARG_PARAMETER => [
				'default' => 'prev',
				'type' => 'string',
				'description' => 'The name to use for the previous value.'
			]
		];
	}

	/**
	 * @inheritDoc
	 */
	public function execute( $initial, ?PPNode ...$steps ): array {
		$parameter = $this->getKeywordArg( self::KWARG_PARAMETER );
		$result = $initial;

		foreach ( $steps as $step ) {
			$args = $this->getFrame()->getArguments();
			$args[$parameter] = Utils::export( $result );

			$nodeArray = $this->getParser()->getPreprocessor()->newPartNodeArray( $args );
			$childFrame = $this->getFrame()->newChild( $nodeArray, $this->getFrame()->getTitle() );

			$result = trim( $childFrame->expand( $step ) );
		}

		return [ $result ];
	}
}
