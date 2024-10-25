<?php

namespace LaunchpadBudAssets\Builders;

class JavascriptBuilder extends AssetBuilder {

	/**
	 * Is the script in the footer?
	 *
	 * @var bool
	 */
	protected $in_footer = false;

	/**
	 * The script is in the footer.
	 * @return $this
	 */
	public function in_footer(): self {
		$this->in_footer = true;
		return $this;
	}

	/**
	 * Enqueue the script.
	 *
	 * @return string
	 */
	public function enqueue(): string {
		$key = parent::enqueue();

		if('' == $key) {
			return $key;
		}

		list($script_url, $dependencies) = $this->fetch_real_script($this->url, $this->dependencies, $this->in_footer);

		wp_enqueue_script($key, $script_url, $dependencies, $this->plugin_version, $this->in_footer);

		return $key;
	}

	/**
	 * Register the script.
	 *
	 * @return string
	 */
	public function register(): string {
		$key = parent::register();

		if('' == $key) {
			return $key;
		}

		list($script_url, $dependencies) = $this->fetch_real_script($this->url, $this->dependencies, $this->in_footer);

		wp_register_script($key, $script_url, $dependencies, $this->plugin_version, $this->in_footer);
		return $key;
	}
}