<?php

namespace ArrayFunctions;

use MediaWiki\MediaWikiServices;
use Wikimedia\Services\ServiceContainer;

/**
 * Getter for all ArrayFunctions services. This class reduces the risk of mistyping
 * a service name and serves as the interface for retrieving services for ArrayFunctions.
 *
 * @note Program logic should use dependency injection instead of this class wherever
 * possible.
 *
 * @note This class should only contain static methods.
 *
 * @package WikiGuard
 */
final class ArrayFunctionsServices {
	/**
	 * Disable the construction of this class by making the constructor private.
	 */
	private function __construct() {
	}

	/**
	 * Get the ArrayEnvironment singleton.
	 *
	 * @param ServiceContainer|null $services
	 * @return ArrayEnvironment
	 */
	public static function getArrayEnvironment( ?ServiceContainer $services = null ): ArrayEnvironment {
		return self::getService( "ArrayEnvironment", $services );
	}

	/**
	 * Get the ArrayFunctionRegistry singleton.
	 *
	 * @param ServiceContainer|null $services
	 * @return ArrayFunctionRegistry
	 */
	public static function getArrayFunctionRegistry( ?ServiceContainer $services = null ): ArrayFunctionRegistry {
		return self::getService( "ArrayFunctionRegistry", $services );
	}

	/**
	 * Get the service with the given name.
	 *
	 * @param string $service
	 * @param ServiceContainer|null $services
	 * @return ArrayEnvironment|ArrayFunctionRegistry
	 */
	private static function getService( string $service, ?ServiceContainer $services ) {
		return ( $services ?: MediaWikiServices::getInstance() )->getService( "ArrayFunctions.$service" );
	}
}
