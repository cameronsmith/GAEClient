<?php namespace UKCASmith\GAEClient\Requests;

use UKCASmith\GAEClient\Requests\Clients\GoogleRequestTrait;

class Auth extends Request
{
    use GoogleRequestTrait;

    /**
     * @var array
     */
    protected $arr_scopes = [
        'https://www.googleapis.com/auth/cloud-platform'
    ];

    /**
     * Set scopes.
     *
     * @param array $arr_scopes
     * @return $this
     */
    public function setScopes(array $arr_scopes) {
        $this->arr_scopes = $arr_scopes;
        return $this;
    }

    /**
     * Get scopes.
     *
     * @return array
     */
    protected function getScopes()
    {
        return $this->arr_scopes;
    }

    /**
     * Get OAuth header.
     *
     * @return string
     */
    public function execute()
    {
        return $this->getOAuthHeader();
    }
}