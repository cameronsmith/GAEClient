<?php namespace UKCASmith\GAEClient\Utils;

use UKCASmith\GAEClient\Exceptions\CannotLocateDeployFile;
use UKCASmith\GAEClient\Exceptions\InvalidEnvironment;
use UKCASmith\GAEClient\Exceptions\MissingRequiredKey;

class DeployFile
{
    /**
     * @var array
     */
    protected $arr_json;

    /**
     * @var string
     */
    protected $str_environment;

    /**
     * DeployFile constructor.
     *
     * @param array $arr_json
     * @param string $str_environment
     */
    public function __construct(array $arr_json, $str_environment)
    {
        static::validate($arr_json, $str_environment);
        $this->arr_json = $arr_json;
        $this->str_environment = $str_environment;
    }

    /**
     * Get required key.
     *
     * @param $str_key
     * @return mixed
     * @throws MissingRequiredKey
     */
    public function getRequired($str_key)
    {
        if (!isset($this->arr_json[$this->str_environment][$str_key])) {
            throw new MissingRequiredKey(
                'Missing required key from deployment file: [' . $this->str_environment . '][' . $str_key . ']'
            );
        }

        return $this->arr_json[$this->str_environment][$str_key];
    }

    /**
     * Validate main deploy file.
     *
     * @param array $arr_json
     * @param $str_environment
     * @throws InvalidEnvironment
     */
    public static function validate(array $arr_json, $str_environment)
    {
        if (!isset($arr_json[$str_environment])) {
            $obj_exception = new InvalidEnvironment(
                'No environment found for "' . $str_environment . '". Please make sure you have provided a valid '
                . 'environment key and is the correct case.'
            );
            throw $obj_exception;
        }
    }

    /**
     * Get deploy file in order of: deploy.local.json, deploy.json
     *
     * @return array
     * @throws CannotLocateDeployFile
     */
    public static function getDeploySettings()
    {
        $str_deploy_file = getcwd() . DIRECTORY_SEPARATOR . 'deploy.local.json';
        if (file_exists($str_deploy_file)) {
            return json_decode(file_get_contents($str_deploy_file), true);
        }

        $str_deploy_file = getcwd() . DIRECTORY_SEPARATOR . 'deploy.json';
        if (file_exists($str_deploy_file)) {
            return json_decode(file_get_contents($str_deploy_file), true);
        }

        throw new CannotLocateDeployFile(
            'Cannot locate deploy.json file within your current working directory.' . PHP_EOL .
            'For more information please refer to https://github.com/uk-casmith/GAEClient to learn how to creating deploy.json files.'
        );
    }
}