<?php

class ZfAssetic_ViewHelper_CssAssetTest extends PHPUnit_Framework_TestCase {

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

		$factory = new Assetic\Factory\AssetFactory(__DIR__ . '/_files');
		Zend_Registry::set('AssetFactory', $factory);
	}

	public function testProcessCreatesFileAndReturnUrl() {

		$helper = $this->_getHelper();

		$helper->addAsset('/test1.css');
		$helper->addAsset('/test2.css');

		$destination = $helper->process();

		$this->assertEquals('/asset/1f79627b6faaadf8c6cd848430eaed9f.css', $destination);
		$this->assertFileExists($this->asset_dir . '/1f79627b6faaadf8c6cd848430eaed9f.css');
		$this->assertStringEqualsFile($this->asset_dir . '/1f79627b6faaadf8c6cd848430eaed9f.css', '.somerules {}

#some-other-rule {
	background-color: purple;
}
');
	}

	public function testCaptureAndOutputResult() {
		$helper = $this->_getHelper();

		$helper->captureStart();
		echo "<link href=\"/test1.css\" rel=\"stylesheet\" type=\"text/css\" />";
		echo "<link href=\"/test2.css\" rel=\"stylesheet\" type=\"text/css\" />";
		$helper->captureEnd();

		$this->assertEquals('<link rel="stylesheet" href="/asset/1f79627b6faaadf8c6cd848430eaed9f.css" type="text/css" />', (string)$helper);
	}

	protected function _getHelper() {
		$helper = new ZfAssetic_ViewHelper_CssAsset();
		$helper->setPublicDirectory($this->public_dir);
		$helper->setAssetDirectory($this->asset_dir);
		
		return $helper;
	}

}
