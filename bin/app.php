#!/usr/bin/env php
<?php
require __DIR__ .'/../vendor/autoload.php';

use Symfony\Component\Console\Application;

define('VERSION', '1.0.0');

/**
 * Get credentials path.
 *
 * @param string $str_file_name
 * @return bool|string
 */
function getCredentialsPath($str_file_name) {
    // current working directory.
    $str_credentials = getcwd() . DIRECTORY_SEPARATOR . $str_file_name;
    if (file_exists($str_credentials)) {
        return $str_credentials;
    }

    // directory above
    $str_credentials = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . $str_file_name;
    if (file_exists($str_credentials)) {
        return $str_credentials;
    }

    // home directory
    $str_credentials = getenv("HOME") . DIRECTORY_SEPARATOR . $str_file_name;
    if (file_exists($str_credentials)) {
        return $str_credentials;
    }

    return false;
}

$str_file_name = 'cae-client-credentials.json';
if (empty(getenv('GOOGLE_APPLICATION_CREDENTIALS'))) {
    $mix_path = getCredentialsPath($str_file_name);

    if (false === $mix_path) {
        $str_error =
            'You MUST have a file called "' . $str_file_name . '" in the client\'s '
            . 'root folder in order to run these commands locally.'
            . PHP_EOL . PHP_EOL
            . 'For information on how to create a service credential account please visit: '
            . 'https://cloud.google.com/iam/docs/service-accounts';
        echo $str_error;

        exit(1);
    }

    putenv('GOOGLE_APPLICATION_CREDENTIALS=' . $mix_path);
}

$obj_application = new Application('GAEClient', VERSION);

// App
$obj_application->add(new Initialize);
$obj_application->add(new BuildImage);

// Google
$obj_application->add(new CreateVersion);
$obj_application->add(new ListVersions);
$obj_application->add(new GetAuthHeader);
$obj_application->run();