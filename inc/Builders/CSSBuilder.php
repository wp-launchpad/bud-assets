<?php

namespace LaunchpadBudAssets\Builders;

class CSSBuilder extends AssetBuilder {

	protected $media = 'all';

	public function with_media(string $media): self {
		$this->media = $media;
		return $this;
	}

	public function enqueue(): string {
		list($style_url, $dependencies) = $this->fetch_real_style($this->url, $this->dependencies, $this->media);

		$full_key = $this->get_full_key($this->key);

		wp_enqueue_style($full_key, $style_url, $dependencies, $this->plugin_version, $this->media);

		return $full_key;
	}

	public function register(): string {
		list($style_url, $dependencies) = $this->fetch_real_style($this->url, $this->dependencies, $this->media);

		$full_key = $this->get_full_key($this->key);

		wp_register_style($full_key, $style_url, $dependencies, $this->plugin_version, $this->media);

		return $full_key;
	}
}