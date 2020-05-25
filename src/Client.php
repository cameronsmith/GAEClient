<?php namespace UKCASmith\GAEClient;

use UKCASmith\GAEClient\Exceptions\MissingRequestObject;
use UKCASmith\GAEClient\Requests\Request;
use UKCASmith\GAEClient\Exceptions\InvalidEnvironment;

class Client
{
    /**
     * @var Request
     */
    protected $obj_request;

    /**
     * Add request.
     *
     * @param Request $obj_request
     * @return $this
     */
    public function addRequest(Request $obj_request) {
        $this->obj_request = $obj_request;
        return $this;
    }

    /**
     * Validate the request.
     *
     * @param array $arr_deploy
     * @param $str_environment
     * @throws InvalidEnvironment
     */
    public function validate(array $arr_deploy, $str_environment) {
        if (!isset($arr_deploy[$str_environment])) {
            $obj_exception = new InvalidEnvironment(
                'No environment found for "' . $str_environment . '". Please make sure you have provided a valid '
                . 'environment key and is the correct case.'
            );
            throw $obj_exception;
        }
    }

    /**
     * Make the request call.
     *
     * @param array $arr_deploy
     * @param $str_environment
     * @throws MissingRequestObject
     */
    public function call(array $arr_deploy, $str_environment) {
        if (empty($obj_request)) {
            throw new MissingRequestObject(
                'You must supply a request object before making attempting to make the request'
            );
        }
        $this->validate($arr_deploy, $str_environment);
    }
}