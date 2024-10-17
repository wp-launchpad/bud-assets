# Bud Assets
A mapper between WordPress and Bud.js assets.

To enqueue a bud.js asset then use the name of the entrypoint to enqueue instead of the URL.

## Manipulate script

Unlike vanilla WordPress, this library uses a builder to enqueue and register scripts.

For that you can use the method `with_script`

### Enqueue script

To enqueue a script it is possible with the method `enqueue` the following way:

```php
$assets
    ->with_script('/app.js')
    ->enqueue();
```

### Register script

To register a script it is possible with the method `register` the following way:

```php
$assets
    ->with_script('/app.js')
    ->register();
```

## Manipulate style

Unlike vanilla WordPress, this library uses a builder to enqueue and register styles.

For that you can use the method `with_style`

### Enqueue style

To enqueue a style it is possible with the method `enqueue` the following way:

```php
$assets
    ->with_style('/app.css')
    ->enqueue();
```

### Register style

To register a style it is possible with the method `register` the following way:

```php
$assets
    ->with_style('/app.css')
    ->register();
```

## Register only on certain conditions

With this library, it is possible to enqueue or register a script or style based on certain conditions.

For that on both builders a method `with_query` is available:

```php
$assets
    ->with_style('/app.css')
    ->with_query(function (\LaunchpadBudAssets\Builders\AvailabilityQuery $query) {
        $query->with_block('my-block')
        return $query;
    })
    ->enqueue();
```

The query offers a couple entities the asset can be paired with:

| Method         | Entity          |
|----------------|-----------------|
| with_block     | Gutenberg block |
| with_shortcode | Shortcode       |
| with_template  | Template        |
| with_taxonomy  | Taxonomy        |
| with_post_type | Post type       |
