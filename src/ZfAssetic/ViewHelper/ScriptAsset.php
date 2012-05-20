<?php

class ZfAssetic_ViewHelper_ScriptAsset extends ZfAssetic_ViewHelper_AbstractAsset {

	protected $_type = "js";

	public function scriptAsset() {
		return $this;
	}

	public function parseCapture($content) {
		// Parse <link> for href then add them
		$document = new DOMDocument();
		$document->strictErrorChecking = FALSE;
		$document->loadHTML($content);

		$links = $document->getElementsByTagName('script');
		foreach ($links as $link) {
			if ($link->nodeType == XML_ELEMENT_NODE && $link->hasAttribute('src')) {
				$this->addAsset($link->getAttribute('src'));
			}
		}
	}

	public function toString() {
		$destination = $this->process();
		return "<script src=\"$destination\" type=\"text/javascript\"></script>";
	}

}
