#!/usr/bin/env php
<?php
require __DIR__ .'/../vendor/autoload.php';

use Symfony\Component\Console\Application;

define('VERSION', '1.0.0');

$str_file_name = 'cae-client-credentials.json';

$bol_found_credentials = false;
$str_credentials = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . $str_file_name;
if (file_exists($str_credentials)) {
    $bol_found_credentials = true;
}

if (!file_exists(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . $str_file_name)) {
    $str_error =
        'You MUST have a file called "' . $str_file_name . '" in the client\'s '
        . 'root folder in order to run these commands locally.'
        . PHP_EOL . PHP_EOL
        . 'For information on how to create a service credential account please visit: '
        . 'https://cloud.google.com/iam/docs/service-accounts';
    echo $str_error;

    exit(1);
}

putenv('GOOGLE_APPLICATION_CREDENTIALS=' . $str_credentials);

$obj_application = new Application('GAEClient', VERSION);
// App
$obj_application->add(new Initialize);
$obj_application->add(new BuildImage);

// Google
$obj_application->add(new CreateVersion);
$obj_application->add(new ListVersions);
$obj_application->add(new GetAuthHeader);
$obj_application->run();