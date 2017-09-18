Rumbleship Financial (RFi) php-gateway-client

# Setup
## Environment Composer + PHP
Package Management and linting. 
Composer is to PHP as NPM is to Node.  `composer.json` is to PHP as `package.json` is to Node.

    brew tap homebrew/php
    brew install php56
    brew install composer
    
### PHP version Management? 
Personally I'm trying out [php-version](https://github.com/wilmoore/php-version) for now. 

## Dependencies

Now with composer installed you can run composer to install the package dependencies:

    composer install

## Test

    composer test 
    
## Lint
   
    composer lint
    
    






