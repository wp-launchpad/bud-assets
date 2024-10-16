<?php

namespace LaunchpadBudAssets\Builders;

use LaunchpadFilesystem\FilesystemBase;

abstract class AssetBuilder {

	use FetchAssets;

	/**
	 * URL from the asset.
	 *
	 * @var string
	 */
	protected $url = '';

	/**
	 * Asset dependencies.
	 *
	 * @var string[]
	 */
	protected $dependencies = [];

	/**
	 * Asset key.
	 *
	 * @var string
	 */
	protected $key = '';

	/**
	 * Plugin slug.
	 *
	 * @var string
	 */
	protected $plugin_slug = '';

	/**
	 * Plugin version.
	 *
	 * @var string
	 */
	protected $plugin_version = '';

	/**
	 * Asset base URL.
	 * @var string
	 */
	protected $assets_url = '';

	protected $assets_path = '';

	protected $queries = [];

	/**
	 * Plugin launcher file.
	 *
	 * @var string
	 */
	protected $plugin_launcher_file = '';

	/**
	 * Uses a query to add the assets.
	 *
	 * @param callable(AvailabilityQuery): void $query_setup Callback set up the query.
	 *
	 * @return $this
	 */
	public function with_query(callable $query_setup): self {
		$this->queries []= $query_setup(new AvailabilityQuery());
		return $this;
	}

	/**
	 * Add dependencies to the requirements for the asset.
	 *
	 * @param string[] $dependencies Dependencies to add.
	 *
	 * @return $this
	 */
	public function with_dependencies(array $dependencies): self {
		$this->dependencies = array_merge($this->dependencies, $dependencies);
		return $this;
	}

	/**
	 * Add dependency to the requirements for the asset.
	 *
	 * @param string $dependency Dependency to add.
	 *
	 * @return $this
	 */
	public function with_dependency(string $dependency): self {
		$this->dependencies []= $dependency;
		return $this;
	}

	/**
	 * Set up a key for the asset.
	 *
	 * @param string $key Asset key.
	 *
	 * @return $this
	 */
	public function with_key(string $key): self {
		$this->key = $key;
		return $this;
	}

	/**
	 * Filesystem.
	 *
	 * @var FilesystemBase
	 */
	protected $filesystem;

	/**
	 * Instantiate the builder.
	 *
	 * @param string $url URL from the asset.
	 * @param string $plugin_slug Plugin slug.
	 * @param string $plugin_version Plugin version.
	 * @param string $assets_url Plugin asset URL.
	 * @param FilesystemBase $filesystem Filesystem.
	 */
	public function __construct( string $url, string $plugin_slug, string $plugin_version, string $assets_url, string $plugin_launcher_file, FilesystemBase $filesystem ) {
		$this->url            = $url;
		$this->plugin_slug    = $plugin_slug;
		$this->plugin_version = $plugin_version;
		$this->assets_url     = $assets_url;
		$this->plugin_launcher_file    = $plugin_launcher_file;
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

	/**
	 * Get the plugin version.
	 *
	 * @return string
	 */
	protected function get_plugin_version(): string {
		return $this->plugin_version;
	}

	/**
	 * Get the plugin assets URL.
	 *
	 * @return string
	 */
	protected function get_assets_url(): string {
		return $this->assets_url;
	}

	/**
	 * Get the filesystem.
	 *
	 * @return FilesystemBase
	 */
	protected function get_filesystem(): FilesystemBase {
		return $this->filesystem;
	}

	protected function get_plugin_slug(): string {
		return $this->plugin_slug;
	}

	protected function get_plugin_launcher_file(): string {
		return $this->plugin_launcher_file;
	}

	/**
	 * Enqueue the asset.
	 *
	 * @return string
	 */
	public function enqueue(): string {
		return $this->apply_queries();
	}

	/**
	 * Register the asset.
	 *
	 * @return string
	 */
	public function register(): string {
		return $this->apply_queries();
	}

	/**
	 * Return the key based on the fact the queries are matched.
	 *
	 * @return string
	 */
	protected function apply_queries(): string {

		if(! $this->key) {
			$this->key =$this->url;
		}

		$full_key = $this->get_full_key($this->key);

		if(0 === count($this->queries)) {
			return $full_key;
		}

		foreach ($this->queries as $query) {
			if($query->applies()) {
				return $full_key;
			}
		}

		return '';
	}
}