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
 * @method Authorization setUser(User $user = null)
 * @method Authorization setConnectedUserIds(array $connectedUserIds)
 * @method Authorization setToken(Token $token)
 * @method Authorization setAcl(Acl $acl)
 * @method Authorization setJsonMiddleware(Json $acl)
 * @method User|null     getUser()
 * @method array         getConnectedUserIds()
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
     * @return string|null
     */
    private function getResource()
    {
        return $this->getRequestPath()[self::KEY_RESOURCE];
    }

    /**
     * @return string|null
     */
    private function getPrivilege()
    {
        return $this->getRequestPath()[self::KEY_PRIVILEGE];
    }

    /**
     * @param string $token
     *
     * @return $this
     */
    private function findUser($token)
    {
        if (!empty($token)) {
            $user = $this->getToken()->findUser($token);
            $this->setUser($user);

            $connectedUserIds = $user ? $this->getToken()->getConnectedUsers() : [];
            $this->setConnectedUserIds($connectedUserIds);
        } else {
            $this->setUser(null);
            $this->setConnectedUserIds(array());
        }

        return $this;
    }

    /**
     * @return string
     */
    private function getUserRole()
    {
        $user = $this->getUser();

        return $user ? $user->getRole() : User::ROLE_GUEST;
    }

    /**
     * Setup json response
     */
    public function call()
    {
        /** @var \Api\Slim $app */
        $app = $this->getApplication();

        $token = $app->request()->get('token');

        try {
            $this->validate($token);

            $app->config('user', $this->getUser());
            $app->config('connectedUserIds', $this->getConnectedUserIds());

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
     *
     * @throws ResourceDeniedException
     */
    private function validate($token)
    {
        $role      = $this->findUser($token)->getUserRole();
        $resource  = $this->getResource();
        $privilege = $this->getPrivilege();

        $resource = empty($resource) ? Acl::RESOURCE_INDEX : $resource;

        $allowed = $this->getAcl()->isAllowed($role, $resource, $privilege);

        if (!$allowed) {
            $message = 'Resource "' . $resource . '" with privilege "' . $privilege . '" '
                       . 'is not allowed for role "' . $role . '" '
                       . (empty($token) ? 'without' : 'using') . ' token';

            throw new ResourceDeniedException($message);
        }
    }
}