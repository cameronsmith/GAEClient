<?php namespace UKCASmith\GAEClient\Requests;

use UKCASmith\GAEClient\Exceptions\InvalidHttpResponseCode;
use UKCASmith\GAEClient\Exceptions\MissingRequestObject;
use UKCASmith\GAEClient\Requests\Clients\GoogleRequestTrait;

class Status extends Request
{
    use GoogleRequestTrait;

    protected $str_project;
    protected $str_operation;
    protected $arr_response;

    /**
     * ENDPOINT to call
     */
    const END_POINT = 'https://appengine.googleapis.com/v1/apps/%s/operations/%s';

    /**
     * @var array
     */
    protected $arr_scopes = [
        'https://www.googleapis.com/auth/cloud-platform'
    ];

    /**
     * Get scopes for request.
     *
     * @return array
     */
    protected function getScopes()
    {
        return $this->arr_scopes;
    }

    /**
     * @param $str_project
     * @return $this
     */
    public function setProject($str_project) {
        $this->str_project = $str_project;
        return $this;
    }

    /**
     * @param $str_operation
     * @return $this
     */
    public function setOperation($str_operation) {
        $this->str_operation = $str_operation;
        return $this;
    }

    public function getResponse() {
        return $this->arr_response;
    }


    /**
     * Validate.
     *
     * @throws MissingRequestObject
     */
    public function validate() {
        if (empty($this->str_project)) {
            throw new MissingRequestObject(
                'You must supply a project name to the request object.'
            );
        }

        if (empty($this->str_operation)) {
            throw new MissingRequestObject(
                'You must supply an operation to the request object.'
            );
        }
    }


    public function execute()
    {
        $this->validate();
        $obj_client = $this->getClient();
        $obj_response = $obj_client->get(sprintf(static::END_POINT, $this->str_project, $this->str_operation));

        if ($obj_response->getStatusCode() !== 200) {
            throw new InvalidHttpResponseCode(
                'An invalid response code was returned: ' . $obj_response->getBody(),
                $obj_response->getStatusCode()
            );
        }

        $arr_response = json_decode($obj_response->getBody(), true);
        $this->arr_response = $arr_response;

        return isset($arr_response['done']);
    }
}