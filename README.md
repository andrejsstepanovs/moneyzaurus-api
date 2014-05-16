moneyzaurus-api
===============

API implementation for expense monitoring system [wormhit/moneyzaurus][1].
Code is based on [wormhit/slim-api][2] framework.

[![Build Status](https://travis-ci.org/wormhit/moneyzaurus-api.png?branch=master)](https://travis-ci.org/wormhit/moneyzaurus-api) [![Code Quality](https://scrutinizer-ci.com/g/wormhit/moneyzaurus-api/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/wormhit/moneyzaurus-api/) [![Coverage Coverage](https://img.shields.io/coveralls/wormhit/moneyzaurus-api.svg)](https://coveralls.io/r/wormhit/moneyzaurus-api?branch=master) [![License](https://poser.pugx.org/wormhit/slim-api/license.png)](https://packagist.org/packages/wormhit/slim-api)

Setup
-----------------

Start server:

```php -S localhost:8000```


Run tests:

```vendor/bin/phpunit -c tests/phpunit.xml```

Import db structure:

```mysql -u root < data/app.sql```

