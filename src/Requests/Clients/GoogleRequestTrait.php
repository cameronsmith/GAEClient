<?php namespace UKCASmith\GAEClient\Requests\Clients;

use Google\Auth\ApplicationDefaultCredentials;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Client;

trait GoogleRequestTrait
{
    protected function get($str_endpoint) {
        $obj_client = $this->getClient();
        return $obj_client->get($str_endpoint);
    }

    protected function post($str_endpoint, $str_json_body) {
        $obj_client = $this->getClient();
        return $obj_client->post($str_endpoint, $str_json_body);
    }

    private function getClient() {
        $middleware = ApplicationDefaultCredentials::getMiddleware($this->getScopes());
        $stack = HandlerStack::create();
        $stack->push($middleware);

        return new Client([
            'handler' => $stack,
            'auth' => 'google_auth'
        ]);
    }
}