<?php namespace UKCASmith\GAEClient\Services;

use Google\Cloud\Storage\StorageClient;
use UKCASmith\GAEClient\Client;
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

    /**
     * Process deployment.
     *
     * @param array $arr_deploy_json
     * @param string $str_environment
     * @param bool $bol_pause
     * @return \Generator
     */
    public function process($arr_deploy_json, $str_environment, $bol_pause = false) {
        $obj_deploy_file = new DeployFile($arr_deploy_json, $str_environment);
        $str_bucket = $obj_deploy_file->getRequired('bucket');
        $str_project = $obj_deploy_file->getRequired('project');

        if ($bol_pause) yield static::STEPS_COMPRESS;
        $str_file_name = $this->compress(getcwd());
        if ($bol_pause) yield static::STEPS_SUCCESS;

        if ($bol_pause) yield static::STEPS_UPLOAD;
        $this->upload($str_file_name, $str_bucket);
        if ($bol_pause) yield static::STEPS_SUCCESS;

        if ($bol_pause) yield static::STEPS_DEPLOY;
        $obj_version_request = new Version;
        $str_operation_id = $obj_version_request
            ->setProject($str_project)
            ->setVersion($obj_deploy_file->getRequired('version'))
            ->setBucket($str_bucket)
            ->setSourceZip(basename($str_file_name))
            ->execute();
        if ($bol_pause) yield static::STEPS_SUCCESS;

        $obj_status_request = new Status;
        $obj_status_request
            ->setProject($str_project)
            ->setOperation($str_operation_id);

        if ($bol_pause) yield static::STEPS_CONFIRM;
        $arr_response = $this->checkStatus($obj_status_request);

        if (isset($arr_response['response']['versionUrl'])) {
            if ($bol_pause) yield static::STEPS_SUCCESS;
        } else {
            if ($bol_pause) yield static::STEPS_FAILED;
        }

        return $arr_response;
    }

    /**
     * @param Status $obj_status_request
     * @return Status
     */
    protected function checkStatus(Status $obj_status_request) {
        $bol_complete = false;
        while($bol_complete === false) {
            $bol_complete = $obj_status_request->execute();
            sleep(1);
        }

        return $obj_status_request->getResponse();
    }

    /**
     * Compress source code.
     *
     * @param string $str_path
     * @return string
     */
    protected function compress($str_path) {
        $obj_compress = Factory::make(Factory::ZIP);
        $obj_files = new IgnoreFolderDots;
        return $obj_compress->build($obj_files->get($str_path));
    }

    /**
     * Upload source code.
     *
     * @param string $str_file_name
     * @param $str_bucket
     */
    protected function upload($str_file_name, $str_bucket) {
        $obj_storage = new StorageClient;
        $obj_bucket = $obj_storage->bucket($str_bucket);
        $obj_bucket->upload(
            fopen($str_file_name, 'r')
        );
    }
}