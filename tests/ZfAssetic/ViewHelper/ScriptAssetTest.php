<?php

class ZfAssetic_ViewHelper_ScriptAssetTest extends PHPUnit_Framework_TestCase {

	public $public_dir;
	public $asset_dir;

	public function setUp() {
		Zend_Registry::_unsetInstance();

		$this->public_dir = __DIR__ . '/_public_dir';
		$this->asset_dir = $this->public_dir . '/asset';

		if (is_dir($this->public_dir)) {
			`rm -r {$this->public_dir}`;
			`mkdir -p {$this->asset_dir}`;
		}
	}

	public function testProcessCreatesFileAndReturnUrl() {
		$factory = new Assetic\Factory\AssetFactory(__DIR__ . '/_files');
		Zend_Registry::set('AssetFactory', $factory);

		$helper = new ZfAssetic_ViewHelper_ScriptAsset();
		$helper->setPublicDirectory($this->public_dir);
		$helper->setAssetDirectory($this->asset_dir);
		$helper->addAsset('/test1.js');
		$helper->addAsset('/test2.js');

		$destination = $helper->process();

		$this->assertEquals('/asset/0141522526d6be5302041ffa6093933b.js', $destination);
		$this->assertFileExists($this->asset_dir . '/0141522526d6be5302041ffa6093933b.js');
		$this->assertStringEqualsFile($this->asset_dir . '/0141522526d6be5302041ffa6093933b.js', 'var name = "Cedric Lesquir";

alert(name);
');
	}

	public function testCaptureAndOutputResult() {
		$factory = new Assetic\Factory\AssetFactory(__DIR__ . '/_files');
		Zend_Registry::set('AssetFactory', $factory);

		$helper = new ZfAssetic_ViewHelper_ScriptAsset();
		$helper->setPublicDirectory($this->public_dir);
		$helper->setAssetDirectory($this->asset_dir);

		$helper->captureStart();
		echo "<script src=\"/test1.js\"></script>";
		echo "<script src=\"/test2.js\"></script>";
		$helper->captureEnd();

		$this->assertEquals('<script src="/asset/0141522526d6be5302041ffa6093933b.js" type="text/javascript"></script>', $helper->toString());

	}

}
