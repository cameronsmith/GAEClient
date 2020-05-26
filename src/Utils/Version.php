<?php namespace UKCASmith\GAEClient\Utils;

class Version
{
    /**
     * Get version.
     *
     * @return string
     * @throws \Exception
     */
    public static function get() {
        $str_composer_file =
            __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'composer.json';

        if (!file_exists($str_composer_file)) {
            throw new \Exception('Unable to locate composer file.');
        }
        $arr_composer = json_decode(file_get_contents($str_composer_file), true);
        if (empty($arr_composer['version'])) {
            throw new \Exception('The version of the application contained within composer.json is empty!.');
        }

        return $arr_composer['version'];
    }
}