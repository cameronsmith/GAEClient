<?php namespace UKCASmith\GAEClient\Compress;

use RecursiveIteratorIterator;
use UKCASmith\GAEClient\Compress\Files\FilesInterface;

class Zip
{
    /**
     * Build zip file.
     *
     * @param RecursiveIteratorIterator $obj_files
     * @param string $str_filename
     * @return string
     */
    public function build(RecursiveIteratorIterator $obj_files, $str_filename = 'gae-tmp-output.zip') {
        $str_cwd = getcwd();
        $str_build_path = $str_cwd . DIRECTORY_SEPARATOR . '.gae-output';

        if (!file_exists($str_build_path)) {
            mkdir($str_build_path, 0777, true);
        }

        $obj_zip = new \ZipArchive();
        $obj_zip->open(
            $str_build_path . DIRECTORY_SEPARATOR . $str_filename,
            \ZipArchive::CREATE | \ZipArchive::OVERWRITE
        );

        foreach ($obj_files as $str_name => $obj_file)
        {
            // Skip directories they are added automatically
            if ($obj_file->isDir()) {
                continue;
            }

            $filePath = $obj_file->getRealPath();
            $relativePath = substr($filePath, strlen($str_cwd) + 1);

            $obj_zip->addFile($filePath, $relativePath);
        }

        $obj_zip->close();

        return $str_build_path . DIRECTORY_SEPARATOR . $str_filename;
    }
}