moneyzaurus-api
===============

API implementation for expense monitoring system [wormhit/moneyzaurus][1].
Code is based on [wormhit/slim-api][2] framework.

Setup
-----------------

Start server:

```php -S localhost:8000```


Run tests:

```vendor/bin/phpunit -c tests/phpunit.xml```

Import db structure:

```mysql -u root < data/app.sql```
