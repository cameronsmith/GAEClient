<?php
/**
 * Created by PhpStorm.
 * User: cameron
 * Date: 04/05/20
 * Time: 18:43
 */

namespace UKCASmith\GAEClient\Compress\Files;


interface FilesInterface
{
    /**
     * Get files.
     *
     * @param string $str_path
     * @return \RecursiveIteratorIterator
     */
    public function get($str_path);
}