<?php

namespace LaunchpadBudAssets;

use LaunchpadFilesystem\FilesystemBase;

class Assets
{
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
     * Assets path.
     *
     * @var string
     */
    protected $assets_path = '';


    const ENTRYPOINTS_FILE = 'entrypoints.json';

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
     * @param string $key script key.
     * @param string $url script url.
     * @param array $dependencies script dependencies.
     * @param bool $in_footer is the script in the footer.
     *
     * @return void
     */
    public function enqueue_script(string $key, string $url, array $dependencies = [], bool $in_footer = false) {
        $bud_dependencies = $this->find_bud_dependencies($url);
        if(count($bud_dependencies) === 0) {
            $bud_dependencies = [
                $url,
            ];
        }

        $last_url = array_pop($bud_dependencies);

        foreach ($bud_dependencies as $bud_dependency) {
            $full_key = $this->generate_key($bud_dependency);
            wp_enqueue_script($full_key, $bud_dependency, $dependencies, $this->plugin_version, $in_footer);
            $dependencies []= $full_key;
        }

        wp_enqueue_script($this->get_full_key($key), $last_url, $dependencies, $this->plugin_version, $in_footer);
    }

    /**
     * Enqueue style.
     *
     * @param string $key style key.
     * @param string $url style URL.
     * @param array $dependencies style
     * @param string $media which media the style should display.
     * @return void
     */
    public function enqueue_style(string $key, string $url, array $dependencies = [], string $media = 'all') {
        $bud_dependencies = $this->find_bud_dependencies($url);
        if(count($bud_dependencies) === 0) {
            $bud_dependencies = [
                $url,
            ];
        }

        $last_url = array_pop($bud_dependencies);

        foreach ($bud_dependencies as $bud_dependency) {
            $full_key = $this->generate_key($bud_dependency);
            wp_enqueue_style($full_key, $bud_dependency, $dependencies, $this->plugin_version, $media);
            $dependencies []= $full_key;
        }

        wp_enqueue_style($this->get_full_key($key), $last_url, $dependencies, $this->plugin_version, $media);
    }

    /**
     * Get the real URL from an asset.
     *
     * @param string $url URL from the asset.
     *
     * @return string|mixed
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
     * Find dependencies from a bud asset.
     *
     * @param string $url asset URL.
     * @return array
     */
    protected function find_bud_dependencies(string $url): array {
        $url_parts = explode('.', $url);
        $assets_path = $this->get_assets_path();
        $entrypoints_path = $assets_path . DIRECTORY_SEPARATOR . self::ENTRYPOINTS_FILE;
        if( ! $this->filesystem->exists($entrypoints_path)) {
            return [];
        }

        $entrypoints = json_decode($this->filesystem->get_contents($entrypoints_path), true);
        foreach ($url_parts as $part) {
            if(! is_array($entrypoints) || ! key_exists($part, $entrypoints)) {
                return [];
            }

            $entrypoints = $entrypoints[$part];
        }

        $entrypoints = array_map(function ($entrypoint) {
            return $this->assets_url . DIRECTORY_SEPARATOR . $entrypoint;
        }, $entrypoints);

        return $entrypoints;
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
        $assets_path = $plugin_dir . '/' . $assets_path;
        $this->assets_path = str_replace('/', DIRECTORY_SEPARATOR, $assets_path);

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
        return $this->plugin_slug . sanitize_key($url);
    }
}
