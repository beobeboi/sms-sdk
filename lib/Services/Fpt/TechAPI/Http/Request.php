<?php

namespace DiagVN\Services\Fpt\TechAPI\Http;

use DiagVN\Services\Fpt\TechAPI\Constant;

class Request
{
    const CONTENT_TYPE_JSON = 'application/json';

    protected $endpoint = '';
    protected $action = '';
    protected $method = 'POST';
    protected $params = array();
    protected $headers = array();
    protected $contentType = self::CONTENT_TYPE_JSON;


    /**
     * Init endpoint in constructor
     */
    public function __construct()
    {
        $this->endpoint = Constant::getEndpoint();
    }


    /**
     * Set param request
     *
     * @param string $name
     * @param string $val
     * @return Request
     */
    public function setParam($name, $val)
    {
        $this->params[$name] = $val;
        return $this;
    }


    /**
     * Set params request
     *
     * @param array $params
     * @return Request
     */
    public function setParams(array $params)
    {
        $this->params = $params;
        return $this;
    }


    /**
     * Add params to request
     *
     * @param array $params
     * @return Request
     */
    public function addParams(array $params)
    {
        $this->params += $params;
        return $this;
    }


    /**
     * Set request action
     *
     * @param string $action
     * @return \TechAPI\Http\Request
     */
    public function setAction($action)
    {
        $this->action = $action;
        return $this;
    }


    /**
     * Get request body params
     *
     * @return string|multitype:
     */
    public function getPostBody()
    {
        return $this->params;
    }


    /**
     * Get request header
     *
     * @return array
     */
    public function getRequestHeaders()
    {
        return $this->headers + array('Content-Type' => $this->contentType);
    }


    /**
     * Get request url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->endpoint . $this->action;
    }


    /**
     * Get request method
     *
     * @return string
     */
    public function getRequestMethod()
    {
        return $this->method;
    }


    /**
     * Get user agent
     *
     * @return string
     */
    public function getUserAgent()
    {
        return 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/45.0.2454.93 Safari/537.36';
    }
}
