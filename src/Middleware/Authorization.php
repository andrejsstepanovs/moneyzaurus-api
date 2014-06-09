<?php

namespace Api\Middleware;

use Slim\Middleware;
use Api\Service\Acl;
use Api\Service\Authorization\Token;
use Api\Entities\User;
use Api\Service\AccessorTrait;
use Api\Service\Exception\ResourceDeniedException;
use Api\Middleware\Json;

/**
 * Class Json
 *
 * @package Api\Middleware
 *
 * @method Authorization setToken(Token $token)
 * @method Authorization setAcl(Acl $acl)
 * @method Authorization setJsonMiddleware(Json $acl)
 * @method Token         getToken()
 * @method Acl           getAcl()
 * @method Json          getJsonMiddleware()
 */
class Authorization extends Middleware
{
    use AccessorTrait;

    /** uri separator */
    const URL_SEPARATOR = '/';

    /** resource consecutive number */
    const KEY_RESOURCE  = 0;

    /** privilege consecutive number */
    const KEY_PRIVILEGE = 1;

    /** @var array */
    private $path;

    /**
     * @return string
     */
    private function getRequestPath()
    {
        if ($this->path === null) {
            /** @var \Api\Slim $app */
            $app = $this->getApplication();
            $path = $app->request()->getPath();
            $path = trim($path, self::URL_SEPARATOR);

            $pathData = explode(self::URL_SEPARATOR, $path);

            $this->path = array(
                self::KEY_RESOURCE  => !empty($pathData[self::KEY_RESOURCE]) ? $pathData[self::KEY_RESOURCE] : null,
                self::KEY_PRIVILEGE => !empty($pathData[self::KEY_PRIVILEGE]) ? $pathData[self::KEY_PRIVILEGE] : null,
            );
        }

        return $this->path;
    }

    /**
     * Setup json response
     */
    public function call()
    {
        /** @var \Api\Slim $app */
        $app = $this->getApplication();

        $token = $app->request()->get('token');
        $tokenModule = $this->getToken();

        try {
            $accessToken = $tokenModule->findAccessToken($token);
            $user        = $accessToken ? $accessToken->getUser() : null;

            if ($accessToken) {
                $tokenModule->validateExpired($accessToken, $user);
            }
            $role = $user ? $user->getRole() : User::ROLE_GUEST;
            $this->validatePrivilege($token, $role);

            $connectedUserIds = $user ? $tokenModule->getConnectedUsers($user) : [];

            $app->config('user', $user);
            $app->config('connectedUserIds', $connectedUserIds);

            $this->getNextMiddleware()->call();

        } catch (\RuntimeException $exc) {
            $data = [
                'success' => false,
                'message' => $exc->getMessage()
            ];
            $app->setData($data);

            $response = $app->response();
            $response->setStatus(403);

            $this->getJsonMiddleware()->modifyResponse($app, $response);
        }
    }

    /**
     * @param string $token
     * @param string $role
     *
     * @throws ResourceDeniedException
     */
    private function validatePrivilege($token, $role)
    {
        $path = $this->getRequestPath();

        $privilege = $path[self::KEY_PRIVILEGE];
        $resource  = $path[self::KEY_RESOURCE];
        $resource  = empty($resource) ? Acl::RESOURCE_INDEX : $resource;

        $allowed = $this->getAcl()->isAllowed($role, $resource, $privilege);

        if (!$allowed) {
            $message = sprintf(
                'Resource "%s" with privilege "%s" is not allowed for role "%s" %s token.%s',
                $resource,
                $privilege,
                $role,
                empty($token) ? 'without' : 'using',
                !empty($token) ? ' Please check token.' : ''
            );

            throw new ResourceDeniedException($message);
        }
    }
}