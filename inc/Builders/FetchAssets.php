<?php

namespace LaunchpadBudAssets\Builders;

use LaunchpadFilesystem\FilesystemBase;

trait FetchAssets {

	protected $entrypoints_file = 'entrypoints.json';

	/**
	 * Assets path.
	 *
	 * @var string
	 */
	protected $assets_path = '';

	/**
	 * Find dependencies from a bud asset.
	 *
	 * @param string $url asset URL.
	 * @return array
	 */
	protected function find_bud_dependencies(string $url): array {
		$url_parts = explode('.', $url);
		$assets_path = $this->get_assets_path();
		$entrypoints_path = $assets_path . DIRECTORY_SEPARATOR . $this->entrypoints_file;
		if( ! $this->get_filesystem()->exists($entrypoints_path)) {
			return [];
		}

		$entrypoints = json_decode($this->get_filesystem()->get_contents($entrypoints_path), true);
		foreach ($url_parts as $part) {
			if(! is_array($entrypoints) || ! key_exists($part, $entrypoints)) {
				return [];
			}

			$entrypoints = $entrypoints[$part];
		}

		$entrypoints = array_map(function ($entrypoint) {
			return $this->get_assets_url() . DIRECTORY_SEPARATOR . $entrypoint;
		}, $entrypoints);

		return $entrypoints;
	}

	/**
	 * Fetch the real url from the style and register its dependencies.
	 *
	 * @param string $url style URL.
	 * @param array $dependencies style
	 * @param string $media which media the style should display.
	 *
	 * @return array
	 */
	protected function fetch_real_style(string $url, array $dependencies = [], string $media = 'all'): array
	{
		$bud_dependencies = $this->find_bud_dependencies($url);
		if(count($bud_dependencies) === 0) {
			$bud_dependencies = [
				$url,
			];
		}

		$last_url = array_pop($bud_dependencies);

		foreach ($bud_dependencies as $bud_dependency) {
			$full_key = $this->generate_key($bud_dependency);
			wp_register_style($full_key, $bud_dependency, $dependencies, $this->get_plugin_version(), $media);
			$dependencies []= $full_key;
		}

		return [$last_url, $dependencies];
	}

	/**
	 * Fetch the real url from the script and register its dependencies.
	 *
	 * @param string $url script url.
	 * @param array $dependencies script dependencies.
	 * @param bool $in_footer is the script in the footer.
	 *
	 * @return array
	 */
	protected function fetch_real_script(string $url, array $dependencies = [], bool $in_footer = false): array
	{
		$bud_dependencies = $this->find_bud_dependencies($url);
		if(count($bud_dependencies) === 0) {
			$bud_dependencies = [
				$url,
			];
		}

		$last_url = array_pop($bud_dependencies);

		foreach ($bud_dependencies as $bud_dependency) {
			$full_key = $this->generate_key($bud_dependency);
			wp_register_script($full_key, $bud_dependency, $dependencies, $this->get_plugin_version(), $in_footer);
			$dependencies []= $full_key;
		}

		return [$last_url, $dependencies];
	}

	/**
	 * Get assets path.
	 *
	 * @return string
	 */
	protected function get_assets_path(): string {
		if($this->assets_path) {
			return $this->assets_path;
		}

		$plugin_url = plugin_dir_url($this->plugin_launcher_file);
		$plugin_dir = dirname($this->plugin_launcher_file);
		$assets_path = str_replace($plugin_url, '', $this->assets_url);
		$assets_path = $plugin_dir . DIRECTORY_SEPARATOR . $assets_path;
		$this->assets_path = str_replace(DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR, $assets_path);

		return $this->assets_path;
	}

	/**
	 * Generate a key for the URL.
	 *
	 * @param string $url URL to generate a key for.
	 *
	 * @return string
	 */
	protected function generate_key(string $url): string {
		return $this->get_plugin_slug() . sanitize_key($url);
	}

	/**
	 * Get the plugin version.
	 *
	 * @return string
	 */
	abstract protected function get_plugin_version(): string;

	/**
	 * Get the plugin slug.
	 *
	 * @return string
	 */
	abstract protected function get_plugin_slug(): string;

	/**
	 * Get the plugin assets URL.
	 *
	 * @return string
	 */
	abstract protected function get_assets_url(): string;

	/**
	 * Get the plugin launcher file.
	 *
	 * @return string
	 */
	abstract protected function get_plugin_launcher_file(): string;
	/**
	 * Get the filesystem.
	 *
	 * @return FilesystemBase
	 */
	abstract protected function get_filesystem(): FilesystemBase;

}