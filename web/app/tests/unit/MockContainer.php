<?php

namespace Tests;

use Pimple\Container as Pimple;
use PHPUnit_Framework_TestCase;

/**
 * Class MockContainer
 *
 * @package Tests
 */
class MockContainer extends Pimple
{
    /** @var bool */
    protected $isInitialized = false;

    /**
     * @param string $name
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    public function get($name)
    {
        if (!$this->isInitialized) {
            $this->isInitialized = true;
            $this->initialize();
        }

        return $this[$name];
    }

    /**
     * @param PHPUnit_Framework_TestCase $testCase
     *
     * @return $this
     */
    public function setTestCase(PHPUnit_Framework_TestCase $testCase)
    {
        $this['case'] = $testCase;

        return $this;
    }

    public function initialize()
    {
        $this->initEntities();
        $this->initServiceTransaction();
        $this->initEmail();
        $this->initMiddleware();

        $this['Api\Module\Config'] = function (MockContainer $self) {
            return $self->buildMock(
                'Api\Module\Config',
                array('get')
            );
        };
    }

    private function initMiddleware()
    {
        $this['Api\Middleware\Json'] = function (MockContainer $self) {
            return $self->buildMock(
                '\Api\Middleware\Json',
                array('modifyResponse')
            );
        };
    }

    private function initEmail()
    {
        $this['Tests\Stub\TestTransport'] = function (MockContainer $self) {
            require_once 'Stub/TestTransport.php';

            return new \Tests\Stub\TestTransport();
        };

        $this['\Swift_Mailer'] = function (MockContainer $self) {
            return new \Swift_Mailer($self->get('Tests\Stub\TestTransport'));
        };

        $this['\Egulias\EmailValidator\EmailValidator'] = function (MockContainer $self) {
            return $self->buildMock(
                '\Egulias\EmailValidator\EmailValidator',
                array('isValid')
            );
        };
    }

    public function initServiceTransaction()
    {
        $this['Api\Service\Items\Data'] = function (MockContainer $self) {
            return $self->buildMock(
                'Api\Service\Items\Data',
                array('getItems')
            );
        };

        $this['Api\Service\Groups\Data'] = function (MockContainer $self) {
            return $self->buildMock(
                'Api\Service\Groups\Data',
                array('getGroups')
            );
        };

        $this['Api\Service\Connection\Save'] = function (MockContainer $self) {
            return $self->buildMock(
                'Api\Service\Connection\Save',
                array('save')
            );
        };

        $this['Api\Service\Connection\Data'] = function (MockContainer $self) {
            return $self->buildMock(
                'Api\Service\Connection\Data',
                array(
                    'findByUser',
                    'findByParent',
                    'getInvitedUser',
                    'normalizeResults',
                    'findById',
                )
            );
        };

        $this['Api\Service\Transaction\Data'] = function (MockContainer $self) {
            return $self->buildMock(
                'Api\Service\Transaction\Data',
                array(
                    'find',
                    'toArray',
                    'getTransactionsList',
                    'normalizeResults',
                )
            );
        };

        $this['Api\Service\Email\Messages\ConnectionInvitation'] = function (MockContainer $self) {
            return $self->buildMock(
                'Api\Service\Email\Messages\ConnectionInvitation',
                array(
                    'ConnectionInvitation',
                    'getMessage',
                )
            );
        };

        $this['Api\Service\Email\Messages\PasswordRecovery'] = function (MockContainer $self) {
            return $self->buildMock(
                'Api\Service\Email\Messages\PasswordRecovery',
                array(
                    'setUser',
                    'setPassword',
                    'getMessage',
                )
            );
        };

        $this['Api\Service\User\Data'] = function (MockContainer $self) {
            return $self->buildMock(
                'Api\Service\User\Data',
                array(
                    'setUser',
                    'findUser',
                )
            );
        };

        $this['Api\Service\User\Save'] = function (MockContainer $self) {
            return $self->buildMock(
                'Api\Service\User\Save',
                array(
                    'setEntityManager',
                    'saveUser',
                )
            );
        };

        $this['Api\Service\Transaction\Validate'] = function (MockContainer $self) {
            return $self->buildMock(
                'Api\Service\Transaction\Validate',
                array(
                    'isAllowed',
                )
            );
        };

        $this['Api\Service\Transaction\Save'] = function (MockContainer $self) {
            return $self->buildMock(
                'Api\Service\Transaction\Save',
                array(
                    'save',
                )
            );
        };

        $this['Api\Service\Transaction\Remove'] = function (MockContainer $self) {
            return $self->buildMock(
                'Api\Service\Transaction\Remove',
                array(
                    'remove',
                )
            );
        };

        $this['Api\Service\Transaction\Date'] = function (MockContainer $self) {
            return $self->buildMock(
                'Api\Service\Transaction\Date',
                array(
                    'getDateTime',
                )
            );
        };

        $this['Api\Service\Transaction\Money'] = function (MockContainer $self) {
            return $self->buildMock(
                'Api\Service\Transaction\Money',
                array(
                    'getAmount',
                )
            );
        };

        $this['Api\Service\Locale'] = function (MockContainer $self) {
            return $self->buildMock(
                'Api\Service\Locale',
                array(
                    'setLocale',
                    'setUser',
                    'getDateFormatter',
                    'getDateTimeFormatter',
                    'getFormattedMoney',
                    'format',
                    'isValidLocale',
                    'isValidTimezone',
                )
            );
        };

        $this['Doctrine\ORM\EntityRepository'] = function (MockContainer $self) {
            return $self->buildMock(
                'Doctrine\ORM\EntityRepository',
                array(
                    'find',
                    'findBy',
                    'findOneBy',
                    'getUser',
                )
            );
        };

        $this['Doctrine\ORM\EntityManager'] = function (MockContainer $self) {
            return $self->buildMock(
                'Doctrine\ORM\EntityManager',
                array(
                    'flush',
                    'commit',
                    'persist',
                    'rollback',
                    'getRepository',
                    'beginTransaction',
                    'createQuery',
                    'findBy',
                    'findOneBy',
                    'find',
                    'remove',
                )
            );
        };

        $this['Doctrine\ORM\AbstractQuery'] = function (MockContainer $self) {
            return $self->buildMock(
                'Doctrine\ORM\AbstractQuery',
                array(
                    'setParameters',
                    'setMaxResults',
                    'setFirstResult',
                    'getResult',
                    'getSQL',
                    '_doExecute',
                )
            );
        };

        $this['Api\Service\Authorization\Crypt'] = function (MockContainer $self) {
            return $self->buildMock(
                'Api\Service\Authorization\Crypt',
                array(
                    'verify',
                    'create',
                    'getRandomPassword',
                )
            );
        };

        $this['Api\Service\Authorization\Token'] = function (MockContainer $self) {
            return $self->buildMock(
                'Api\Service\Authorization\Token',
                array(
                    'findAccessToken',
                    'validateExpired',
                    'getConnectedUsers',
                    'save',
                    'get',
                    'generateToken',
                    'setUser',
                    'setAccessToken',
                    'remove',
                )
            );
        };

        $this['\Api\Service\Time'] = function (MockContainer $self) {
            return $self->buildMock(
                '\Api\Service\Time',
                array(
                    'setTimezone',
                    'getMicroTimeDifference',
                    'getDateTime',
                    'compareDateTime',
                )
            );
        };

        $this['\Api\Service\Json'] = function (MockContainer $self) {
            return $self->buildMock(
                '\Api\Service\Json',
                array(
                    'encode',
                    'decode',
                    'getJsonErrorMessage',
                )
            );
        };

        $this['Api\Service\Predict\Group'] = function (MockContainer $self) {
            return $self->buildMock(
                'Api\Service\Predict\Group',
                array(
                    'predict',
                )
            );
        };

        $this['Api\Service\Predict\Price'] = function (MockContainer $self) {
            return $self->buildMock(
                'Api\Service\Predict\Price',
                array(
                    'predict',
                )
            );
        };

        $this['\Slim\Slim'] = function (MockContainer $self) {
            return $self->buildMock(
                '\Slim\Slim',
                array(
                    'request',
                    'config',
                    'response',
                    'getData',
                    'setData',
                    'getNextMiddleware',
                )
            );
        };

        $this['\Api\Slim'] = function (MockContainer $self) {
            return $self->buildMock(
                '\Api\Slim',
                array(
                    'request',
                    'config',
                    'response',
                    'getData',
                    'setData',
                    'getNextMiddleware',
                )
            );
        };

        $this['\Slim\Middleware'] = function (MockContainer $self) {
            return $self->buildMock(
                '\Slim\Middleware',
                array(
                    'call',
                )
            );
        };

        $this['\Slim\Http\Response'] = function (MockContainer $self) {
            return $self->buildMock(
                '\Slim\Http\Response',
                array(
                    'headers',
                    'setBody',
                    'setStatus',
                )
            );
        };

        $this['\Slim\Http\Headers'] = function (MockContainer $self) {
            return $self->buildMock(
                '\Slim\Http\Headers',
                array(
                    'set',
                )
            );
        };

        $this['\Slim\Http\Request'] = function (MockContainer $self) {
            return $self->buildMock(
                '\Slim\Http\Request',
                array(
                    'get',
                    'post',
                    'getPath',
                )
            );
        };

        $this['Api\Service\Chart\Pie'] = function (MockContainer $self) {
            return $self->buildMock(
                'Api\Service\Chart\Pie',
                array(
                    'getData',
                    'normalizeResults',
                )
            );
        };

        $this['Api\Service\Acl'] = function (MockContainer $self) {
            return $self->buildMock(
                'Api\Service\Acl',
                array(
                    'isAllowed',
                )
            );
        };

        $this['Zend\Crypt\Password\Bcrypt'] = function (MockContainer $self) {
            return $self->buildMock(
                'Zend\Crypt\Password\Bcrypt',
                array(
                    'create',
                    'verify',
                )
            );
        };
    }

    private function initEntities()
    {
        $this['Api\Entities\Transaction'] = function (MockContainer $self) {
            return $self->buildMock(
                'Api\Entities\Transaction',
                array(
                    'getId',
                    'getItem',
                    'getGroup',
                    'getPrice',
                    'getUser',
                    'getDate',
                    'getDateCreated',
                    'getCurrency',
                )
            );
        };

        $this['Api\Entities\User'] = function (MockContainer $self) {
            return $self->buildMock(
                'Api\Entities\User',
                array(
                    'getTimezone',
                    'getId',
                    'getPassword',
                    'getEmail',
                    'getRole',
                    'setPassword',
                    'setDisplayName',
                    'getDisplayName',
                    'setLocale',
                    'getLocale',
                    'setLanguage',
                    'getLanguage',
                    'setState',
                    'getState',
                    'getLoginAttempts',
                    'setLoginAttempts',
                )
            );
        };

        $this['Api\Entities\Item'] = function (MockContainer $self) {
            return $self->buildMock(
                'Api\Entities\Item',
                array(
                    'getName',
                )
            );
        };

        $this['Api\Entities\Group'] = function (MockContainer $self) {
            return $self->buildMock(
                'Api\Entities\Group',
                array(
                    'getName',
                )
            );
        };

        $this['Api\Entities\Currency'] = function (MockContainer $self) {
            return $self->buildMock(
                'Api\Entities\Currency',
                array(
                    'getCurrency',
                )
            );
        };

        $this['Api\Entities\AccessToken'] = function (MockContainer $self) {
            return $self->buildMock(
                'Api\Entities\AccessToken',
                array(
                    'getToken',
                    'setToken',
                    'setCreated',
                    'getCreated',
                    'setUser',
                    'getUser',
                    'setUsedAt',
                    'getUsedAt',
                    'setValidUntil',
                    'getValidUntil',
                )
            );
        };

        $this['Api\Entities\Connection'] = function (MockContainer $self) {
            return $self->buildMock(
                'Api\Entities\Connection',
                array(
                    'getParent',
                    'getId',
                    'getDateCreated',
                    'getState',
                    'getUser',
                )
            );
        };
    }

    /**
     * @param string $class
     * @param array  $methods
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function buildMock($class, array $methods = null, $disableAutoloader = false)
    {
        if ($disableAutoloader) {
            $mock = $this
                ->getCase()
                ->getMockBuilder($class)
                ->disableOriginalConstructor()
                ->disableProxyingToOriginalMethods()
                ->disableAutoload()
                ->disableOriginalClone()
                ->disableArgumentCloning()

                ->setMethods(array_unique($methods));
        } else {
            $mock = $this
                ->getCase()
                ->getMockBuilder($class)
                ->disableOriginalConstructor()
                ->setMethods(array_unique($methods));
        }

        return $mock->getMock();
    }

    /**
     * @return PHPUnit_Framework_TestCase
     */
    private function getCase()
    {
        return $this->get('case');
    }
}
