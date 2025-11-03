<?php

namespace ArrayFunctions\ArrayFunctions;

use ArrayFunctions\Exceptions\RuntimeException;
use ArrayFunctions\Utils;
use MediaWiki\MediaWikiServices;
use MediaWiki\Title\Title;
use NamespaceInfo;
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
	 * @return string The resulting wikitext
	 * @throws RuntimeException
	 */
	private function expandTemplate( string $templateName, array $data ): string {
		$frame = $this->getFrame();
		$title = $this->getTitleForTemplateName( $templateName );

		if ( $title === null || !$title->canExist() ) {
			// The given template title is not valid
			throw new RuntimeException(
				Utils::createMessageArray( 'af-error-invalid-template-name', [ $templateName ] )
			);
		}

		if ( $frame->depth >= $this->getParser()->getOptions()->getMaxTemplateDepth() ) {
			// The max template depth has been reached
			throw new RuntimeException(
				Utils::createMessageArray( 'af-error-max-template-depth-reached' )
			);
		}

		if ( $this->getNamespaceInfo()->isNonincludable( $title->getNamespace() ) ) {
			// Template inclusion is denied
			throw new RuntimeException(
				Utils::createMessageArray( 'af-error-template-inclusion-denied', [ $templateName ] )
			);
		}

		[ $node, $finalTitle ] = $this->getParser()->getTemplateDom( $title );

		if ( $node === false ) {
			// Template does not exist
			return sprintf( '[[%s]]', $title->getFullText() );
		}

		if ( !$this->getFrame()->loopCheck( $finalTitle ) ) {
			// Template loop detected
			throw new RuntimeException(
				Utils::createMessageArray( 'af-error-template-loop-detected', [ $templateName ] )
			);
		}

		// Export any non-strings in the given data
		$exportedData = array_map( static fn ( $subData ): string => Utils::export( $subData ), $data );

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

		// Expand the frame and return the resulting wikitext
		return trim( $newFrame->expand( $node ) );
	}

	/**
	 * Returns the title for the given template name.
	 *
	 * @param string $templateName
	 * @return \MediaWiki\Title\Title|Title|null
	 */
	private function getTitleForTemplateName( string $templateName ) {
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
}
