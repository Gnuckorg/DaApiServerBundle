DaApiServerBundle
=================

DaApiServerBundle is a Symfony2's bundle allowing to provide a REST API in a simple and secure way.

Installation
------------

Installation is a quick 3 steps process.

### Step 1: Add in composer

Add the bundle in the composer.json file:

``` js
// composer.json

"require": {
    // ...
    "friendsofsymfony/oauth-server-bundle": "dev-master",
    "da/auth-model-bundle": "dev-master",
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
    new Da\AuthModelBundle\DaAuthModelBundle(),
    new Da\ApiServerBundle\DaApiServerBundle(),
);
```

### Step 3: Parameterize a database

Do not forget to parameterize the database for the DaAuthModelBundle where you can find your clients.
You can use the [DaOAuthServerBundle](https://github.com/Gnuckorg/DaOAuthServerBundle) to create your clients.

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
```

The URLs under `/api` will authenticate a client of your API with the API token send with the request.
For the time being, the API token must be send in the HTTP header "X-API-Security-Token".

Documentation
-------------

This bundle have some other features that can help you to develop a REST API documented [here](https://github.com/Gnuckorg/DaApiServerBundle/blob/master/Resources/doc/index.md).

What about the API client side?
-------------------------------

Take a look at the [DaApiClientBundle](https://github.com/Gnuckorg/DaApiClientBundle)!