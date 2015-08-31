<?php namespace App\Support;

class Slugger
{
    /**
     * Replace non-alphanumeric characters with slashes
     * Then replace multiple slashes with single slash and trim slashes
     *
     * @param  string  $string
     * @param  bool  $convert_to_lowercase;
     * @param  string  $glue
     * @return string
     */
    public static function slugify($string, $convert_to_lowercase, $glue = '-')
    {
        if ($convert_to_lowercase) {
            $string = strtolower($string);
        }
        $string = str_replace('&', 'and', $string);

        $string = preg_replace('/[^a-zA-Z0-9]/', $glue, strip_tags($string));

        return trim(preg_replace('/(' . $glue . ')+/', $glue, $string), $glue);
    }
}

