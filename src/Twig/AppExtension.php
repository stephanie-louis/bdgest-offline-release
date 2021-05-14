<?php
namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AppExtension extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('fileFromURL', [$this, 'getFileNameFromURL']),
        ];
    }

    /**
     * @param string $url
     * @return string
     */
    public function getFileNameFromURL($url)
    {
        $file = '';
        $pos = 0;
        if ($pos = strrpos($url, '/')){
            $file = substr($url, $pos + 1, strlen($url));
        }
        return $file;
    }
}
