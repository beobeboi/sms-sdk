<?php

namespace DiagVN\Services\Fpt\TechAPI\Auth;

use DiagVN\Services\Fpt\TechAPI\Client;
use DiagVN\Services\Fpt\TechAPI\Api\ApiInterface;
use DiagVN\Services\Fpt\TechAPI\Http\Request;
use DiagVN\Services\Fpt\TechAPI\Http\Curl;
use DiagVN\Services\Fpt\TechAPI\Error;

class ClientCredentials
{
    const GRANT_TYPE = 'client_credentials';

    protected $client = null;
    protected $curl = null;

    /**
     * Construction
     *
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
        $this->curl = new Curl();
    }

    /**
     * Request token to authorization
     *
     * @return string
     */
    public function getAccessToken()
    {
        $oAccessToken = AccessToken::getInstance();

        // read access token in session.
        $accessToken = $oAccessToken->getAccessToken();
        if ($accessToken) {
            return $accessToken;
        }

        // request access token when empty or expires
        $oRequest = new Request();
        $oRequest->setParam('grant_type', self::GRANT_TYPE)
            ->addParams($this->client->getAuth())
            ->setAction('/oauth2/token');

        $accessToken = $this->curl->execute($oRequest);

        if ($accessToken && !isset($accessToken['error'])) {
            // request done and no error
            $oAccessToken->setAccessToken($accessToken);
            return (string)$oAccessToken;
        }

        // throw error
        Error::setError($accessToken['error'], $accessToken['error_description']);
    }


    /**
     * Execute access to api
     *
     * @param ApiInterface $api
     * @return mixed
     */
    public function execute(ApiInterface $api)
    {
        // get access token
        $sAccessToken = $this->getAccessToken();

        if (empty($sAccessToken)) {
            Error::setError(Error::EXPIRED_TOKEN);
        }

        // access resource
        $oRequest = new Request();
        $oRequest->setParam('access_token', $sAccessToken)
            ->addParams($api->toArray())
            ->setAction($api->getAction());

        // return result
        return $this->curl->execute($oRequest);
    }
}
