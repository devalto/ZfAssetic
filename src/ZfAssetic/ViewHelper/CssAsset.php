<?php

class ZfAssetic_ViewHelper_CssAsset extends ZfAssetic_ViewHelper_AbstractAsset {

	protected $_type = "css";

	public function cssAsset() {
		return $this;
	}

	public function parseCapture($content) {
		// Parse <link> for href then add them
		$document = new DOMDocument();
		$document->strictErrorChecking = FALSE;
		$document->loadHTML($content);

		$links = $document->getElementsByTagName('link');
		foreach ($links as $link) {
			if ($link->nodeType == XML_ELEMENT_NODE && $link->hasAttribute('href')) {
				$this->addAsset($link->getAttribute('href'));
			}
		}
	}

	public function toString() {
		$destination = $this->process();
		return "<link rel=\"stylesheet\" href=\"$destination\" type=\"text/css\" />";
	}

}
