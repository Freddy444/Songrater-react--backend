<?php
class Basecontroller
{

    public function __call($name, $arguments)
    {
        $this->sendOutput('', array('HTTP/1.1 404 Not Found'));
    }


    protected function getUriSegments()
    {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $uri = explode( '/', $uri );
        return $uri;
    }


    protected function getQueryStringParams()
    {
        parse_str($_SERVER['QUERY_STRING'], $query);
        return $query;
    }

    protected function sendOutput($data, $httpHeaders=array())
    {
        header('Access-Control-Allow-Origin: http://localhost:3001');
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Custom-Header');
        header('Referrer-Policy: no-referrer-when-downgrade');        
        if (is_array($httpHeaders) && count($httpHeaders)) {
            foreach ($httpHeaders as $httpHeader) {
                header($httpHeader);
            }
        }
        echo $data;
        exit;
    }
}