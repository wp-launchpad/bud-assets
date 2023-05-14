<?php

namespace LaunchpadBudAssets;

class Assets
{
    protected $plugin_slug = '';

    protected $assets_url = '';

    protected $plugin_version = '';

    protected $plugin_launcher_file = '';

    public function enqueue_script(string $key, string $url, array $dependencies = [], bool $in_footer = false) {
        $bud_dependencies = $this->find_bud_dependencies($url);
        wp_enqueue_script($this->get_full_key($key), $url, $dependencies, $this->plugin_version, $in_footer);
    }

    protected function get_real_url(string $url) {
        if(strpos($url, $this->assets_url) === false) {
           return $url;
        }
        $path = str_replace($this->assets_url, '', $url);
    }

    protected function find_bud_dependencies(string $url): array {

    }

    public function get_full_key(string $key) {
        return $this->plugin_slug . $key;
    }
}
