<?php

namespace ArrayFunctions;

use ArrayFunctions\ArrayFunctions\ArrayFunction;
use Config;
use Parser;
use PPFrame;

class ArrayFunctionFactory {
	/**
	 * @var Config The MediaWiki configuration
	 */
	private Config $config;

	/**
	 * @param Config $config The MediaWiki configuration
	 */
	public function __construct( Config $config ) {
		$this->config = $config;
	}

	/**
	 * @param class-string<ArrayFunction> $function
	 * @param Parser $parser
	 * @param PPFrame $frame
	 * @return ArrayFunction
	 */
	public function createArrayFunction( string $function, Parser $parser, PPFrame $frame ): ArrayFunction {
		return new $function( $parser, $frame, $this->buildConfig( $function ) );
	}

	/**
	 * Builds an array containing all configuration variables required by the class.
	 *
	 * @param class-string<ArrayFunction> $function
	 *
	 * @return array
	 */
	private function buildConfig( string $function ): array {
		$config = [];
		$requiredConfig = $function::getRequiredConfigVariables();

		foreach ( $requiredConfig as $configName ) {
			$config[$configName] = $this->config->get( $configName );
		}

		return $config;
	}
}
