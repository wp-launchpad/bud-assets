<?php

namespace LaunchpadBudAssets\Tests\Unit\inc\Assets;

use Mockery;
use LaunchpadBudAssets\Assets;
use LaunchpadFilesystem\FilesystemBase;


use LaunchpadBudAssets\Tests\Unit\TestCase;

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
        $this->plugin_slug = '';
        $this->assets_url = '';
        $this->plugin_version = '';
        $this->plugin_launcher_file = '';

        $this->assets = new Assets($this->filesystem, $this->plugin_slug, $this->assets_url, $this->plugin_version, $this->plugin_launcher_file);
    }

    /**
     * @dataProvider configTestData
     */
    public function testShouldDoAsExpected( $config )
    {
        $this->assets->enqueue_style($config['key'], $config['url'], $config['dependencies'], $config['media']);

    }
}
