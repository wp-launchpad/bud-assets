<?php
return [
    'budAssetShouldReturnRightPath' => [
        'config' => [
              'url' => 'app.js',
              'exists' => true,
              'content' => json_encode([
                  'app.js' => 'app.hash.js'
              ]),
            'plugin_url' => 'http://example.org/wp-content/plugin'
        ],
        'expected' => [
            'url' => 'app.hash.js',
            'manifest_path' => '/path/wp-content/plugin//assets/manifest.json',
        ]
    ],
    'notBudAssetShouldReturnSame' => [
        'config' => [
            'url' => 'app2.js',
            'exists' => true,
            'content' => json_encode([
                'app.js' => 'app.hash.js'
            ]),
            'plugin_url' => 'http://example.org/wp-content/plugin'
        ],
        'expected' => [
            'url' => 'app2.js',
            'manifest_path' => '/path/wp-content/plugin//assets/manifest.json',
        ]
    ],
    'budAssetWithoutManifestShouldReturnSame' => [
        'config' => [
            'url' => 'app.js',
            'exists' => false,
            'content' => json_encode([
                'app.js' => 'app.hash.js'
            ]),
            'plugin_url' => 'http://example.org/wp-content/plugin'
        ],
        'expected' => [
            'url' => 'app.js',
            'manifest_path' => '/path/wp-content/plugin//assets/manifest.json',
        ]
    ],
    'budAssetWithoutManifestContentShouldReturnSame' => [
        'config' => [
            'url' => 'app.js',
            'exists' => true,
            'content' => '',
            'plugin_url' => 'http://example.org/wp-content/plugin'
        ],
        'expected' => [
            'url' => 'app.js',
            'manifest_path' => '/path/wp-content/plugin//assets/manifest.json',
        ]
    ],
];
