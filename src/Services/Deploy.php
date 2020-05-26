<?php namespace UKCASmith\GAEClient\Services;

use Google\Cloud\Storage\StorageClient;
use UKCASmith\GAEClient\Compress\Factory;
use UKCASmith\GAEClient\Compress\Files\IgnoreFolderDots;
use UKCASmith\GAEClient\Requests\Status;
use UKCASmith\GAEClient\Requests\Version;
use UKCASmith\GAEClient\Utils\DeployFile;

class Deploy
{
    /**
     * Steps
     */
    const STEPS_SUCCESS = 1;
    const STEPS_COMPRESS = 2;
    const STEPS_UPLOAD = 3;
    const STEPS_DEPLOY = 4;
    const STEPS_CONFIRM = 5;
    const STEPS_FAILED = 6;
    const STEPS_CLEANUP = 7;

    /**
     * @var string
     */
    protected $str_custom_label;

    /**
     * Override label in configuration.
     *
     * @param $str_label
     * @return $this
     */
    public function setCustomLabel($str_label)
    {
        $this->str_custom_label = $str_label;
        return $this;
    }

    /**
     * Process deployment.
     *
     * @param array $arr_deploy_json
     * @param string $str_environment
     * @return \Generator
     */
    public function process($arr_deploy_json, $str_environment)
    {
        $obj_deploy_file = new DeployFile($arr_deploy_json, $str_environment);
        $str_bucket = $obj_deploy_file->getRequired('bucket');
        $str_project = $obj_deploy_file->getRequired('project');
        $arr_environments = $obj_deploy_file->getRequired('env');
        $str_version = !empty($this->str_custom_label) ? $this->str_custom_label : $obj_deploy_file->getRequired('version');

        yield static::STEPS_COMPRESS;
        $str_file_name = $this->compress(getcwd());
        yield static::STEPS_SUCCESS;

        yield static::STEPS_UPLOAD;
        $this->upload($str_file_name, $str_bucket);
        yield static::STEPS_SUCCESS;

        yield static::STEPS_DEPLOY;
        $obj_version_request = new Version;
        $str_operation_id = $obj_version_request
            ->setProject($str_project)
            ->setVersion($str_version)
            ->setEnvironmentVariables($arr_environments)
            ->setBucket($str_bucket)
            ->setSourceZip(basename($str_file_name))
            ->execute();
        yield static::STEPS_SUCCESS;

        $obj_status_request = new Status;
        $obj_status_request
            ->setProject($str_project)
            ->setOperation($str_operation_id);

        yield static::STEPS_CLEANUP;
        unlink($str_file_name);
        yield static::STEPS_SUCCESS;

        yield static::STEPS_CONFIRM;
        $arr_response = $this->checkStatus($obj_status_request);

        if (isset($arr_response['response']['versionUrl'])) {
            yield static::STEPS_SUCCESS;
        } else {
            yield static::STEPS_FAILED;
        }

        return $arr_response;
    }

    /**
     * Compress source code.
     *
     * @param string $str_path
     * @return string
     */
    public function compress($str_path)
    {
        $obj_compress = Factory::make(Factory::ZIP);
        $obj_files = new IgnoreFolderDots;
        $str_filename = $obj_compress->build($obj_files->get($str_path));
        $str_sha_name = sha1_file($str_filename);

        $arr_path_parts = pathinfo($str_filename);
        $str_new_path_file = $arr_path_parts['dirname'] . DIRECTORY_SEPARATOR . $str_sha_name . '.' . Factory::ZIP;
        rename($str_filename, $str_new_path_file);

        return $str_new_path_file;
    }

    /**
     * Upload source code.
     *
     * @param string $str_file_name
     * @param $str_bucket
     */
    public function upload($str_file_name, $str_bucket)
    {
        $obj_storage = new StorageClient;
        $obj_bucket = $obj_storage->bucket($str_bucket);
        $obj_bucket->upload(
            fopen($str_file_name, 'r')
        );
    }

    /**
     * @param Status $obj_status_request
     * @return Status
     */
    protected function checkStatus(Status $obj_status_request)
    {
        $bol_complete = false;
        while ($bol_complete === false) {
            $bol_complete = $obj_status_request->execute();
            sleep(1);
        }

        return $obj_status_request->getResponse();
    }
}