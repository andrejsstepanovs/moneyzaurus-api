<?php

namespace Api\Module;

use SlimApi\Kernel\Routing as KernelRouting;
use Api\Slim;
use Slim\Http\Request;

/**
 * Class Routing
 *
 * @package Api\Module
 *
 * @method Slim      getSlim
 * @method Container getContainer
 */
class Routing extends KernelRouting
{
    /**
     * @param Container $container
     *
     * @return $this
     */
    private function initSlim(Container $container)
    {
        $this->getSlim()->add($container->get(Container::MIDDLEWARE_PROCESS_TIME));
        $this->getSlim()->add($container->get(Container::MIDDLEWARE_AUTHORIZATION));
        $this->getSlim()->add($container->get(Container::MIDDLEWARE_HEADERS));
        $this->getSlim()->add($container->get(Container::MIDDLEWARE_JSON));

        return $this;
    }

    /**
     * @return $this
     */
    public function init()
    {
        $container = $this->getContainer();

        $this->initSlim($container);
        $slim = $this->getSlim();

        $config  = $container->get(Container::CONFIG);
        $devMode = $config->get(Config::DEVMODE);

        ini_set('display_errors', intval($devMode));
        $slim->config('debug', $devMode);

        set_error_handler(
            function ($errno, $errstr) {
                throw new \RuntimeException($errstr);
            },
            E_ALL
        );

        if (!$devMode) {
            $slim->error(function (\Exception $exc) {
                throw $exc;
            });
        }

        $baseUrl = $config->get(Config::BASE_URL);
        if (!empty($baseUrl)) {
            // if dirname is baseUrl name dont match it
            $pos = strrpos(API_BASE_DIR, $baseUrl);
            if ($pos !== false && $pos == strlen(API_BASE_DIR) - strlen($baseUrl)) {
                $baseUrl = '/' . trim($baseUrl, '/');
            } else {
                $baseUrl = '';
            }
        }

        $slim->response->status(200); // @todo set corrrect response codes

        $slim->map(
            $baseUrl . '/',
            function () use ($container, $slim) {
                /** @var \Api\Controller\Index\IndexController $controller */
                $controller = $container->get('controller.index.index');
                $slim->setData($controller->getResponse());
            }
        )
        ->via(Request::METHOD_GET);

        $slim->map(
            $baseUrl . '/test',
            function () use ($container, $slim) {
                phpinfo();die();
            }
        )
        ->via(Request::METHOD_GET);

        $slim->map(
            $baseUrl . '/authenticate/login',
            function () use ($container, $slim) {
                $username = $slim->getRequestValue('username');
                $password = $slim->getRequestValue('password');

                /** @var \Api\Controller\Authenticate\LoginController $controller */
                $controller = $container->get('controller.authenticate.login');
                $slim->setData($controller->getResponse($username, $password));
            }
        )
        ->via(Request::METHOD_POST);

        $slim->map(
            $baseUrl . '/user/register',
            function () use ($container, $slim) {
                /** @var \Api\Controller\User\RegisterController $controller */
                $controller = $container->get('controller.user.register');
                $responseData = $controller->getResponse(
                    $slim->getRequestValue('username'),
                    $slim->getRequestValue('password'),
                    $slim->getRequestValue('timezone'),
                    $slim->getRequestValue('display_name'),
                    $slim->getRequestValue('display_name'),
                    $slim->getRequestValue('language'),
                    $slim->getRequestValue('locale')
                );

                $slim->setData($responseData);
            }
        )
        ->via(Request::METHOD_POST);

        $slim->map(
            $baseUrl . '/authenticate/logout',
            function () use ($container, $slim) {
                /** @var \Api\Controller\Authenticate\LogoutController $controller */
                $controller = $container->get('controller.authenticate.logout');
                $response = $controller->getResponse(
                    $slim->config('user'),
                    $slim->getRequestValue('token')
                );
                $slim->setData($response);
            }
        )
        ->via(Request::METHOD_GET);

        $slim->map(
            $baseUrl . '/authenticate/password-recovery',
            function () use ($container, $slim) {
                $username = $slim->getRequestValue('username');

                 /** @var \Api\Controller\Authenticate\PasswordRecoveryController $controller */
                $controller = $container->get('controller.authenticate.password-recovery');
                $response = $controller->getResponse($username);
                $slim->setData($response);
            }
        )
        ->via(Request::METHOD_POST);

        $slim->map(
            $baseUrl . '/transactions/id/:id',
            function ($id) use ($container, $slim) {
                /** @var \Api\Controller\Transactions\IdController $controller */
                $controller = $container->get('controller.transactions.id');
                $response = $controller->getResponse(
                    $slim->config('user'),
                    $slim->config('connectedUserIds'),
                    $id
                );
                $slim->setData($response);
            }
        )
        ->via(Request::METHOD_GET);

        $slim->map(
            $baseUrl . '/transactions/remove/:id',
            function ($id) use ($container, $slim) {
                /** @var \Api\Controller\Transactions\RemoveController $controller */
                $controller = $container->get('controller.transactions.remove');
                $response = $controller->getResponse(
                    $slim->config('user'),
                    $slim->config('connectedUserIds'),
                    $id
                );
                $slim->setData($response);
            }
        )
        ->via(Request::METHOD_DELETE);

        $slim->map(
            $baseUrl . '/transactions/update/:id',
            function ($id) use ($container, $slim) {
                /** @var \Api\Controller\Transactions\UpdateController $controller */
                $controller = $container->get('controller.transactions.update');
                $response = $controller->getResponse(
                    $slim->config('user'),
                    $slim->config('connectedUserIds'),
                    $id,
                    $slim->getRequestValue('item'),
                    $slim->getRequestValue('group'),
                    $slim->getRequestValue('price'),
                    $slim->getRequestValue('currency'),
                    $slim->getRequestValue('date')
                );
                $slim->setData($response);
            }
        )
        ->via(Request::METHOD_POST);

        $slim->map(
            $baseUrl . '/transactions/list',
            function () use ($container, $slim) {
                /** @var \Api\Controller\Transactions\ListController $controller */
                $controller = $container->get('controller.transactions.list');
                $response = $controller->getResponse(
                    $slim->config('user'),
                    $slim->config('connectedUserIds'),
                    $slim->getRequestValue('offset'),
                    $slim->getRequestValue('limit'),
                    $slim->getRequestValue('from'),
                    $slim->getRequestValue('till'),
                    $slim->getRequestValue('item'),
                    $slim->getRequestValue('group'),
                    $slim->getRequestValue('price')
                );
                $slim->setData($response);
            }
        )
        ->via(Request::METHOD_GET);

        $slim->map(
            $baseUrl . '/transactions/add',
            function () use ($container, $slim) {
                /** @var \Api\Controller\Transactions\CreateController $controller */
                $controller = $container->get('controller.transactions.create');
                $response = $controller->getResponse(
                    $slim->config('user'),
                    $slim->getRequestValue('item'),
                    $slim->getRequestValue('group'),
                    $slim->getRequestValue('price'),
                    $slim->getRequestValue('currency'),
                    $slim->getRequestValue('date')
                );
                $slim->setData($response);
            }
        )
        ->via(Request::METHOD_POST, Request::METHOD_PUT);

        $slim->map(
            $baseUrl . '/distinct/groups',
            function () use ($container, $slim) {
                /** @var \Api\Controller\Distinct\GroupsController $controller */
                $controller = $container->get('controller.distinct.groups');
                $response = $controller->getResponse(
                    $slim->config('user'),
                    $slim->config('connectedUserIds'),
                    $slim->getRequestValue('from'),
                    $slim->getRequestValue('count')
                );
                $slim->setData($response);
            }
        )
        ->via(Request::METHOD_GET);

        $slim->map(
            $baseUrl . '/distinct/items',
            function () use ($container, $slim) {
                /** @var \Api\Controller\Distinct\ItemsController $controller */
                $controller = $container->get('controller.distinct.items');
                $response = $controller->getResponse(
                    $slim->config('user'),
                    $slim->config('connectedUserIds'),
                    $slim->getRequestValue('from'),
                    $slim->getRequestValue('count')
                );
                $slim->setData($response);
            }
        )
        ->via(Request::METHOD_GET);

        $slim->map(
            $baseUrl . '/predict/group',
            function () use ($container, $slim) {
                /** @var \Api\Controller\Predict\GroupController $controller */
                $controller = $container->get('controller.predict.group');
                $response = $controller->getResponse(
                    $slim->config('user'),
                    $slim->config('connectedUserIds'),
                    $slim->getRequestValue('item')
                );
                $slim->setData($response);
            }
        )
        ->via(Request::METHOD_POST);

        $slim->map(
            $baseUrl . '/predict/price',
            function () use ($container, $slim) {
                /** @var \Api\Controller\Predict\PriceController $controller */
                $controller = $container->get('controller.predict.price');
                $response = $controller->getResponse(
                   $slim->config('user'),
                   $slim->config('connectedUserIds'),
                    $slim->getRequestValue('item'),
                    $slim->getRequestValue('group')
                );
                $slim->setData($response);
            }
        )
        ->via(Request::METHOD_POST);

        $slim->map(
            $baseUrl . '/user/data',
            function () use ($container, $slim) {
                /** @var \Api\Controller\User\DataController $controller */
                $controller = $container->get('controller.user.data');
                $response = $controller->getResponse($slim->config('user'));
                $slim->setData($response);
            }
        )
        ->via(Request::METHOD_GET);

        $slim->map(
            $baseUrl . '/user/update',
            function () use ($container, $slim) {
                /** @var \Api\Controller\User\UpdateController $controller */
                $controller = $container->get('controller.user.update');
                $response = $controller->getResponse(
                    $slim->config('user'),
                    $slim->getRequestValue('email'),
                    $slim->getRequestValue('name'),
                    $slim->getRequestValue('locale'),
                    $slim->getRequestValue('language'),
                    $slim->getRequestValue('timezone')
                );
                $slim->setData($response);
            }
        )
        ->via(Request::METHOD_POST);

        $slim->map(
            $baseUrl . '/connection/list',
            function () use ($container, $slim) {
                /** @var \Api\Controller\Connection\ListController $controller */
                $controller = $container->get('controller.connection.list');
                $response = $controller->getResponse(
                    $slim->config('user'),
                    (bool) $slim->getRequestValue('parent')
                );
                $slim->setData($response);
            }
        )
        ->via(Request::METHOD_GET);

        $slim->map(
            $baseUrl . '/connection/add',
            function () use ($container, $slim) {
                /** @var \Api\Controller\Connection\AddController $controller */
                $controller = $container->get('controller.connection.add');
                $response = $controller->getResponse(
                    $slim->config('user'),
                    $slim->getRequestValue('email')
                );
                $slim->setData($response);
            }
        )
        ->via(Request::METHOD_POST);

        $slim->map(
            $baseUrl . '/connection/reject/:id',
            function ($id) use ($container, $slim) {
                /** @var \Api\Controller\Connection\RejectController $controller */
                $controller = $container->get('controller.connection.reject');
                $response = $controller->getResponse($slim->config('user'), $id);
                $slim->setData($response);
            }
        )
        ->via(Request::METHOD_POST);

        $slim->map(
            $baseUrl . '/connection/accept/:id',
            function ($id) use ($container, $slim) {
                /** @var \Api\Controller\Connection\AcceptController $controller */
                $controller = $container->get('controller.connection.accept');
                $response = $controller->getResponse($slim->config('user'), $id);
                $slim->setData($response);
            }
        )
        ->via(Request::METHOD_POST);

        $slim->map(
            $baseUrl . '/chart/pie',
            function () use ($container, $slim) {
                /** @var \Api\Controller\Chart\PieController $controller */
                $controller = $container->get('controller.chart.pie');
                $response = $controller->getResponse(
                    $slim->config('user'),
                    $slim->config('connectedUserIds'),
                    $slim->getRequestValue('currency'),
                    $slim->getRequestValue('from'),
                    $slim->getRequestValue('till')
                );
                $slim->setData($response);
            }
        )
        ->via(Request::METHOD_GET);

        return $this;
    }
}
