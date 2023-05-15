<?php

namespace LaunchpadBudAssets\Tests\Unit\inc\Assets;

use Mockery;
use LaunchpadBudAssets\Assets;
use LaunchpadFilesystem\FilesystemBase;


use LaunchpadBudAssets\Tests\Unit\TestCase;
use Brain\Monkey\Functions;

/**
 * @covers \LaunchpadBudAssets\Assets::enqueue_style
 */
class Test_enqueueStyle extends TestCase {

    /**
     * @var FilesystemBase
     */
    protected $filesystem;

    /**
     * @var string
     */
    protected $plugin_slug;

    /**
     * @var string
     */
    protected $assets_url;

    /**
     * @var string
     */
    protected $plugin_version;

    /**
     * @var string
     */
    protected $plugin_launcher_file;

    /**
     * @var Assets
     */
    protected $assets;

    public function set_up() {
        parent::set_up();
        $this->filesystem = Mockery::mock(FilesystemBase::class);
        $this->plugin_slug = 'plugin_slug';
        $this->assets_url = 'http://example.org/wp-content/plugin/assets';
        $this->plugin_version = '1.0.0';
        $this->plugin_launcher_file = '/path/wp-content/plugin/plugin_launcher_file';

        $this->assets = new Assets($this->filesystem, $this->plugin_slug, $this->assets_url, $this->plugin_version, $this->plugin_launcher_file);
    }

    /**
     * @dataProvider configTestData
     */
    public function testShouldDoAsExpected( $config, $expected )
    {
        Functions\when('plugin_dir_url')->justReturn($config['plugin_url']);
        Functions\when('sanitize_key')->returnArg();

        foreach ($expected['styles'] as $item) {
            Functions\expect('wp_enqueue_style')->with($item['key'], $item['url'], $item['dependencies'], $expected['plugin_version'], $expected['media']);
        }

        $this->configureFilesystem($config, $expected);

        $this->assets->enqueue_style($config['key'], $config['url'], $config['dependencies'], $config['media']);
    }

    protected function configureFilesystem($config, $expected) {
        $this->filesystem->expects()->exists($expected['entrypoints_path'])->andReturn($config['exists']);
        if(! $config['exists']) {
            return;
        }
        $this->filesystem->expects()->get_contents($expected['entrypoints_path'])->andReturn($config['content']);
    }
}
