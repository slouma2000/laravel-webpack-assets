# Laravel Webpack Assets
Package that allows you to include assets from json file, generated by [Webpack Manifest Plugin](https://github.com/danethurber/webpack-manifest-plugin)

<p align="center">
    <img src="https://travis-ci.org/malyusha/laravel-webpack-assets.svg?branch=master">
</p>

## Installation

Require the latest version of package using [Composer](https://getcomposer.org/) 

`$ composer require malyusha/laravel-webpack-assets`

Next, you need to add service provider into your `config/app.php` file in `providers` section:
* `\Malyusha\WebpackAssets\WebpackAssetsServiceProvider::class`

You can add the WebpackAssets facade in `facades` section:
* `'WebpackAssets' => \Malyusha\WebpackAssets\Facade::class`


## Configuration

To change package configuration you need to publish configuration files:

`$ php artisan vendor:publish`

This will publish `assets.php` file inside your `config` directory.
Configuration file has a few options:

* `file` - path to manifest.json file, relative to `public` directory;
* `stacks` - array, containing names of [blade stacks](https://laravel.com/docs/5.4/blade#stacks). Need only if you are using custom [@assets blade directive](#assets-directive), provided by this package.

## Usage

Package provides helper functions to include script and style HTML elements inside blade templates:

* `webpack_script($script)` - will generate `<script src="path_to_script_from_manifest_file"></script>`;
* `webpack_style($script`- will do the same as `webpack_script` but for style;
* `webpack($chunkName = null)` - will return instance of `Asset` class if no arguments provided, otherwise returns asset url with host.

## Examples

Let's imagine, that you have generated `manifest.json` file with such content:
```json
{
  "app.js": "/assets/1b53147322421b069bf1.js",
  "auth.background.png": "/assets/e60cc0de08eee2256222218425eaa943.png",
  "auth.login.css": "/assets/css/20a7e7e51e1f142b2b1f.css",
  "auth.login.js": "/assets/20a7e7e51e1f142b2b1f.js",
  "auth.password.forgot.css": "/assets/css/280c079c4407fbd0fdca.css",
  "auth.password.forgot.js": "/assets/280c079c4407fbd0fdca.js"
}
```

### Retrieving paths

```php
$webpackAssets = webpack();
// Full urls with hostname
echo $webpackAssets->url('app.js'); // http://host.dev/assets/1b53147322421b069bf1.js
echo $webpackAssets->url('app.css'); // http://host.dev/assets/css/20a7e7e51e1f142b2b1f.css

// Relative paths
echo $webpackAssets->path('app.js'); // /assets/1b53147322421b069bf1.js

```

### Using in blade templates

Whenever you want to output your asset simply write:

```blade
{!! webpack_script('app.js') !!}

// or

{!! webpack()->image('background.png', 'Background') !!} 
// Output: <img alt="Background" src="http://host.dev/assets/e60cc0de08eee2256222218425eaa943.png">
```

### Assets directive

Custom blade directive called `@assets` provided in this package. It's usefull when you include separated style/script files for each page. You may be familiar with such construction (`@push` from `laravel/5.4`, you may have used `@yield` to achieve the same goal):

```blade
{{-- Somewhere in the header --}}
@stack('styles')

{{-- Somewhere in the footer --}}
@stack('scripts.before')

<script src="/assets/app.js"></script>

@stack('scripts')

{{-- And then on auth page --}}
@push('scripts')
  <script src='/assets/auth.js'></script>
@endpush

@push('styles')
  <link rel="stylesheet" href="/assets/auth.css">
@endpush
```

Instead of writing 2 blocks of push you can simply write:

* This will do the same as 2 @push directives only for scripts/styles:
```blade
@assets('auth.js', 'auth.css')
```
* Include only js file
```blade
@assets('auth.js')
```
* Include only css file
```blade
@assets(null, 'auth.css') 
```

To use `@assets` directive set `stacks` configuration inside `config/assets.php` file to your `stacks` keys. 