<?php

namespace DiagVN\Services\Fpt\TechAPI\Http;

use DiagVN\Services\Fpt\TechAPI\Constant;
use GuzzleHttp\Client;

class Curl
{
    /**
     * Execute an HTTP Request
     */
    public function execute(Request $request)
    {
        $client = new Client(
            [
                'verify' => false,
                'timeout' => Constant::getTimeout(),
            ]
        );
        $requestHeaders = $request->getRequestHeaders();
        $requestHeaders = array_merge($requestHeaders, [
            'User-Agent' => $request->getUserAgent()
        ]);
        $response = $client->request(
            $request->getRequestMethod(),
            $request->getUrl(),
            [
                'json' => $request->getPostBody(),
                'headers' => $requestHeaders,
            ]
        );

        return json_decode($response->getBody()->getContents(), true);
    }
}
