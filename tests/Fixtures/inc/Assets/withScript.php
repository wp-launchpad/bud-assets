<?php

use LaunchpadBudAssets\Builders\JavascriptBuilder;

return [
    'callWithoutActionShouldReturnBuilderWithoutDoingSomethingElse' => [
        'config' => [
			'plugin_url' => 'http://example.org/wp-content/plugin',
			'enqueue' => false,
			'register' => false,
			'key' => '',
			'url' => 'http://example.org/js/example.js',
			'taxonomies' => [],
			'called' => false,
			'content' => json_encode([
				'app' => [
					'js' => [
						'dependency.js',
						'app.hash.js',
					]
				]
			])
        ],
        'expected' => [
			'is_tax' => true,
			'entrypoints_path' => '/path/wp-content/plugin//assets/entrypoints.json',
			'enqueue' => false,
			'register' => false,
			'return' => JavascriptBuilder::class,
        ]
    ],
	'enqueueShouldCallQueueFunction' => [
		'config' => [
			'plugin_url' => 'http://example.org/wp-content/plugin',
			'enqueue' => true,
			'register' => false,
			'key' => '',
			'url' => 'http://example.org/js/example.js',
			'taxonomies' => [],
			'called' => true,
			'content' => json_encode([
				'app' => [
					'js' => [
						'dependency.js',
						'app.hash.js',
					]
				]
			])
		],
		'expected' => [
			'is_tax' => true,
			'entrypoints_path' => '/path/wp-content/plugin//assets/entrypoints.json',
			'enqueue' => true,
			'register' => false,
			'return' => 'plugin_slughttp://example.org/js/example.js',
		]
	],
	'registerShouldCallRegisterFunction' => [
		'config' => [
			'plugin_url' => 'http://example.org/wp-content/plugin',
			'enqueue' => false,
			'register' => true,
			'key' => '',
			'url' => 'http://example.org/js/example.js',
			'taxonomies' => [],
			'called' => true,
			'content' => json_encode([
				'app' => [
					'js' => [
						'dependency.js',
						'app.hash.js',
					]
				]
			])
		],
		'expected' => [
			'is_tax' => true,
			'entrypoints_path' => '/path/wp-content/plugin//assets/entrypoints.json',
			'enqueue' => false,
			'register' => true,
			'return' => 'plugin_slughttp://example.org/js/example.js',
		]
	],
	'affectKeyAndRegisterShouldUseNewKey' => [
		'config' => [
			'plugin_url' => 'http://example.org/wp-content/plugin',
			'enqueue' => false,
			'register' => true,
			'key' => 'my_key',
			'url' => 'http://example.org/js/example.js',
			'taxonomies' => [],
			'called' => true,
			'content' => json_encode([
				'app' => [
					'js' => [
						'dependency.js',
						'app.hash.js',
					]
				]
			])
		],
		'expected' => [
			'is_tax' => true,
			'entrypoints_path' => '/path/wp-content/plugin//assets/entrypoints.json',
			'enqueue' => false,
			'register' => true,
			'return' => 'plugin_slugmy_key',
		]
	],
	'affectKeyAndEnqueueShouldCallQueueFunction' => [
		'config' => [
			'plugin_url' => 'http://example.org/wp-content/plugin',
			'enqueue' => true,
			'register' => false,
			'key' => 'my_key',
			'url' => 'http://example.org/js/example.js',
			'called' => true,
			'taxonomies' => [],
			'content' => json_encode([
				'app' => [
					'js' => [
						'dependency.js',
						'app.hash.js',
					]
				]
			])
		],
		'expected' => [
			'is_tax' => true,
			'entrypoints_path' => '/path/wp-content/plugin//assets/entrypoints.json',
			'enqueue' => true,
			'register' => false,
			'return' => 'plugin_slugmy_key',
		]
	],
	'registerWithAQueryAndWrongPageShouldNotRegister' => [
		'config' => [
			'plugin_url' => 'http://example.org/wp-content/plugin',
			'enqueue' => false,
			'register' => true,
			'key' => 'my_key',
			'url' => 'http://example.org/js/example.js',
			'called' => false,
			'taxonomies' => [
				'taxonomy'
			],
			'content' => json_encode([
				'app' => [
					'js' => [
						'dependency.js',
						'app.hash.js',
					]
				]
			])
		],
		'expected' => [
			'is_tax' => false,
			'entrypoints_path' => '/path/wp-content/plugin//assets/entrypoints.json',
			'enqueue' => false,
			'register' => false,
			'return' => '',
		]
	],
	'registerWithAQueryAndRightPageShouldRegister' => [
		'config' => [
			'plugin_url' => 'http://example.org/wp-content/plugin',
			'enqueue' => false,
			'register' => true,
			'key' => 'my_key',
			'url' => 'http://example.org/js/example.js',
			'called' => true,
			'taxonomies' => [
				'taxonomy'
			],
			'content' => json_encode([
				'app' => [
					'js' => [
						'dependency.js',
						'app.hash.js',
					]
				]
			])
		],
		'expected' => [
			'is_tax' => true,
			'entrypoints_path' => '/path/wp-content/plugin//assets/entrypoints.json',
			'enqueue' => false,
			'register' => true,
			'return' => 'plugin_slugmy_key',
		]
	],
	'enqueueWithAQueryAndWrongPageShouldNotEnqueue' => [
		'config' => [
			'plugin_url' => 'http://example.org/wp-content/plugin',
			'enqueue' => true,
			'register' => false,
			'key' => 'my_key',
			'url' => 'http://example.org/js/example.js',
			'called' => false,
			'taxonomies' => [
				'taxonomy'
			],
			'content' => json_encode([
				'app' => [
					'js' => [
						'dependency.js',
						'app.hash.js',
					]
				]
			])
		],
		'expected' => [
			'is_tax' => false,
			'entrypoints_path' => '/path/wp-content/plugin//assets/entrypoints.json',
			'enqueue' => false,
			'register' => false,
			'return' => '',
		]
	],
	'enqueueWithAQueryAndRightPageShouldEnqueue' => [
		'config' => [
			'plugin_url' => 'http://example.org/wp-content/plugin',
			'enqueue' => true,
			'register' => false,
			'key' => 'my_key',
			'url' => 'http://example.org/js/example.js',
			'called' => true,
			'taxonomies' => [
				'taxonomy'
			],
			'content' => json_encode([
				'app' => [
					'js' => [
						'dependency.js',
						'app.hash.js',
					]
				]
			])
		],
		'expected' => [
			'is_tax' => true,
			'entrypoints_path' => '/path/wp-content/plugin//assets/entrypoints.json',
			'enqueue' => true,
			'register' => false,
			'return' => 'plugin_slugmy_key',
		]
	],
];
