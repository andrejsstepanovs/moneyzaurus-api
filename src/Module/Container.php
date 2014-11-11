<?php

namespace Api\Module;

use SlimApi\Kernel\Container as KernelContainer;
use Zend\Crypt\Password\Bcrypt as PasswordCrypt;

/**
 * Class Container
 *
 * @package Api
 */
class Container extends KernelContainer
{
    const ENTITY_MANAGER           = 'entity.manager';
    const DOCTRINE_LOGGER          = 'doctrine.logger';
    const PASSWORD_CRYPT           = 'password.crypt';

    const SERVICE_TIME             = 'service.time';
    const SERVICE_JSON             = 'service.json';
    const SERVICE_LOCALE           = 'service.locale';
    const SERVICE_ACL              = 'service.acl';

    const TRANSACTION_DATA         = 'transaction.transactions';
    const TRANSACTION_SAVE         = 'transaction.save';
    const TRANSACTION_UPDATE       = 'transaction.update';
    const TRANSACTION_REMOVE       = 'transaction.remove';
    const TRANSACTION_VALIDATE     = 'transaction.validate';
    const TRANSACTION_MONEY        = 'transaction.money';
    const TRANSACTION_DATE         = 'transaction.date';

    const ITEMS_DATA               = 'items.data';
    const GROUPS_DATA              = 'groups.data';

    const CHART_PIE                = 'chart.pie';

    const USER_DATA                = 'user.data';
    const USER_SAVE                = 'user.save';

    const CONNECTION_SAVE          = 'connection.save';
    const CONNECTION_DATA          = 'connection.data';

    const PREDICT_GROUP            = 'predict.group';
    const PREDICT_PRICE            = 'predict.price';

    const AUTHORIZATION_TOKEN      = 'authorization.token';
    const AUTHORIZATION_CRYPT      = 'authorization.crypt';

    const EMAIL_VALIDATOR          = 'email.validator';
    const EMAIL_MAILER             = 'email.mailer';
    const EMAIL_TRANSPORT          = 'email.transport';

    const EMAIL_MESSAGE_RECOVERY   = 'email.message.recovery';
    const EMAIL_MESSAGE_CONNECTION = 'email.message.connection';

    const MIDDLEWARE_AUTHORIZATION = 'middleware.authorization';
    const MIDDLEWARE_PROCESS_TIME  = 'middleware.processTime';
    const MIDDLEWARE_JSON          = 'middleware.json';
    const MIDDLEWARE_HEADERS       = 'middleware.headers';

    const LOGGER                   = 'logger';
    const LOGGER_HANDLER           = 'logger.handler';

    /**
     * @return void
     */
    public function initialize()
    {
        parent::initialize();

        $this->initEntityManager();
        $this->initMiddleware();
        $this->initServices();
        $this->initControllers();
    }

    private function initServices()
    {
        $this->initServiceMain();
        $this->initServiceUser();
        $this->initServiceConnection();
        $this->initServiceChart();
        $this->initServiceTransaction();
        $this->initServiceItems();
        $this->initServiceGroups();
        $this->initServiceAuthorization();
        $this->initServicePredict();
        $this->initServiceEmail();
    }

    private function initServiceMain()
    {
        $this[self::SERVICE_TIME] = function () {
            $time = new \Api\Service\Time();

            return $time;
        };

        $this[self::SERVICE_JSON] = function () {
            $json = new \Api\Service\Json();

            return $json;
        };

        $this[self::SERVICE_LOCALE] = function () {
            $locale = new \Api\Service\Locale();

            return $locale;
        };

        $this[self::SERVICE_ACL] = function () {
            $acl = new \Api\Service\Acl();
            $acl->setAcl(new \Zend\Permissions\Acl\Acl());

            return $acl;
        };

        $this[self::LOGGER_HANDLER] = function () {
            $config  = $this->getConfig()->get(Config::LOG);
            $handler = empty($config['file']) ? null : new \Monolog\Handler\StreamHandler($config['file']);

            return $handler;
        };

        $this[self::LOGGER] = function () {
            $logger  = new \Monolog\Logger('app');
            $handler = $this->get(self::LOGGER_HANDLER);
            if ($handler) {
                $logger->pushHandler($handler);
            }

            return $logger;
        };
    }

    private function initServiceUser()
    {
        $this[self::USER_DATA] = function () {
            $data = new \Api\Service\User\Data();
            $data->setUser(
                 $this->get(self::ENTITY_MANAGER)->getRepository('Api\Entities\User')
            );

            return $data;
        };

        $this[self::USER_SAVE] = function () {
            $save = new \Api\Service\User\Save();
            $save->setEntityManager($this->get(self::ENTITY_MANAGER));

            return $save;
        };
    }

    private function initServiceEmail()
    {
        $this[self::EMAIL_VALIDATOR] = function () {
            return new \Egulias\EmailValidator\EmailValidator();
        };

        $this[self::EMAIL_TRANSPORT] = function () {
            $config = $this->getConfig()->get(Config::EMAIL);

            if ($config['test']) {
                $transport = new \Tests\Stub\TestTransport();
            } else {
                $transport = \Swift_SmtpTransport::newInstance(
                    $config['host'],
                    $config['port'],
                    $config['security']
                );
                $transport->setUsername($config['username'])
                          ->setPassword($config['password']);
            }

            return $transport;
        };

        $this[self::EMAIL_MAILER] = function () {
            return \Swift_Mailer::newInstance($this->get(self::EMAIL_TRANSPORT));
        };

        $this[self::EMAIL_MESSAGE_RECOVERY] = function () {
            $email = new \Api\Service\Email\Messages\PasswordRecovery();
            $email->setSender($this->getConfig()->get(Config::EMAIL)['username']);

            return $email;
        };

        $this[self::EMAIL_MESSAGE_CONNECTION] = function () {
            $email = new \Api\Service\Email\Messages\ConnectionInvitation();
            $email->setSender($this->getConfig()->get(Config::EMAIL)['username']);

            return $email;
        };
    }

    private function initServicePredict()
    {
        $this[self::PREDICT_GROUP] = function () {
            $group = new \Api\Service\Predict\Group();
            $group->setEntityManager($this->get(self::ENTITY_MANAGER));
            return $group;
        };

        $this[self::PREDICT_PRICE] = function () {
            $group = new \Api\Service\Predict\Price();
            $group->setEntityManager($this->get(self::ENTITY_MANAGER));
            return $group;
        };
    }

    private function initServiceConnection()
    {
        $this[self::CONNECTION_SAVE] = function () {
            $save = new \Api\Service\Connection\Save();
            $save->setEntityManager($this->get(self::ENTITY_MANAGER));

            return $save;
        };

        $this[self::CONNECTION_DATA] = function () {
            $entityManager = $this->get(self::ENTITY_MANAGER);
            $data = new \Api\Service\Connection\Data();
            $data->setConnectionRepository($entityManager->getRepository('Api\Entities\Connection'));
            $data->setLocale($this->get(self::SERVICE_LOCALE));
            $data->setUserData($this->get(self::USER_DATA));
            $data->setEmailValidator($this->get(self::EMAIL_VALIDATOR));

            return $data;
        };

    }

    private function initServiceChart()
    {
        $this[self::CHART_PIE] = function () {
            $entityManager = $this->get(self::ENTITY_MANAGER);
            $pie = new \Api\Service\Chart\Pie();
            $pie->setEntityManager($entityManager);
            $pie->setLocale($this->get(self::SERVICE_LOCALE));
            $pie->setTransactionEntity(
                $entityManager->getRepository('Api\Entities\Transaction')
            );

            return $pie;
        };
    }

    private function initServiceTransaction()
    {
        $this[self::TRANSACTION_VALIDATE] = function () {
            $validate = new \Api\Service\Transaction\Validate();
            return $validate;
        };

        $this[self::TRANSACTION_DATA] = function () {
            $entityManager = $this->get(self::ENTITY_MANAGER);
            $data = new \Api\Service\Transaction\Data();
            $data->setEntityManager($entityManager);
            $data->setLocale($this->get(self::SERVICE_LOCALE));
            $data->setTransactionEntity(
                 $entityManager->getRepository('Api\Entities\Transaction')
            );
            return $data;
        };

        $this[self::TRANSACTION_SAVE] = function () {
            $entityManager = $this->get(self::ENTITY_MANAGER);

            $create = new \Api\Service\Transaction\Save();
            $create->setEntityManager($entityManager);
            $create->setItemEntity($entityManager->getRepository('Api\Entities\Item'));
            $create->setGroupEntity($entityManager->getRepository('Api\Entities\Group'));
            $create->setCurrencyEntity($entityManager->getRepository('Api\Entities\Currency'));

            return $create;
        };

        $this[self::TRANSACTION_REMOVE] = function () {
            $remove = new \Api\Service\Transaction\Remove();
            $remove->setEntityManager($this->get(self::ENTITY_MANAGER));
            return $remove;
        };

        $this[self::TRANSACTION_MONEY] = function () {
            $money = new \Api\Service\Transaction\Money();
            return $money;
        };

        $this[self::TRANSACTION_DATE] = function () {
            $date = new \Api\Service\Transaction\Date();
            return $date;
        };
    }

    private function initServiceItems()
    {
        $this[self::ITEMS_DATA] = function () {
            $entityManager = $this->get(self::ENTITY_MANAGER);
            $data = new \Api\Service\Items\Data();
            $data->setEntityManager($entityManager);

            return $data;
        };
    }

    private function initServiceGroups()
    {
        $this[self::GROUPS_DATA] = function () {
            $entityManager = $this->get(self::ENTITY_MANAGER);
            $data = new \Api\Service\Groups\Data();
            $data->setEntityManager($entityManager);

            return $data;
        };
    }

    private function initServiceAuthorization()
    {
        $this[self::PASSWORD_CRYPT] = function () {
            $crypt = new PasswordCrypt();

            return $crypt->setCost($this->getConfig()->get(Config::PASSWORD_DEFAULT_COST));
        };

        $this[self::AUTHORIZATION_CRYPT] = function () {
            $crypt = new \Api\Service\Authorization\Crypt();

            return $crypt->setCrypt($this->get(self::PASSWORD_CRYPT));
        };

        $this[self::AUTHORIZATION_TOKEN] = function () {
            $token = new \Api\Service\Authorization\Token;
            $token->setEntityManager($this->get(self::ENTITY_MANAGER));
            $token->setAccessToken(new \Api\Entities\AccessToken());
            $token->setTime($this->get(self::SERVICE_TIME));

            $securityData = $this->getConfig()->get(Config::SECURITY);
            $token->setTokenInterval($securityData['token_interval']);

            return $token;
        };
    }

    private function initControllers()
    {
        $this['controller.index.index'] = function () {
            return new \Api\Controller\Index\IndexController();
        };

        $this->initControllerAuthenticate();
        $this->initControllerTransactions();
        $this->initControllerConnection();
        $this->initControllerDistinct();
        $this->initControllerPredict();
        $this->initControllerUser();
        $this->initControllerChart();
    }

    private function initControllerUser()
    {
        $this['controller.user.data'] = function () {
            $controller = new \Api\Controller\User\DataController();

            return $controller;
        };

        $this['controller.user.update'] = function () {
            $controller = new \Api\Controller\User\UpdateController();
            $controller->setUser($this->get(self::USER_SAVE));
            $controller->setEmailValidator($this->get(self::EMAIL_VALIDATOR));
            $controller->setLocale($this->get(self::SERVICE_LOCALE));

            return $controller;
        };

        $this['controller.user.register'] = function () {
            $controller = new \Api\Controller\User\RegisterController();
            $controller->setCrypt($this->get(self::AUTHORIZATION_CRYPT));
            $controller->setToken($this->get(self::AUTHORIZATION_TOKEN));
            $controller->setUserSave($this->get(self::USER_SAVE));
            $controller->setUserData($this->get(self::USER_DATA));
            $controller->setEmailValidator($this->get(self::EMAIL_VALIDATOR));
            $controller->setLocale($this->get(self::SERVICE_LOCALE));

            return $controller;
        };

    }

    private function initControllerPredict()
    {
        $this['controller.predict.group'] = function () {
            $controller = new \Api\Controller\Predict\GroupController();
            $controller->setPredictGroup($this->get(self::PREDICT_GROUP));

            return $controller;
        };

        $this['controller.predict.price'] = function () {
            $controller = new \Api\Controller\Predict\PriceController();
            $controller->setData($this->get(self::TRANSACTION_DATA));
            $controller->setPredictPrice($this->get(self::PREDICT_PRICE));

            return $controller;
        };
    }

    private function initControllerDistinct()
    {
        $this['controller.distinct.groups'] = function () {
            $controller = new \Api\Controller\Distinct\GroupsController();
            $controller->setGroupsData($this->get(self::GROUPS_DATA));

            return $controller;
        };

        $this['controller.distinct.items'] = function () {
            $controller = new \Api\Controller\Distinct\ItemsController();
            $controller->setItemsData($this->get(self::ITEMS_DATA));

            return $controller;
        };
    }

    private function initControllerConnection()
    {
        $this['controller.connection.list'] = function () {
            $controller = new \Api\Controller\Connection\ListController();
            $controller->setConnectionData($this->get(self::CONNECTION_DATA));

            return $controller;
        };

        $this['controller.connection.add'] = function () {
            $controller = new \Api\Controller\Connection\AddController();
            $controller->setConnectionSave($this->get(self::CONNECTION_SAVE));
            $controller->setConnectionData($this->get(self::CONNECTION_DATA));
            $controller->setMailer($this->get(self::EMAIL_MAILER));
            $controller->setMessage($this->get(self::EMAIL_MESSAGE_CONNECTION));
            $controller->setConnection(new \Api\Entities\Connection());

            return $controller;
        };

        $this['controller.connection.accept'] = function () {
            $controller = new \Api\Controller\Connection\AcceptController();
            $controller->setConnectionSave($this->get(self::CONNECTION_SAVE));
            $controller->setConnectionData($this->get(self::CONNECTION_DATA));

            return $controller;
        };

        $this['controller.connection.reject'] = function () {
            $controller = new \Api\Controller\Connection\RejectController();
            $controller->setConnectionSave($this->get(self::CONNECTION_SAVE));
            $controller->setConnectionData($this->get(self::CONNECTION_DATA));

            return $controller;
        };

    }

    private function initControllerAuthenticate()
    {
        $this['controller.authenticate.login'] = function () {
            $controller = new \Api\Controller\Authenticate\LoginController();
            $controller->setCrypt($this->get(self::AUTHORIZATION_CRYPT));
            $controller->setToken($this->get(self::AUTHORIZATION_TOKEN));
            $controller->setUserData($this->get(self::USER_DATA));
            $controller->setUserSave($this->get(self::USER_SAVE));
            $controller->setLocale($this->get(self::SERVICE_LOCALE));

            $securityConfig = $this->getConfig()->get(Config::SECURITY);

            $controller->setMaxLoginAttempts($securityConfig['max_login_attempts']);
            $controller->setLoginAbuseSleepTime($securityConfig['login_abuse_sleep_time']);

            return $controller;
        };

        $this['controller.authenticate.logout'] = function () {
            $controller = new \Api\Controller\Authenticate\LogoutController();
            $controller->setToken($this->get(self::AUTHORIZATION_TOKEN));

            return $controller;
        };

        $this['controller.authenticate.password-recovery'] = function () {
            $controller = new \Api\Controller\Authenticate\PasswordRecoveryController();
            $controller->setCrypt($this->get(self::AUTHORIZATION_CRYPT));
            $controller->setUserData($this->get(self::USER_DATA));
            $controller->setUserSave($this->get(self::USER_SAVE));
            $controller->setMailer($this->get(self::EMAIL_MAILER));
            $controller->setMessage($this->get(self::EMAIL_MESSAGE_RECOVERY));

            return $controller;
        };
    }

    private function initControllerTransactions()
    {
        $this['controller.transactions.id'] = function () {
            $controller = new \Api\Controller\Transactions\IdController();
            $controller->setData($this->get(self::TRANSACTION_DATA));
            $controller->setValidate($this->get(self::TRANSACTION_VALIDATE));
            $controller->setMoney($this->get(self::TRANSACTION_MONEY));

            return $controller;
        };

        $this['controller.transactions.list'] = function () {
            $controller = new \Api\Controller\Transactions\ListController();
            $controller->setData($this->get(self::TRANSACTION_DATA));
            $controller->setDate($this->get(self::TRANSACTION_DATE));

            return $controller;
        };

        $this['controller.transactions.create'] = function () {
            $controller = new \Api\Controller\Transactions\CreateController();
            $controller->setSave($this->get(self::TRANSACTION_SAVE));
            $controller->setMoney($this->get(self::TRANSACTION_MONEY));
            $controller->setDate($this->get(self::TRANSACTION_DATE));

            return $controller;
        };

        $this['controller.transactions.update'] = function () {
            $controller = new \Api\Controller\Transactions\UpdateController();
            $controller->setSave($this->get(self::TRANSACTION_SAVE));
            $controller->setData($this->get(self::TRANSACTION_DATA));
            $controller->setValidate($this->get(self::TRANSACTION_VALIDATE));
            $controller->setMoney($this->get(self::TRANSACTION_MONEY));
            $controller->setDate($this->get(self::TRANSACTION_DATE));

            return $controller;
        };

        $this['controller.transactions.remove'] = function () {
            $controller = new \Api\Controller\Transactions\RemoveController();
            $controller->setData($this->get(self::TRANSACTION_DATA));
            $controller->setRemove($this->get(self::TRANSACTION_REMOVE));
            $controller->setValidate($this->get(self::TRANSACTION_VALIDATE));

            return $controller;
        };
    }

    private function initControllerChart()
    {
        $this['controller.chart.pie'] = function () {
            $controller = new \Api\Controller\Chart\PieController();
            $controller->setChartPie($this->get(self::CHART_PIE));
            $controller->setDate($this->get(self::TRANSACTION_DATE));

            return $controller;
        };
    }

    private function initMiddleware()
    {
        $this[self::MIDDLEWARE_AUTHORIZATION] = function () {
            $middleware = new \Api\Middleware\Authorization();
            $middleware->setAcl($this->get(self::SERVICE_ACL));
            $middleware->setToken($this->get(self::AUTHORIZATION_TOKEN));
            $middleware->setTime($this->get(self::SERVICE_TIME));
            $middleware->setJsonMiddleware($this->get(self::MIDDLEWARE_JSON));

            return $middleware;
        };

        $this[self::MIDDLEWARE_PROCESS_TIME] = function () {
            $middleware = new \Api\Middleware\ProcessTime();
            $middleware->setTime($this->get(self::SERVICE_TIME));

            return $middleware;
        };

        $this[self::MIDDLEWARE_JSON] = function () {
            $middleware = new \Api\Middleware\Json();
            $middleware->setJsonService($this->get(self::SERVICE_JSON));

            return $middleware;
        };

        $this[self::MIDDLEWARE_HEADERS] = function () {
            $middleware = new \Api\Middleware\Headers();

            return $middleware;
        };

    }

    private function initEntityManager()
    {
        $dbConfig = $this->getConfig()->get(Config::DATABASE);

        $this[self::DOCTRINE_LOGGER] = function() {
            return new \Api\Service\Doctrine\SQLLogger();
        };

        $this[self::ENTITY_MANAGER] = function () use ($dbConfig) {
            $proxyDir                  = null;
            $cache                     = null;
            $useSimpleAnnotationReader = false;
            $config = \Doctrine\ORM\Tools\Setup::createAnnotationMetadataConfiguration(
                $dbConfig[Config::DATABASE_ENTITIES],
                $this->getConfig()->get(Config::DEVMODE),
                $proxyDir,
                $cache,
                $useSimpleAnnotationReader
            );

            $config->setSQLLogger($this[self::DOCTRINE_LOGGER]);

            $entityManager = \Doctrine\ORM\EntityManager::create(
                $dbConfig[Config::DATABASE_CONNECTION],
                $config
            );

            return $entityManager;
        };
    }

}
