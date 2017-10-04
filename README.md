Rumbleship Financial (RFi) php-gateway-client

# Setup
## Environment Composer + PHP
Package Management and linting.
Composer is to PHP as NPM is to Node.`composer.json` is to PHP as `package.json` is to Node.

### PHP and versioning on a Mac
Personally I'm using [php-version](https://github.com/wilmoore/php-version) for
php version management.  Command line commands for getting this done:

    brew tap homebrew/homebrew-php
    brew install php56
    brew unlink php56
    brew install php-version
    source $(brew --prefix php-version)/php-version.sh && php-version 5

To use the `php-version` command going forward, copy the last line into your `.zshrc` or `.bashrc`;
Most of this is just duplicating directions into one place for (hopefully) ease of use.

### Composer
Composer is to PHP as NPM is to Node.`composer.json` is to PHP as `package.json` is to Node.

    brew install composer

Now with composer installed you can run composer commands such as:

    composer install
    composer test
    composer lint

Composer is setup to [generate an autoload file](https://getcomposer.org/doc/01-basic-usage.md#autoloading)

    composer dump-autoload
