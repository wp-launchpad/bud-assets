<?php

namespace LaunchpadBudAssets\Builders;

class CSSBuilder extends AssetBuilder {

	/**
	 * On which media the script will be applied.
	 *
	 * @var string
	 */
	protected $media = 'all';

	/**
	 * Change medias on which the script going to be applied.
	 * @param string $media
	 *
	 * @return $this
	 */
	public function with_media(string $media): self {
		$this->media = $media;
		return $this;
	}

	/**
	 * Enqueue the style.
	 *
	 * @return string
	 */
	public function enqueue(): string {
		$key = parent::enqueue();

		if('' == $key) {
			return $key;
		}

		list($style_url, $dependencies) = $this->fetch_real_style($this->url, $this->dependencies, $this->media);

		wp_enqueue_style($key, $style_url, $dependencies, $this->plugin_version, $this->media);

		return $key;
	}

	/**
	 * Register the style.
	 *
	 * @return string
	 */
	public function register(): string {
		$key = parent::register();

		if('' == $key) {
			return $key;
		}

		list($style_url, $dependencies) = $this->fetch_real_style($this->url, $this->dependencies, $this->media);

		wp_register_style($key, $style_url, $dependencies, $this->plugin_version, $this->media);

		return $key;
	}
}