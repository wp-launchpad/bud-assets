<?php

namespace LaunchpadBudAssets\Builders;

class JavascriptBuilder extends AssetBuilder {

	protected $in_footer = false;

	public function in_footer(): self {
		$this->in_footer = true;
		return $this;
	}

	public function enqueue(): string {

		$key = $this->get_full_key($this->key);

		list($script_url, $dependencies) = $this->fetch_real_script($this->url, $this->dependencies, $this->in_footer);

		wp_enqueue_script($key, $script_url, $dependencies, $this->plugin_version, $this->in_footer);

		return $key;
	}

	public function register(): string {
		$key = $this->get_full_key($this->key);

		list($script_url, $dependencies) = $this->fetch_real_script($this->url, $this->dependencies, $this->in_footer);

		wp_register_script($key, $script_url, $dependencies, $this->plugin_version, $this->in_footer);
		return $this->get_full_key($key);
	}
}