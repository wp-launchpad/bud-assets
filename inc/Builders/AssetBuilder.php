<?php

namespace LaunchpadBudAssets\Builders;

use LaunchpadFilesystem\FilesystemBase;

abstract class AssetBuilder {

	use FetchAssets;

	protected $url = '';

	protected $dependencies = [];

	protected $key = '';

	protected $plugin_slug = '';

	protected $plugin_version = '';

	protected $assets_url = '';

	public function with_dependencies(array $dependencies): self {
		$this->dependencies = array_merge($this->dependencies, $dependencies);
		return $this;
	}

	public function with_dependency(string $dependency): self {
		$this->dependencies []= $dependency;
		return $this;
	}

	public function with_key(string $key): self {
		$this->key = $key;
		return $this;
	}

	/**
	 * @var FilesystemBase
	 */
	protected $filesystem;

	/**
	 * @param string $url
	 * @param string $plugin_slug
	 * @param string $plugin_version
	 * @param string $assets_url
	 * @param FilesystemBase $filesystem
	 */
	public function __construct( string $url, string $plugin_slug, string $plugin_version, string $assets_url, FilesystemBase $filesystem ) {
		$this->url            = $url;
		$this->plugin_slug    = $plugin_slug;
		$this->plugin_version = $plugin_version;
		$this->assets_url     = $assets_url;
		$this->filesystem     = $filesystem;
	}

	/**
	 * Get full key.
	 *
	 * @param string $key partial key.
	 * @return string
	 */
	public function get_full_key(string $key) {
		return $this->plugin_slug . $key;
	}

	protected function get_plugin_version(): string {
		return $this->plugin_version;
	}

	protected function get_assets_url(): string {
		return $this->assets_url;
	}

	protected function get_filesystem(): FilesystemBase {
		return $this->filesystem;
	}

	abstract public function enqueue(): string;

	abstract public function register(): string;
}