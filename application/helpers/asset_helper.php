<?php 
defined('BASEPATH') || exit('No direct script access allowed');

if (!function_exists('asset'))
{
    /**
     * Get the url from assets folder or the link, script tags
     *
     * @param string  $uri
     * @param boolean $withTags
     * @return string
     */
    function asset($uri, $withTags = false) {
        $ci =& get_instance();
        $uri = '/assets/'.ltrim($uri, DIRECTORY_SEPARATOR);
        $url = $ci->config->base_url($uri);
        if ($withTags === true) {
            $extension = null;
            $posDot = strrpos($uri, '.');
            if ($posDot !== false) {
                $extension = substr($uri, $posDot + 1);
            }
            switch ($extension) {
                case 'js':
                    return '<script src="'.$url.'" type="text/javascript"></script>';
                case 'css':
                    return '<link href="'.$url.'" rel="stylesheet" type="text/css">';
                default: 
                    return $url;
            }
        }
        return $url;
    }
}
