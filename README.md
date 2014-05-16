moneyzaurus-api
===============

API implementation for expense monitoring system [wormhit/moneyzaurus][1].
Code is based on [wormhit/slim-api][2] framework.

[![Build Status](https://travis-ci.org/wormhit/moneyzaurus-api.png?branch=master)](https://travis-ci.org/wormhit/moneyzaurus-api) [![Scrutinizer Quality Score](https://scrutinizer-ci.com/g/wormhit/moneyzaurus-api/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/wormhit/moneyzaurus-api/) [![Code Coverage](https://coveralls.io/repos/wormhit/moneyzaurus-api/badge.png?branch=master)](https://coveralls.io/r/wormhit/moneyzaurus-api?branch=master) [![License](https://poser.pugx.org/wormhit/slim-api/license.png)](https://packagist.org/packages/wormhit/slim-api)

Setup
-----------------

Start server:

```php -S localhost:8000```


Run tests:

```vendor/bin/phpunit -c tests/phpunit.xml```

Import db structure:

```mysql -u root < data/app.sql```

