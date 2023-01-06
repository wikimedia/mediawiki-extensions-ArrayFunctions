<?php

namespace ArrayFunctions\ArrayFunctions;

use ArrayFunctions\Exceptions\RuntimeException;
use ArrayFunctions\Utils;
use MediaWiki\MediaWikiServices;
use MediaWiki\Permissions\PermissionManager;
use MediaWiki\User\UserFactory;
use NamespaceInfo;
use Title;
use TitleFactory;

/**
 * Implements the #af_template parser function.
 */
class AFTemplate extends ArrayFunction {
	/**
	 * @inheritDoc
	 */
	public static function getName(): string {
		return 'af_template';
	}

	/**
	 * @inheritDoc
	 * @throws RuntimeException
	 */
	public function execute( string $templateName, array $data ): array {
		return [ $this->expandTemplate( $templateName, $data ) ];
	}

	/**
	 * Expands the given template with the given arguments. This function does not build wikitext, because that
	 * is prone to wikitext injection attacks. It instead uses the parser to expand the template with the given
	 * arguments directly.
	 *
	 * @param string $templateName The template to expand
	 * @param array $data The data to pass to the template
	 * @return string The resulting HTML
	 * @throws RuntimeException
	 */
	private function expandTemplate( string $templateName, array $data ): string {
		$frame = $this->getFrame();
		$title = $this->getTitleForTemplateName( $templateName );

		if ( $title === null || !$title->canExist() ) {
			// The given template title is not valid
			throw new RuntimeException( wfMessage( 'af-error-invalid-template-name', $templateName ) );
		}

		if ( $frame->depth >= $this->getParser()->getOptions()->getMaxTemplateDepth() ) {
			// The max template depth has been reached
			throw new RuntimeException( wfMessage( 'af-error-max-template-depth-reached' ) );
		}

		if ( method_exists( $this->getParser(), 'getUserIdentity' ) ) {
			$user = $this->getUserFactory()->newFromUserIdentity( $this->getParser()->getUserIdentity() );
		} else {
			$user = $this->getParser()->getUser();
		}

		if (
			!$this->getPermissionManager()->userCan( 'read', $user, $title ) ||
			$this->getNamespaceInfo()->isNonincludable( $title->getNamespace() )
		) {
			// Template inclusion is denied
			throw new RuntimeException( wfMessage( 'af-error-template-inclusion-denied', $templateName ) );
		}

		list( $node, $finalTitle ) = $this->getParser()->getTemplateDom( $title );

		if ( $node === false ) {
			// Template does not exist
			throw new RuntimeException( wfMessage( 'af-error-template-does-not-exist', $templateName ) );
		}

		if ( !$this->getFrame()->loopCheck( $finalTitle ) ) {
			// Template loop detected
			throw new RuntimeException( wfMessage( 'af-error-template-loop-detected', $templateName ) );
		}

		// Export any non-strings in the given data
		$exportedData = array_map( fn ( $subData ): string => Utils::export( $subData ), $data );

		// Increment any numeric keys with one, so counting starts at 1
		$templateArgs = [];

		foreach ( $exportedData as $key => $value ) {
			if ( is_int( $key ) ) {
				$key++;
			}

			$templateArgs[$key] = $value;
		}

		// Build a new frame for the template and the given data
		$templateArgs = $this->getParser()->getPreprocessor()->newPartNodeArray( $templateArgs );
		$newFrame = $frame->newChild( $templateArgs, $finalTitle );

		// Expand the frame and return the resulting HTML
		return $newFrame->expand( $node );
	}

	/**
	 * Returns the title for the given template name.
	 *
	 * @param string $templateName
	 * @return Title|null
	 */
	private function getTitleForTemplateName( string $templateName ): ?Title {
		return $this->getTitleFactory()->newFromText( $templateName, NS_TEMPLATE );
	}

	/**
	 * Returns the TitleFactory singleton.
	 *
	 * @return TitleFactory
	 */
	private function getTitleFactory(): TitleFactory {
		return MediaWikiServices::getInstance()->getTitleFactory();
	}

	/**
	 * Returns the NamespaceInfo singleton.
	 *
	 * @return NamespaceInfo
	 */
	private function getNamespaceInfo(): NamespaceInfo {
		return MediaWikiServices::getInstance()->getNamespaceInfo();
	}

	/**
	 * Returns the PermissionManager singleton.
	 *
	 * @return PermissionManager
	 */
	private function getPermissionManager(): PermissionManager {
		return MediaWikiServices::getInstance()->getPermissionManager();
	}

	/**
	 * Returns the UserFactory singleton.
	 *
	 * @return UserFactory
	 */
	public function getUserFactory(): UserFactory {
		return MediaWikiServices::getInstance()->getUserFactory();
	}
}
