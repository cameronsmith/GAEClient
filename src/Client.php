<?php namespace UKCASmith\GAEClient;

use UKCASmith\GAEClient\Requests\Request;
use UKCASmith\GAEClient\Exceptions\InvalidEnvironment;

class Client
{
    protected $obj_request;

    public function request(Request $obj_request) {
        $this->obj_request = $obj_request;
        return $this;
    }

    public function validate(array $arr_deploy, $str_environment) {
        if (!isset($arr_deploy[$str_environment])) {
            $obj_exception = new InvalidEnvironment(
                'No environment found for "' . $str_environment . '". Please make sure you have provided a valid '
                . 'environment key and is the correct case.'
            );
            throw $obj_exception;
        }
    }

    public function call(array $arr_deploy, $str_environment) {
        $this->validate($arr_deploy, $str_environment);
    }
}