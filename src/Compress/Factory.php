<?php namespace UKCASmith\GAEClient\Compress;

class Factory
{
    const ZIP = 'zip';

    public static function make($str_type) {
        switch($str_type) {
            case static::ZIP:
                return new Zip;
                break;

        }
    }
}