<?php

namespace App\Service;


use function Symfony\Component\DependencyInjection\Loader\Configurator\ref;

class StringManager
{
    /**
     * @param string $html
     * @return string
     */
    public function getLabel(string $html)
    {
        $s = $html;
        preg_match('#<label>(.*)</label>#', $html, $match);
        if (count($match) > 0)
            return $match[1];
        else
            return '';
    }

    /**
     * @param string $label
     * @return mixed|string
     */
    public function cleanLabel(string $label)
    {
        $cleaned = str_replace(':', '', $label);
        $cleaned = trim($cleaned);
        return $cleaned;
    }

    /**
     * @param string $html
     * @return string
     */
    public function getValueOfLabel(string $html)
    {
        $html = str_replace(array("\n", "\r"), '', $html);
        preg_match('#<label>(.*)</label>(.*)#', $html, $match);
        $value = '';
        if (count($match) > 1) $value = trim(strip_tags($match[2]));
        if ($value === null) $value = '';

        return $value;
    }

    /**
     * @param string $url
     * @return string
     */
    public static function getFileNameFromURL($url)
    {
        $file = '';
        $pos = 0;
        if ($pos = strrpos($url, '/')){
            $file = substr($url, $pos + 1, strlen($url));
        }
        return $file;
    }
}