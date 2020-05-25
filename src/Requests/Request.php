<?php namespace UKCASmith\GAEClient\Requests;

abstract class Request
{
    abstract protected function get($str_endpoint);
    abstract protected function post($str_endpoint, $str_json_body);
    abstract protected function getScopes();
    abstract public function execute();
}