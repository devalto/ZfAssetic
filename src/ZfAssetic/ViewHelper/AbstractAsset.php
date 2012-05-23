<?php

abstract class ZfAssetic_ViewHelper_AbstractAsset {

	/**
	 * Capture lock
	 * @var bool
	 */
	protected $_captureLock;

	protected $_assets = array();

	/**
	 * @var Assetic\Factory\AssetFactory
	 */
	protected $_asset_factory;

	/**
	 * Where to put assets when it is processed
	 *
	 * @var string
	 */
	protected $_asset_directory;

	/**
	 * Where is the public directory
	 *
	 * The public directory is where the webserver is configured to serve files
	 *
	 * @var string
	 */
	protected $_public_directory;

	/**
	 * The type of asset
	 *
	 * Could be "css" or "js"
	 *
	 * @var string
	 */
	protected $_type;

	public function addAsset($path) {
		$path = ltrim($path, '/');
		$this->_assets[] = $path;
	}

	public function getAssets() {
		return $this->_assets;
	}

	/**
	 * Start capture action
	 *
	 * @return void
	 */
	public function captureStart() {
		if ($this->_captureLock) {
			$e = new RuntimeException('Cannot nest captures');
			$e->setView($this->view);
			throw $e;
		}

		$this->_captureLock = true;
		ob_start();
	}

	/**
	 * End capture action and store
	 *
	 * @return void
	 */
	public function captureEnd() {
		$content = ob_get_clean();
		$this->_captureLock = false;

		$this->parseCapture($content);

		return $this;
	}

	public function getType() {
		if (!isset($this->_type)) {
			throw new RuntimeException('No asset type was previously configured');
		}

		return $this->_type;
	}

	/**
	 * Returns the asset factory that will be used to process files
	 *
	 * @return Assetic\Factory\AssetFactory
	 */
	public function getAssetFactory() {
		if (!$this->_asset_factory) {
			if (!Zend_Registry::isRegistered('AssetFactory')) {
				throw new RuntimeException('No AssetFactory was previously configured');
			}
			$this->setAssetFactory(Zend_Registry::get('AssetFactory'));
		}

		return $this->_asset_factory;
	}

	public function setAssetFactory(Assetic\Factory\AssetFactory $factory) {
		$this->_asset_factory = $factory;
	}

	public function getAssetDirectory() {
		if (!$this->_asset_directory) {
			if (!Zend_Registry::isRegistered('AssetDirectory')) {
				$this->setAssetDirectory($this->getPublicDirectory() . '/asset');
			} else {
				$this->setAssetDirectory(Zend_Registry::get('AssetDirectory'));
			}
		}

		return $this->_asset_directory;
	}

	public function setAssetDirectory($dir) {
		$dir = rtrim($dir, '/');

		if (!is_dir($dir)) {
			throw new RuntimeException("Directory $dir does not exist");
		}

		$this->_asset_directory = $dir;
	}

	public function getPublicDirectory() {
		if (!$this->_public_directory) {
			if (!Zend_Registry::isRegistered('PublicDirectory')) {
				throw new RuntimeException('No PublicDirectory was previously configured');
			}
			$this->setPublicDirectory(Zend_Registry::get('PublicDirectory'));
		}

		return $this->_public_directory;
	}

	public function setPublicDirectory($dir) {
		$dir = rtrim($dir, '/');

		if (!is_dir($dir)) {
			throw new RuntimeException("Directory $dir does not exist");
		}

		$this->_public_directory = $dir;
	}

	public function setConfig($config) {
		$key_to_method = array(
			'asset_factory' => 'setAssetFactory',
			'asset_directory' => 'setAssetDirectory'
		);

		foreach ($config as $k => $v) {
			if (isset($key_to_method[$k])) {
				$this->{$key_to_method[$k]}($v);
			}
		}
	}

	public function process() {
		$factory = $this->getAssetFactory();
		$asset = $factory->createAsset($this->getAssets());

		$hash = self::getCacheKey($asset);
		$destination = $this->getAssetDirectory() . '/' . $hash . '.' . $this->getType();

		// Check if the file exists
		if (!file_exists($destination)) {
			file_put_contents($destination, $asset->dump());
		}

		$destination = $this->_removePublicPath($destination);

		return $destination;
	}

	public function __toString() {
		return $this->toString();
	}

	abstract public function toString();

	abstract public function parseCapture($content);

	protected function _removePublicPath($path) {
		$public_path = $this->getPublicDirectory();
		if (substr($path, 0, strlen($public_path)) == $public_path) {
			return substr($path, strlen($public_path));
		}
	}

	/**
	 * Stolen from https://github.com/kriswallsmith/assetic/blob/master/src/Assetic/Asset/AssetCache.php
	 */
	private static function getCacheKey(\Assetic\Asset\AssetInterface $asset) {
		$cacheKey  = $asset->getSourceRoot();
		$cacheKey .= $asset->getSourcePath();
		$cacheKey .= $asset->getTargetPath();
		$cacheKey .= $asset->getLastModified();

		foreach ($asset->getFilters() as $filter) {
			if ($filter instanceof HashableInterface) {
				$cacheKey .= $filter->hash();
			} else {
				$cacheKey .= serialize($filter);
			}
		}

		if ($values = $asset->getValues()) {
			asort($values);
			$cacheKey .= serialize($values);
		}

		return md5($cacheKey);
	}

}

