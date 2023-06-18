<?php

namespace DiagVN\Services\Fpt\TechAPI\Auth;

use DiagVN\Services\Fpt\TechAPI\Constant;
use Illuminate\Support\Facades\Cache;

final class AccessToken
{
    const SESSION_NAME = 'tech_sms_oauth';

    private static $instance = null;

    private $access_token = '';

    private $expired = 0;

    private $token_type = '';

    private $scope = '';

    private $refresh_token = '';


    /**
     * Constructor
     */
    private function __construct()
    {
        if (!isset($_SESSION)) {
            session_start();
        }

        // read old access token
        if (Constant::isCache()) {
            $this->_setAccessToken($this->_getAccessTokenData());
        }
    }


    /**
     * Get class instance
     *
     * @return AccessToken
     */
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new static();
        }

        return self::$instance;
    }


    /**
     * Set access token When request token success
     *
     * @param array $arrData
     */
    public function setAccessToken($arrData): void
    {
        // process expired time
        $arrData['expired'] = time() + $arrData['expires_in'] - 10; // trừ hao 10s
        // store access token
        $this->_setAccessToken($arrData);

        Cache::put(self::SESSION_NAME, serialize(array_filter(get_object_vars($this))), $arrData['expires_in']);
    }


    /**
     * Private set access token
     *
     * @param array $arrData
     */
    private function _setAccessToken($arrData)
    {
        foreach ($arrData as $key => $val) {
            if (isset($this->$key)) {
                $this->$key = $val;
            }
        }
    }


    /**
     * Get access token data
     *
     * @return array
     */
    private function _getAccessTokenData()
    {
        $data = Cache::get(self::SESSION_NAME);
        return !empty($data) ? unserialize($data) : [];
    }


    /**
     * Get access token
     *
     * @return string|NULL
     */
    public function getAccessToken()
    {
        if ($this->access_token && $this->expired > time()) {
            return $this->access_token;
        }

        return '';
    }


    /**
     * Convert class to string (echo, parse string)
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getAccessToken();
    }


    /**
     * Clear cache access token
     */
    public function clear()
    {
        Cache::forget(self::SESSION_NAME);
        $this->access_token = '';
        $this->expired = 0;
        $this->refresh_token = '';
        $this->scope = '';
        $this->token_type = '';
    }
}
