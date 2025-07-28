<?php

if (!function_exists('formatBytes')) {
    /**
     * Format bytes to human readable format
     *
     * @param int $size Size in bytes
     * @param int $precision Number of decimal places
     * @return string Formatted size
     */
    function formatBytes($size, $precision = 2)
    {
        if ($size == 0) {
            return '0 B';
        }

        $base = log($size, 1024);
        $suffixes = ['B', 'KB', 'MB', 'GB', 'TB'];

        return round(pow(1024, $base - floor($base)), $precision) . ' ' . $suffixes[floor($base)];
    }
}
