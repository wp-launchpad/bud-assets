<?php

namespace LaunchpadBudAssets;

use LaunchpadBudAssets\Builders\CSSBuilder;
use LaunchpadBudAssets\Builders\FetchAssets;
use LaunchpadBudAssets\Builders\JavascriptBuilder;
use LaunchpadFilesystem\FilesystemBase;

class Assets
{
	use FetchAssets;

    /**
     * WordPress filesystem.
     *
     * @var FilesystemBase
     */
    protected $filesystem;

    /**
     * Plugin slug.
     *
     * @var string
     */
    protected $plugin_slug = '';

    /**
     * Assets URL.
     *
     * @var string
     */
    protected $assets_url = '';

    /**
     * Plugin version.
     *
     * @var string
     */
    protected $plugin_version = '';

    /**
     * Plugin launcher file.
     *
     * @var string
     */
    protected $plugin_launcher_file = '';

    /**
     * Entrypoint filename.
	 * @deprecated
     *
     * @var  string
     */
    const ENTRYPOINTS_FILE = 'entrypoints.json';

    /*
     * Manifest filename.
     *
     * @var string
     */
    const MANIFEST_FILE = 'manifest.json';

    /**
     * Instantiate the class.
     *
     * @param FilesystemBase $filesystem
     * @param string $plugin_slug
     * @param string $assets_url
     * @param string $plugin_version
     * @param string $plugin_launcher_file
     */
    public function __construct(FilesystemBase $filesystem, string $plugin_slug, string $assets_url, string $plugin_version, string $plugin_launcher_file)
    {
        $this->filesystem = $filesystem;
        $this->plugin_slug = $plugin_slug;
        $this->assets_url = $assets_url;
        $this->plugin_version = $plugin_version;
        $this->plugin_launcher_file = $plugin_launcher_file;
    }

    /**
     * Enqueue a script.
     *
	 * @deprecated
	 *
     * @param string $key script key.
     * @param string $url script url.
     * @param array $dependencies script dependencies.
     * @param bool $in_footer is the script in the footer.
     *
     * @return void
     */
    public function enqueue_script(string $key, string $url, array $dependencies = [], bool $in_footer = false) {
		$script = $this
			->with_script($url)
			->with_key($key)
			->with_dependencies($dependencies);

		if($in_footer) {
			$script->in_footer();
		}

		$script->enqueue();
    }

    /**
     * Register a script.
     *
	 * @deprecated
	 *
	 * @param string $key script key.
     * @param string $url script url.
     * @param array $dependencies script dependencies.
     * @param bool $in_footer is the script in the footer.
     *
     * @return void
     */
    public function register_script(string $key, string $url, array $dependencies = [], bool $in_footer = false)
    {
		$script = $this
			->with_script($url)
			->with_key($key)
			->with_dependencies($dependencies);

		if($in_footer) {
			$script->in_footer();
		}

		$script->register();
    }

    /**
     * Enqueue style.
     *
	 * @deprecated
	 *
     * @param string $key style key.
     * @param string $url style URL.
     * @param array $dependencies style
     * @param string $media which media the style should display.
     * @return void
     */
    public function enqueue_style(string $key, string $url, array $dependencies = [], string $media = 'all') {
		$this
			->with_style($url)
			->with_key($key)
			->with_dependencies($dependencies)
			->with_media($media)
			->enqueue();
    }

    /**
     * Register style.
     *
	 * @deprecated
	 *
     * @param string $key style key.
     * @param string $url style URL.
     * @param array $dependencies style
     * @param string $media which media the style should display.
     * @return void
     */
    public function register_style(string $key, string $url, array $dependencies = [], string $media = 'all')
    {
		$this
			->with_style($url)
			->with_key($key)
			->with_dependencies($dependencies)
			->with_media($media)
			->register();
    }

    /**
     * Get the real URL from an asset.
     *
     * @param string $url URL from the asset.
     *
     * @return string
     */
    public function get_real_url(string $url) {
        $assets_path = $this->get_assets_path();
        $manifest_path = $assets_path . DIRECTORY_SEPARATOR . self::MANIFEST_FILE;

        if( ! $this->filesystem->exists($manifest_path)) {
            return $url;
        }

        $manifest = json_decode($this->filesystem->get_contents($manifest_path), true);

        if(! $manifest || ! key_exists($url, $manifest)) {
            return $url;
        }

        return $manifest[$url];
    }

    /**
     * Get full key.
     *
	 * @deprecated
	 *
     * @param string $key partial key.
     * @return string
     */
    public function get_full_key(string $key) {
        return $this->plugin_slug . $key;
    }

	/**
	 * Define a javascript asset.
	 *
	 * @param string $url URL from the asset.
	 *
	 * @return JavascriptBuilder
	 */
	public function with_script(string $url): JavascriptBuilder {
		return new JavascriptBuilder($url, $this->plugin_slug, $this->plugin_version, $this->assets_url, $this->plugin_launcher_file, $this->filesystem);
	}

	/**
	 * define a css asset.
	 *
	 * @param string $url URL from the asset.
	 *
	 * @return CSSBuilder
	 */
	public function with_style(string $url): CSSBuilder {
		return new CSSBuilder($url, $this->plugin_slug, $this->plugin_version, $this->assets_url, $this->plugin_launcher_file, $this->filesystem);
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

	/**
	 * Get the plugin slug.
	 *
	 * @return string
	 */
	protected function get_plugin_slug(): string {
		return $this->plugin_slug;
	}

	/**
	 * Get the plugin launcher file.
	 *
	 * @return string
	 */
	protected function get_plugin_launcher_file(): string {
		return $this->plugin_launcher_file;
	}
}
