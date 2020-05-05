<?php namespace UKCASmith\GAEClient\Requests;

use UKCASmith\GAEClient\Requests\Clients\GoogleRequestTrait;

class Version extends Request
{
    use GoogleRequestTrait;

    /**
     * ENDPOINT to call
     */
    const END_POINT = 'https://appengine.googleapis.com/v1/apps/%s/services/default/versions';

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

    public function call()
    {

    }
}