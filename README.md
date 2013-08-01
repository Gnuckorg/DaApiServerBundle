DaApiServerBundle
=================

DaApiServerBundle is a Symfony2's bundle allowing to provide a REST API in a simple and secure way.

Installation
------------

Installation is a quick 2 steps process.

### Step 1: Add in composer

Add the bundle in the composer.json file:

``` js
// composer.json

"require": {
    // ...
    "da/api-server-bundle": "dev-master"
},
```

And update your vendors:

``` bash
composer update      # WIN
composer.phar update # LINUX
```

### Step 2: Declare in the kernel

Declare the bundle in your kernel:

``` php
// app/AppKernel.php

$bundles = array(
    // ...
    new Da\ApiServerBundle\DaApiServerBundle(),
);
```

Check the client API key
------------------------

If you want to check the API token of a client of your API for a route pattern, you must specify it in your security.yml:

``` yaml
# app/config/security.yml
security:
    firewalls:
    	#...

        api:
            pattern:   ^/api
            da_api:    true
            stateless: true

    access_control:
        # You can omit this if /api can be accessed both authenticated and anonymously
        - { path: ^/api, roles: [ IS_AUTHENTICATED_FULLY ] }
```

The URLs under `/api` will authenticate a client of your API with the API token send with the request.
For the time being, the API token must be send in the HTTP header "X-API-Token".

What about the API client side?
-------------------------------

Take a look at the [DaApiClientBundle](https://github.com/Gnuckorg/DaApiClientBundle)!