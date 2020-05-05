<?php namespace UKCASmith\GAEClient\Services;

use Google\Cloud\Storage\StorageClient;
use UKCASmith\GAEClient\Client;
use UKCASmith\GAEClient\Compress\Factory;
use UKCASmith\GAEClient\Compress\Files\IgnoreFolderDots;

class Deploy
{
    /**
     * Steps
     */
    const STEPS_SUCCESS = 1;
    const STEPS_COMPRESS = 2;
    const STEPS_UPLOAD = 3;
    const STEPS_DEPLOY = 4;

    /**
     * Process deployment.
     *
     * @param array $arr_deploy_json
     * @param string $str_environment
     * @param bool $bol_pause
     * @return \Generator
     */
    public function process($arr_deploy_json, $str_environment, $bol_pause = false) {
        $obj_client = new Client;
        $obj_client->validate($arr_deploy_json, $str_environment);

        if ($bol_pause) yield static::STEPS_COMPRESS;
        $str_file_name = $this->compress(getcwd());
        if ($bol_pause) yield static::STEPS_SUCCESS;

        if ($bol_pause) yield static::STEPS_UPLOAD;
        $this->upload($str_file_name);
        if ($bol_pause) yield static::STEPS_SUCCESS;

        if ($bol_pause) yield static::STEPS_DEPLOY;
        $obj_client->call($arr_deploy_json, $str_environment);
        if ($bol_pause) yield static::STEPS_SUCCESS;
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