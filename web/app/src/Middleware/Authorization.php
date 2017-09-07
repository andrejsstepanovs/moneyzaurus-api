<?php

namespace Api\Middleware;

use Api\Entities\AccessToken;
use Slim\Http\Request;
use Slim\Middleware;
use Api\Service\Acl;
use Api\Service\Authorization\Token;
use Api\Entities\User;
use Api\Service\AccessorTrait;
use Api\Service\Time;
use Api\Service\Exception\ResourceDeniedException;
use Api\Module\Config;

/**
 * Class Json
 *
 * @package Api\Middleware
 *
 * @method Authorization setToken(Token $token)
 * @method Authorization setAcl(Acl $acl)
 * @method Authorization setJsonMiddleware(Json $acl)
 * @method Authorization setTime(Time $time)
 * @method Authorization setConfig(Config $config)
 * @method Token         getToken()
 * @method Acl           getAcl()
 * @method Json          getJsonMiddleware()
 * @method Time          getTime()
 * @method Config        getConfig()
 */
class Authorization extends Middleware
{
    use AccessorTrait;

    /** uri separator */
    const URL_SEPARATOR = '/';

    /** resource consecutive number */
    private $keyResource = 0;

    /** privilege consecutive number */
    private $keyPrivilege = 1;

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
                $this->keyResource  => !empty($pathData[$this->keyResource]) ? $pathData[$this->keyResource] : null,
                $this->keyPrivilege => !empty($pathData[$this->keyPrivilege]) ? $pathData[$this->keyPrivilege] : null,
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
        $app     = $this->getApplication();
        $baseUrl = $this->getConfig()->get(Config::BASE_URL);
        if (!empty($baseUrl)) {
            $this->keyResource  = 1;
            $this->keyPrivilege = 2;
        }

        $tokenValue  = $this->getTokenValue($app->request());
        $tokenModule = $this->getToken();

        try {
            $accessTokenEntity = $tokenModule->findAccessToken($tokenValue);
            $user = $this->getUser($accessTokenEntity);

            $this->validateAccessToken($tokenValue, $tokenModule, $accessTokenEntity, $user);
            $connectedUserIds = $this->getConnectedUserIds($tokenModule, $user);

            $app->config('user', $user);
            $app->config('connectedUserIds', $connectedUserIds);

            $this->getNextMiddleware()->call();
        } catch (\RuntimeException $exc) {
            $data = [
                'success' => false,
                'message' => $exc->getMessage(),
            ];
            $app->setData($data);

            $response = $app->response();
            $response->setStatus(403);

            $this->getJsonMiddleware()->modifyResponse($app, $response);
        }
    }

    /**
     * @param string           $tokenValue
     * @param Token            $tokenModule
     * @param AccessToken|null $accessToken
     * @param User|null        $user
     *
     * @return $this
     */
    private function validateAccessToken(
        $tokenValue,
        Token $tokenModule,
        AccessToken $accessToken = null,
        User $user = null
    ) {
        if ($accessToken && $user) {
            $tokenModule->validateExpired($accessToken, $user);
        }

        $role = $user ? $user->getRole() : User::ROLE_GUEST;
        $this->validatePrivilege($tokenValue, $role);

        if ($accessToken && $user) {
            $this->updateUsedAt($accessToken, $user);
        }

        return $this;
    }

    /**
     * @param Request $request
     *
     * @return string|null
     */
    private function getTokenValue(Request $request)
    {
        $token = $request->get('token');
        if (empty($token)) {
            $token = $request->post('token');
        }

        return $token;
    }

    /**
     * @param AccessToken|null $accessToken
     *
     * @return User|null
     */
    private function getUser(AccessToken $accessToken = null)
    {
        return $accessToken ? $accessToken->getUser() : null;
    }

    /**
     * @param Token $tokenModule
     * @param User  $user
     *
     * @return array
     */
    private function getConnectedUserIds(Token $tokenModule, User $user = null)
    {
        return $user ? $tokenModule->getConnectedUsers($user) : [];
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

        $privilege = $path[$this->keyPrivilege];
        $resource  = $path[$this->keyResource];
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

    /**
     * @param AccessToken $accessToken
     *
     * @return $this
     */
    private function updateUsedAt(AccessToken $accessToken, User $user)
    {
        $timeZone = new \DateTimeZone($user->getTimezone());
        $dateTime = $this->getTime()->setTimezone($timeZone)->getDateTime();

        $accessToken->setUsedAt($dateTime);
        $this->getToken()->save($accessToken);

        return $this;
    }
}
