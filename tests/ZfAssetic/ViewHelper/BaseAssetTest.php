<?php

class ZfAssetic_ViewHelper_BaseAssetTest extends PHPUnit_Framework_TestCase {

	public function tearDown() {
		Zend_Registry::_unsetInstance();
	}

	public function testGetAssetFactoryWithoutConfigurationThrowsException() {
		$this->setExpectedException("RuntimeException", "No AssetFactory was previously configured");

		$helper = new ZfAssetic_ViewHelper_CssAsset();
		$helper->getAssetFactory();
	}

	public function testGetAssetFactoryWithFactoryConfiguredInRegistry() {
		$factory = new Assetic\Factory\AssetFactory('');
		Zend_Registry::set('AssetFactory', $factory);

		$helper = new ZfAssetic_ViewHelper_CssAsset();

		$this->assertSame($factory, $helper->getAssetFactory());
	}

	public function testCaptureLinkTags() {
		$helper = new ZfAssetic_ViewHelper_CssAsset();
		$helper->captureStart();
		echo "<link href=\"/test1.css\" rel=\"stylesheet\" type=\"text/css\" />";
		echo "<link href=\"/test2.css\" rel=\"stylesheet\" type=\"text/css\" />";
		$helper->captureEnd();

		$this->assertCount(2, $helper->getAssets());
		$this->assertContains('test1.css', $helper->getAssets());
		$this->assertContains('test2.css', $helper->getAssets());
	}

}
