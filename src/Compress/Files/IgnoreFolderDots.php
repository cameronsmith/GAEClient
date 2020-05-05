<?php namespace UKCASmith\GAEClient\Compress\Files;

use UKCASmith\GAEClient\Compress\DirectoryCollection;

class IgnoreFolderDots
{
    /**
     * Get files ignoring folders in the root directory with dots(.).
     *
     * @param $str_path
     * @return \RecursiveIteratorIterator
     */
    public function get($str_path) {
        $obj_directory_collection = new DirectoryCollection;
        $obj_directory_iterator = new \RecursiveDirectoryIterator($str_path);
        foreach($obj_directory_iterator as $str_key => $obj_file) {
            $str_filename = $obj_file->getFilename();
            if ($obj_file->isDir() && substr($str_filename, 0, 1) === '.') {
                continue;
            }

            $obj_directory_collection->add($str_key, $obj_file);
            if ($obj_directory_iterator->hasChildren()) {
                $obj_directory_collection->addChildren($str_key, $obj_directory_iterator->getChildren());
            }
        }

        return new \RecursiveIteratorIterator(
            $obj_directory_collection,
            \RecursiveIteratorIterator::LEAVES_ONLY
        );
    }
}