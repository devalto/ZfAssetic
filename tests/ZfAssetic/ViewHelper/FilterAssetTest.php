<?php

class ZfAssetic_ViewHelper_FilterAssetTest extends PHPUnit_Framework_TestCase {

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

	public function testMininfyCss() {
		$helper = $this->_getHelper('Css');

		$factory = $helper->getAssetFactory();
		$factory->addWorker(new \Assetic\Factory\Worker\EnsureFilterWorker(
			"/.css$/",
			new \Assetic\Filter\Yui\CssCompressorFilter('/usr/share/yui-compressor/yui-compressor.jar')
		));

		$helper->addAsset('test1.css');
		$helper->addAsset('test2.css');

		$destination = $helper->process();

		$this->assertStringEqualsFile($this->public_dir . $destination, implode("\n", array("", "#some-other-rule{background-color:purple}")));
	}

	public function testMininfyScript() {
		$helper = $this->_getHelper('Script');

		$factory = $helper->getAssetFactory();
		$factory->addWorker(new \Assetic\Factory\Worker\EnsureFilterWorker(
			"/.js$/",
			new \Assetic\Filter\Yui\JsCompressorFilter('/usr/share/yui-compressor/yui-compressor.jar')
		));

		$helper->addAsset('test1.js');
		$helper->addAsset('test2.js');
		$helper->addAsset('test3.js');

		$destination = $helper->process();

		$this->assertStringEqualsFile(
			$this->public_dir . $destination,
			implode(
				"\n",
				array(
					'var name="Cedric Lesquir";',
					'alert(name);',
					'var test=function(b){var a=this;if(a){a.name=b}return a};test("Sylvain Filteau");'
				)
			)
		);
	}

	protected function _getHelper($type) {
		$class = 'ZfAssetic_ViewHelper_' . $type . 'Asset';
		$helper = new $class();
		$helper->setPublicDirectory($this->public_dir);
		$helper->setAssetDirectory($this->asset_dir);
		
		return $helper;
	}

}
