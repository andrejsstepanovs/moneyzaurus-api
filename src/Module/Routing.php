<?php

namespace Api\Module;

use SlimApi\Kernel\Routing as KernelRouting;
use Api\Middleware\Json as MiddlewareJson;
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
        $this->getSlim()->add(new MiddlewareJson());

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

        $slim->map(
             '/',
             function() use ($container, $slim) {
                 /** @var \Api\Controller\Index\IndexController $controller */
                 $controller = $container->get('controller.index.index');
                 $slim->setData($controller->getResponse());
             }
        )
        ->via(Request::METHOD_GET);

        $slim->map(
             '/authenticate/login',
             function() use ($container, $slim) {
                 $username = $slim->request()->post('username');
                 $password = $slim->request()->post('password');

                 /** @var \Api\Controller\Authenticate\LoginController $controller */
                 $controller = $container->get('controller.authenticate.login');
                 $slim->setData($controller->getResponse($username, $password));
             }
        )
        ->via(Request::METHOD_POST);

        $slim->map(
             '/authenticate/logout',
             function() use ($container, $slim) {
                 /** @var \Api\Controller\Authenticate\LogoutController $controller */
                 $controller = $container->get('controller.authenticate.logout');
                 $response = $controller->getResponse(
                    $slim->config('user'),
                    $slim->request()->get('token')
                 );
                 $slim->setData($response);
             }
        )
        ->via(Request::METHOD_GET);

        $slim->map(
             '/authenticate/password-recovery',
             function() use ($container, $slim) {
                 $username = $slim->request()->post('username');

                 /** @var \Api\Controller\Authenticate\PasswordRecoveryController $controller */
                 $controller = $container->get('controller.authenticate.password-recovery');
                 $response = $controller->getResponse($username);
                 $slim->setData($response);
             }
        )
        ->via(Request::METHOD_POST);

        $slim->map(
             '/transactions/id/:id',
             function($id) use ($container, $slim) {
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
             '/transactions/remove/:id',
             function($id) use ($container, $slim) {
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
             '/transactions/update/:id',
             function($id) use ($container, $slim) {
                 /** @var \Api\Controller\Transactions\UpdateController $controller */
                 $controller = $container->get('controller.transactions.update');
                 $request = $slim->request();
                 $response = $controller->getResponse(
                     $slim->config('user'),
                     $slim->config('connectedUserIds'),
                     $id,
                     $request->post('item'),
                     $request->post('group'),
                     $request->post('price'),
                     $request->post('currency'),
                     $request->post('date')
                 );
                 $slim->setData($response);
             }
        )
        ->via(Request::METHOD_POST);

        $slim->map(
             '/transactions/list',
             function() use ($container, $slim) {
                 /** @var \Api\Controller\Transactions\ListController $controller */
                 $controller = $container->get('controller.transactions.list');
                 $response = $controller->getResponse(
                     $slim->config('user'),
                     $slim->config('connectedUserIds'),
                     $slim->request()->get('offset'),
                     $slim->request()->get('limit'),
                     $slim->request()->get('from'),
                     $slim->request()->get('till'),
                     $slim->request()->get('item'),
                     $slim->request()->get('group'),
                     $slim->request()->get('price')
                 );
                 $slim->setData($response);
             }
        )
        ->via(Request::METHOD_GET);

        $slim->map(
            '/transactions/add',
            function() use ($container, $slim) {
                /** @var \Api\Controller\Transactions\CreateController $controller */
                $controller = $container->get('controller.transactions.create');
                $request = $slim->request();
                $response = $controller->getResponse(
                    $slim->config('user'),
                    $request->post('item'),
                    $request->post('group'),
                    $request->post('price'),
                    $request->post('currency'),
                    $request->post('date')
                );
                $slim->setData($response);
            }
        )
        ->via(Request::METHOD_POST);

        $slim->map(
            '/distinct/groups',
            function() use ($container, $slim) {
                /** @var \Api\Controller\Distinct\GroupsController $controller */
                $controller = $container->get('controller.distinct.groups');
                $response = $controller->getResponse(
                    $slim->config('user'),
                    $slim->config('connectedUserIds')
                );
                $slim->setData($response);
            }
        )
        ->via(Request::METHOD_GET);

        $slim->map(
            '/distinct/items',
            function() use ($container, $slim) {
                /** @var \Api\Controller\Distinct\ItemsController $controller */
                $controller = $container->get('controller.distinct.items');
                $response = $controller->getResponse(
                    $slim->config('user'),
                    $slim->config('connectedUserIds')
                );
                $slim->setData($response);
            }
        )
        ->via(Request::METHOD_GET);

        $slim->map(
            '/predict/group',
            function() use ($container, $slim) {
                /** @var \Api\Controller\Predict\GroupController $controller */
                $controller = $container->get('controller.predict.group');
                $response = $controller->getResponse(
                    $slim->config('user'),
                    $slim->config('connectedUserIds'),
                    $slim->request()->get('item')
                );
                $slim->setData($response);
            }
        )
        ->via(Request::METHOD_GET);

        $slim->map(
             '/predict/price',
             function() use ($container, $slim) {
                 /** @var \Api\Controller\Predict\PriceController $controller */
                 $controller = $container->get('controller.predict.price');
                 $response = $controller->getResponse(
                    $slim->config('user'),
                    $slim->config('connectedUserIds'),
                    $slim->request()->get('item'),
                    $slim->request()->get('group')
                 );
                 $slim->setData($response);
             }
        )
        ->via(Request::METHOD_GET);

        $slim->map(
            '/user/data',
            function() use ($container, $slim) {
                /** @var \Api\Controller\User\DataController $controller */
                $controller = $container->get('controller.user.data');
                $response = $controller->getResponse($slim->config('user'));
                $slim->setData($response);
            }
        )
        ->via(Request::METHOD_GET);

        $slim->map(
            '/user/update',
            function() use ($container, $slim) {
                /** @var \Api\Controller\User\UpdateController $controller */
                $controller = $container->get('controller.user.update');
                $response = $controller->getResponse(
                    $slim->config('user'),
                    $slim->request()->post('email'),
                    $slim->request()->post('name'),
                    $slim->request()->post('locale'),
                    $slim->request()->post('language'),
                    $slim->request()->post('timezone')
                );
                $slim->setData($response);
            }
        )
        ->via(Request::METHOD_POST);

        $slim->map(
            '/connection/list',
            function() use ($container, $slim) {
                /** @var \Api\Controller\Connection\ListController $controller */
                $controller = $container->get('controller.connection.list');
                $response = $controller->getResponse($slim->config('user'));
                $slim->setData($response);
            }
        )
        ->via(Request::METHOD_GET);

        $slim->map(
            '/connection/add',
            function() use ($container, $slim) {
                /** @var \Api\Controller\Connection\AddController $controller */
                $controller = $container->get('controller.connection.add');
                $response = $controller->getResponse(
                    $slim->config('user'),
                    $slim->request()->post('email')
                );
                $slim->setData($response);
            }
        )
        ->via(Request::METHOD_POST);

        $slim->map(
            '/connection/reject/:id',
            function($id) use ($container, $slim) {
                /** @var \Api\Controller\Connection\RejectController $controller */
                $controller = $container->get('controller.connection.reject');
                $response = $controller->getResponse($slim->config('user'), $id);
                $slim->setData($response);
            }
        )
        ->via(Request::METHOD_POST);

        $slim->map(
            '/connection/accept/:id',
            function($id) use ($container, $slim) {
                /** @var \Api\Controller\Connection\AcceptController $controller */
                $controller = $container->get('controller.connection.accept');
                $response = $controller->getResponse($slim->config('user'), $id);
                $slim->setData($response);
            }
        )
        ->via(Request::METHOD_POST);

        return $this;
    }

}