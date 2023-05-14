<?php

namespace LaunchpadBudAssets;

use LaunchpadFilesystem\FilesystemBase;

class Assets
{
    /**
     * @var FilesystemBase
     */
    protected $filesystem;

    protected $plugin_slug = '';

    protected $assets_url = '';

    protected $plugin_version = '';

    protected $plugin_launcher_file = '';

    const ENTRYPOINTS_FILE = 'entrypoints.json';

    const MANIFEST_FILE = 'manifest.json';

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

    protected function get_real_url(string $url) {
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

    public function get_full_key(string $key) {
        return $this->plugin_slug . $key;
    }

    protected function get_assets_path(): string {
        $plugin_url = plugin_dir_url($this->plugin_launcher_file);
        $plugin_dir = dirname($this->plugin_launcher_file);
        $assets_path = str_replace($plugin_url, '', $this->assets_url);
        $assets_path = $plugin_dir . '/' . $assets_path;
        return str_replace('/', DIRECTORY_SEPARATOR, $assets_path);
    }

    protected function generate_key(string $url): string {
        return $this->plugin_slug . sanitize_key($url);
    }
}
