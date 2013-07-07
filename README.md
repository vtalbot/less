# LESS Compiler for Laravel 4

### Installation

Add to your composer.json those lines

    "require": {
        "vtalbot/less": "1.*"
    }

Run `php artisan config:publish vtalbot/less`

Then edit `config.php` in `app/packages/vtalbot/less` to your needs.

Update `app/config/app.php` with:

    'providers' => array(
        ...
        'VTalbot\Less\LessServiceProvider',
    ),

    ...

    'aliases' => array(
        ...
        'Less'            => 'VTalbot\Less\Facades\Less',
    ),

### Usage

    <link rel="stylesheet" href="css/test.css">

If `css/test.css` doesn't exists in the `public` directory, it will search for `test.less` in `app/less` (changeable in `config.php`) directory.
If found, compile it if needed and return the result.

    Less::make('file-in-less-directory');

### Changelog

#### 1.2

- Changed configuration prefix to an array to check more than one path
- Clean up

#### 1.1

- Check if a change has occured in imported less files