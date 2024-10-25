<?php

namespace LaunchpadBudAssets\Tests\Unit\inc\Assets;

use LaunchpadBudAssets\Builders\AvailabilityQuery;
use LaunchpadBudAssets\Builders\JavascriptBuilder;
use Mockery;
use LaunchpadBudAssets\Assets;
use LaunchpadFilesystem\FilesystemBase;
use Brain\Monkey\Functions;

use LaunchpadBudAssets\Tests\Unit\TestCase;

/**
 * @covers \LaunchpadBudAssets\Assets::with_script
 */
class Test_withScript extends TestCase {

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
    public function testShouldReturnAsExpected( $config, $expected )
    {
		Functions\when('plugin_dir_url')->justReturn($config['plugin_url']);
		Functions\when('sanitize_key')->returnArg();
		Functions\when('is_tax')->justReturn($expected['is_tax']);

		$this->configureFilesystem($config, $expected);

		if( ! $expected['enqueue']) {
			Functions\expect('wp_enqueue_script')->never();
		} else {
			Functions\expect('wp_enqueue_script');
		}

		if( ! $expected['register']) {
			Functions\expect('wp_register_script')->never();
		} else {
			Functions\expect('wp_register_script');
		}

		$builder = $this->assets->with_script($config['url']);

		$builder->with_key($config['key']);

		if(count($config['taxonomies'])) {
			$builder = $builder->with_query(function (AvailabilityQuery $query) use ($config) {
				foreach ($config['taxonomies'] as $taxonomy) {
					$query->with_taxonomy($taxonomy);
				}
				return $query;
			});
		}

		if($config['enqueue']) {
			$builder = $builder->enqueue();
		}

		if($config['register']) {
			$builder = $builder->register();
		}

		if($config['enqueue'] || $config['register']) {
			$this->assertSame($expected['return'], $builder);
		} else {
			$this->assertInstanceOf($expected['return'], $builder);
		}
    }

	protected function configureFilesystem($config, $expected) {
		if(! $config['called']) {
			return;
		}
		$this->filesystem->expects()->exists($expected['entrypoints_path'])->andReturn($config['called']);
		$this->filesystem->expects()->get_contents($expected['entrypoints_path'])->andReturn($config['content']);
	}
}
