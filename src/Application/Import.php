<?php
namespace App\Application;
use App\Application\isCrawlable;

class Import implements isCrawlable
{
    protected $url;
    public function setEntity($entity)
    {
        // TODO: Implement setEntity() method.
    }

    public function setURL($url)
    {
        // TODO: Implement setURL() method.
        $this->url = $url;
    }
    public function getURL()
    {
        return $this->url;
    }


}