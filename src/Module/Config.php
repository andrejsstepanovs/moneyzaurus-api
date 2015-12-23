<?php

namespace Api\Module;

use SlimApi\Kernel\Config as KernelConfig;

/**
 * Class Config
 *
 * @package Api\Kernel
 */
class Config extends KernelConfig
{
    const DATABASE              = 'database';
    const DATABASE_ENTITIES     = 'databaseEntities';
    const DATABASE_CONNECTION   = 'databaseConnection';
    const PASSWORD_DEFAULT_COST = 'defaultPasswordCost';
    const EMAIL                 = 'email';
    const SECURITY              = 'security';
    const LOG                   = 'log';

    /**
     * @param string $key
     * @param string $default
     *
     * @return string
     */
    public function env($key, $default = null)
    {
        if (is_array($key)) {
            foreach ($key as $k) {
                $value = $this->env($k, $default);
                if (!empty($value) && $value !== $default) {
                    break;
                }
            }
        } else {
            $value = getenv($key);
        }

        if (empty($value) && !empty($default)) {
            return $default;
        }

        return $value;
    }
}
