# LESS Compiler for Laravel 4 (Illuminate)

### Installation

Add to your composer.json those lines

    "require": {
        "vtalbot/less": "1.*"
    }

Run `php artisan config:publish vtalbot/less`

Then edit `config.php` in `app/packages/vtalbot/less` to your needs.

Add `'VTalbot\Less\LessServiceProvider',` to `providers` in `app/config/app.php`
and `'Less' => 'VTalbot\Less\Facades\Less',` to `aliases` in `app/config/app.php`

### Usage

    <link rel="stylesheet" href="css/test.css">

If `css/test.css` doesn't exists in the `public` directory, it will search for `test.less` in `app/less` directory.
If found, compile it if needed and return the result.

    Less::make('file-in-less-directory');

### Changelog

#### 1.1.0

- Check if a change has occured in imported less files