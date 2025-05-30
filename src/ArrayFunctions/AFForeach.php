<?php

namespace ArrayFunctions\ArrayFunctions;

use ArrayFunctions\Exceptions\RuntimeException;
use ArrayFunctions\Utils;
use PPNode;

/**
 * Implements the #af_foreach parser function.
 */
class AFForeach extends ArrayFunction {
	private const KWARG_DELIMITER = 'delimiter';
	private const CONFIG_FOREACH_ITERATION_LIMIT = 'ArrayFunctionsForeachIterationLimit';
	private const STATE_FOREACH_ITERATIONS = 'ArrayFunctionsIterations';

	/**
	 * @inheritDoc
	 */
	public static function getName(): string {
		return 'af_foreach';
	}

	public static function getRequiredConfigVariables(): array {
		return [ self::CONFIG_FOREACH_ITERATION_LIMIT ];
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

		$maxIterationCount = $this->getConfigValue( self::CONFIG_FOREACH_ITERATION_LIMIT );

		foreach ( $array as $key => $value ) {
			if ( $maxIterationCount >= 0 ) {
				$iterations = $this->getParser()->getOutput()->getExtensionData( self::STATE_FOREACH_ITERATIONS ) ?? [];
				$iterationCount = count( $iterations );

				if ( $iterationCount >= $maxIterationCount ) {
					throw new RuntimeException( wfMessage( 'af-error-foreach-iteration-limit-reached' ) );
				}

				$this->getParser()->getOutput()->appendExtensionData(
					self::STATE_FOREACH_ITERATIONS,
					$this->computeIterationKey()
				);
			}

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

		$delimiter = $this->getKeywordArg( self::KWARG_DELIMITER );

		return [ implode( $delimiter, $result ) ];
	}

	/**
	 * Compute a unique iteration key.
	 *
	 * @return string
	 */
	private function computeIterationKey(): string {
		$length = 12;
		$characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
		$charactersLength = strlen( $characters );
		$randomKey = '';

		for ( $i = 0; $i < $length; $i++ ) {
			$index = mt_rand( 0, $charactersLength - 1 );
			$randomKey .= $characters[$index];
		}

		return 'ArrayFunctions_iter_' . $randomKey;
	}
}
