<?php
namespace App\Application;

interface isCrawlable {
    public function setEntity($entity);
    public function setURL($url);
}