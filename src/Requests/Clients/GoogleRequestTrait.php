<?php namespace UKCASmith\GAEClient\Requests\Clients;

use Google\Auth\ApplicationDefaultCredentials;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\ServerRequest;

trait GoogleRequestTrait
{
    protected function get($str_endpoint)
    {
        $obj_client = $this->getClient();
        return $obj_client->get($str_endpoint);
    }

    protected function post($str_endpoint, $str_json_body)
    {
        $obj_client = $this->getClient();
        return $obj_client->post($str_endpoint, $str_json_body);
    }

    private function getClient()
    {
        $middleware = ApplicationDefaultCredentials::getMiddleware($this->getScopes());
        $stack = HandlerStack::create();
        $stack->push($middleware);

        return new Client([
            'handler' => $stack,
            'auth' => 'google_auth'
        ]);
    }

    /**
     * Get OAuth header.
     *
     * @return string
     */
    private function getOAuthHeader()
    {
        $obj_middleware = ApplicationDefaultCredentials::getMiddleware($this->getScopes());
        $obj_empty_request = new ServerRequest('POST', '');
        $obj_handler = $obj_middleware(function ($obj_request, $arr_options = []) {
            return $obj_request;
        });

        $obj_request = $obj_handler($obj_empty_request, ['auth' => 'google_auth']);
        return $obj_request->getHeader('authorization')[0];
    }
}