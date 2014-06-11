moneyzaurus-api
===============

API implementation for expense monitoring system [wormhit/moneyzaurus][1].
Code is based on [wormhit/slim-api][2] framework.

[![Build Status](https://travis-ci.org/wormhit/moneyzaurus-api.png?branch=master)](https://travis-ci.org/wormhit/moneyzaurus-api) [![Code Quality](https://scrutinizer-ci.com/g/wormhit/moneyzaurus-api/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/wormhit/moneyzaurus-api/) [![Coverage Coverage](https://coveralls.io/repos/wormhit/moneyzaurus-api/badge.png?branch=master)](https://coveralls.io/r/wormhit/moneyzaurus-api?branch=master) [![License](https://poser.pugx.org/wormhit/slim-api/license.png)](https://packagist.org/packages/wormhit/slim-api)


Install
-----------------

```
sudo docker pull wormhit/moneyzaurus-api
sudo docker run -d wormhit/moneyzaurus-api
sudo docker ps
sudo docker inspect XXXX | grep IPAddress
```

API Docs
-----------------

### show api info
```
curl --request GET 'http://172.17.0.2/'
# {"version":"V1","timestamp":1402504733,"process":0.00081992149353027}
```

### register new user
```
curl --request POST 'http://172.17.0.2/user/register' --data 'username=email@email.com&password=PASS123&locale=en_EN&language=en_EN&timezone=Europe/Berlin'
# {"success":true,"data":{"id":2,"email":"email@email.com","name":null,"role":"user","language":"en_EN","locale":"en_EN","timezone":"Europe\/Berlin","state":1},"timestamp":1402503495,"process":1.6391539573669}
```

### login
```
curl --request POST 'http://172.17.0.2/authenticate/login' --data 'username=email@email.com&password=PASS123'
# {"success":true,"data":{"id":2,"email":"email@email.com","token":"212WRaUZuefGyE_SvJ3mkqHFXB4wgh6No","expires":"Jun 11, 2015, 6:19:15 PM","expires_timestamp":1434039555},"timestamp":1402503555,"process":1.6310708522797}
```

### update user
```
curl --request POST 'http://172.17.0.2/user/update?token=212WRaUZuefGyE_SvJ3mkqHFXB4wgh6No' --data 'name=Test User'
# {"success":true,"timestamp":1402504507,"process":0.45225811004639}
```

### get user data
```
curl --request GET 'http://172.17.0.2/user/data?token=212WRaUZuefGyE_SvJ3mkqHFXB4wgh6No'
# {"success":true,"data":{"id":2,"email":"email@email.com","name":"Test User","role":"user","language":"en_EN","locale":"en_EN","timezone":"Europe\/Berlin","state":1},"timestamp":1402504528,"process":0.00076603889465332}
```

### create new transactions
```
curl --request POST 'http://172.17.0.2/transactions/add?token=212WRaUZuefGyE_SvJ3mkqHFXB4wgh6No' --data 'item=apple&group=food&currency=EUR&price=1.00&date=2014-05-10'
# {"success":true,"data":{"id":4},"timestamp":1402503704,"process":0.60302686691284}

curl --request POST 'http://172.17.0.2/transactions/add?token=212WRaUZuefGyE_SvJ3mkqHFXB4wgh6No' --data 'item=apple&group=food&currency=EUR&price=1.00&date=2014-03-10'
# {"success":true,"data":{"id":5},"timestamp":1402504112,"process":0.46021199226379}

curl --request POST 'http://172.17.0.2/transactions/add?token=212WRaUZuefGyE_SvJ3mkqHFXB4wgh6No' --data 'item=apple&group=food&currency=EUR&price=1.00&date=2014-03-20'
# {"success":true,"data":{"id":6},"timestamp":1402504140,"process":0.47461891174316}

curl --request POST 'http://172.17.0.2/transactions/add?token=212WRaUZuefGyE_SvJ3mkqHFXB4wgh6No' --data 'item=apple&group=food&currency=EUR&price=1.00&date=2014-03-25'
#{"success":true,"data":{"id":7},"timestamp":1402504172,"process":0.43746590614319}
```

### fetch all transactions
```
curl --request GET 'http://172.17.0.2/transactions/list?token=212WRaUZuefGyE_SvJ3mkqHFXB4wgh6No'
# {"success":true,"count":4,"data":[{"id":7,"dateTransaction":"3\/25\/14","dateCreated":"6\/11\/14, 6:29:32 PM","amount":100,"currency":"EUR","currencyName":"Euro","currencySymbol":"€","email":"email@email.com","role":"user","userId":2,"locale":"en_EN","timezone":"Europe\/Berlin","userName":null,"itemName":"apple","itemId":2,"groupName":"food","groupId":2,"date":"2014-03-25","created":"2014-06-11 16:29:32","price":"1.00","money":"€1.00"},{"id":6,"dateTransaction":"3\/20\/14","dateCreated":"6\/11\/14, 6:28:59 PM","amount":100,"currency":"EUR","currencyName":"Euro","currencySymbol":"€","email":"email@email.com","role":"user","userId":2,"locale":"en_EN","timezone":"Europe\/Berlin","userName":null,"itemName":"apple","itemId":2,"groupName":"food","groupId":2,"date":"2014-03-20","created":"2014-06-11 16:28:59","price":"1.00","money":"€1.00"},{"id":5,"dateTransaction":"3\/10\/14","dateCreated":"6\/11\/14, 6:28:32 PM","amount":100,"currency":"EUR","currencyName":"Euro","currencySymbol":"€","email":"email@email.com","role":"user","userId":2,"locale":"en_EN","timezone":"Europe\/Berlin","userName":null,"itemName":"apple","itemId":2,"groupName":"food","groupId":2,"date":"2014-03-10","created":"2014-06-11 16:28:32","price":"1.00","money":"€1.00"},{"id":4,"dateTransaction":"5\/10\/14","dateCreated":"6\/11\/14, 6:25:12 PM","amount":100,"currency":"EUR","currencyName":"Euro","currencySymbol":"€","email":"email@email.com","role":"user","userId":2,"locale":"en_EN","timezone":"Europe\/Berlin","userName":null,"itemName":"banana","itemId":3,"groupName":"food","groupId":3,"date":"2014-05-10","created":"2014-06-11 16:25:12","price":"1.00","money":"€1.00"}],"timestamp":1402504237,"process":0.02446985244751}
```

### update transaction
```
curl --request POST 'http://172.17.0.2/transactions/update/4?token=212WRaUZuefGyE_SvJ3mkqHFXB4wgh6No' --data 'item=banana'
# {"success":true,"data":{"id":4},"timestamp":1402503913,"process":0.47043800354004}
```

### fetch transaction by id
```
curl --request GET 'http://172.17.0.2/transactions/id/4?token=212WRaUZuefGyE_SvJ3mkqHFXB4wgh6No'
# {"success":true,"data":{"id":4,"dateTransaction":"5\/10\/14","dateCreated":"6\/11\/14, 6:25:12 PM","amount":100,"currency":"EUR","currencyName":"Euro","currencySymbol":"€","email":"email@email.com","role":"user","userId":2,"locale":"en_EN","timezone":"Europe\/Berlin","userName":null,"itemName":"banana","itemId":3,"groupName":"food","groupId":2,"date":"2014-05-10","created":"2014-06-11 16:25:12","price":"1.00","money":"€1.00"},"timestamp":1402503968,"process":0.02384614944458}
```

### show all groups
```
curl --request GET 'http://172.17.0.2/distinct/groups?token=212WRaUZuefGyE_SvJ3mkqHFXB4wgh6No'
# {"success":true,"count":1,"data":["food"],"timestamp":1402504269,"process":0.0046210289001465}
```

### show all items
```
curl --request GET 'http://172.17.0.2/distinct/items?token=212WRaUZuefGyE_SvJ3mkqHFXB4wgh6No'
# {"success":true,"count":2,"data":["apple","banana"],"timestamp":1402504290,"process":0.0096001625061035}
```

### predict group
```
curl --request POST 'http://172.17.0.2/predict/group?token=212WRaUZuefGyE_SvJ3mkqHFXB4wgh6No' --data 'item=apple'
# {"success":true,"count":1,"data":["food"],"timestamp":1402504336,"process":0.028403043746948}
```

### predict price
```
curl --request POST 'http://172.17.0.2/predict/price?token=212WRaUZuefGyE_SvJ3mkqHFXB4wgh6No' --data 'item=apple&group=food'
# {"success":true,"count":1,"data":[{"amount":100,"locale":"en_EN","timezone":"Europe\/Berlin","currency":"EUR","currencyName":"Euro","currencySymbol":"€","usedCount":"3","price":"1.00","money":"€1.00"}],"timestamp":1402504365,"process":0.021919965744019}
```

### delete transaction
```
curl --request DELETE 'http://172.17.0.2/transactions/remove/4?token=212WRaUZuefGyE_SvJ3mkqHFXB4wgh6No'
# {"success":true,"timestamp":1402504662,"process":0.47580194473267}
```

### logout
```
curl --request GET 'http://172.17.0.2/authenticate/logout?token=212WRaUZuefGyE_SvJ3mkqHFXB4wgh6No'
# {"success":true,"timestamp":1402504816,"process":0.56578898429871}
```

### password recovery
```
/authenticate/password-recovery
```

### connections between users
```
/connection/list
```

```
/connection/add
```

```
/connection/reject/:id
```

```
/connection/accept/:id
```

[1]: https://github.com/wormhit/moneyzaurus
[2]: https://github.com/wormhit/slim-api