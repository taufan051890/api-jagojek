<?php

if (! function_exists('cdn_path')) {
    /**
     * Get the path to the storage folder.
     *
     * @param  string  $path
     * @return string
     */
    function cdn_path($path = '')
    {
        return '/home/sotong/Project/api/cdn/'.$path;
    }
}
