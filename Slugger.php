<?php namespace Slimak\Support;

class Slugger
{
    /**
     * Replace non-alphanumeric characters with slashes
     * Then replace multiple slashes with single slash and trim slashes
     *
     * @param string $string
     * @param bool   $convert_to_lowercase
     * @param string $glue
     *
     * @return string
     */
    public static function slugify($string, $convert_to_lowercase = false, $glue = '-')
    {
        if ($convert_to_lowercase) {
            $string = strtolower($string);
        }

        // Spell out ampersands and remove apostrophes from contractions
        $string = str_replace(['&', "'"], ['and', ''], $string);

        // Convert non alpha-numeric characters to glue pieces
        $string = preg_replace('/[^a-zA-Z0-9]/', $glue, strip_tags($string));

        // Replace multiple glue pieces with a single piece, trim glue from ends
        if (strlen($glue) > 0) {
            $string = trim(preg_replace('/(' . $glue . ')+/', $glue, $string), $glue);
        }

        return $string;
    }
}
