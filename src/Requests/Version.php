<?php namespace UKCASmith\GAEClient\Requests;

use UKCASmith\GAEClient\Exceptions\InvalidHttpResponse;
use UKCASmith\GAEClient\Exceptions\InvalidHttpResponseCode;
use UKCASmith\GAEClient\Exceptions\MissingRequestObject;
use UKCASmith\GAEClient\Requests\Clients\GoogleRequestTrait;

class Version extends Request
{
    use GoogleRequestTrait;

    protected $str_project;
    protected $str_id;
    protected $str_instance = 'F1';
    protected $str_runtime = 'php55';
    protected $bol_thread_safe = true;
    protected $str_bucket;
    protected $str_source_zip;
    protected $arr_env = [];

    /**
     * ENDPOINT to call
     */
    const END_POINT = 'https://appengine.googleapis.com/v1/apps/%s/services/default/versions';

    /**
     * ENDPOINT for source zip.
     */
    const SOURCE_ZIP_ENDPOINT = 'https://storage.googleapis.com/%s/%s';

    /**
     * @var array
     */
    protected $arr_scopes = [
        'https://www.googleapis.com/auth/cloud-platform'
    ];

    /**
     * @param $str_project
     * @return $this
     */
    public function setProject($str_project) {
        $this->str_project = $str_project;
        return $this;
    }

    public function setRuntime($str_runtime) {
        $this->str_runtime = $str_runtime;
        return $this;
    }

    /**
     * @param $str_id
     * @return $this
     */
    public function setVersion($str_id) {
        $this->str_id = $str_id;
        return $this;
    }

    /**
     * @param array $arr_env
     * @return $this
     */
    public function setEnvironmentVariables(array $arr_env) {
        $this->arr_env = $arr_env;
        return $this;
    }

    /**
     * @param $str_source_zip
     * @return $this
     */
    public function setSourceZip($str_source_zip) {
        $this->str_source_zip = $str_source_zip;
        return $this;
    }

    /**
     * @param $str_bucket
     * @return $this
     * @internal param $str_source_zip
     */
    public function setBucket($str_bucket) {
        $this->str_bucket = $str_bucket;
        return $this;
    }

    /**
     * Get scopes for request.
     *
     * @return array
     */
    protected function getScopes()
    {
        return $this->arr_scopes;
    }

    protected function getJsonPayload()
    {
        return json_encode([
            'id' => $this->str_id,
            'entrypoint' => [
                'shell' => ''
            ],
            'instanceClass' => $this->str_instance,
            'handlers' => [
                [
                    'script' => [
                        'scriptPath' => 'index.php',
                    ],
                    'securityLevel' => 'SECURE_OPTIONAL',
                    'urlRegex' => '.*',
                ],
            ],
            'runtime' => $this->str_runtime,
            'runtimeApiVersion' => $this->str_runtime,
            'threadsafe' => $this->bol_thread_safe,
            'envVariables' => (object) $this->arr_env,
            'deployment' => [
                'zip' => [
                    'sourceUrl' => sprintf(static::SOURCE_ZIP_ENDPOINT, $this->str_bucket, $this->str_source_zip),
                ]
            ],
        ], JSON_PRETTY_PRINT);
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

        if (empty($this->str_id)) {
            throw new MissingRequestObject(
                'You must supply a version name to the request object.'
            );
        }

        if (empty($this->str_bucket)) {
            throw new MissingRequestObject(
                'You must supply a bucket to the request object.'
            );
        }

        if (empty($this->str_source_zip)) {
            throw new MissingRequestObject(
                'You must supply a source zip to the request object.'
            );
        }
    }

    public function execute()
    {
        $str_payload = $this->getJsonPayload();
        $obj_client = $this->getClient();
        $obj_response = $obj_client->post(sprintf(static::END_POINT, $this->str_project), [
           'body' => $str_payload,
        ]);

        if ($obj_response->getStatusCode() !== 200) {
            throw new InvalidHttpResponseCode(
                'An invalid response code was returned: ' . $obj_response->getBody(),
                $obj_response->getStatusCode()
            );
        }

        $arr_response = json_decode($obj_response->getBody(), true);
        if (empty($arr_response['name'])) {
            throw new InvalidHttpResponse(
                'An invalid response was returned: ' . $obj_response->getBody()
            );
        }

        $arr_name = explode('/', $arr_response['name']);
        return end($arr_name);
    }
}