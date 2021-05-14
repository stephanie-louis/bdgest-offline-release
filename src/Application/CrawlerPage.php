<?php

namespace App\Application;


use App\Entity\Album;
use App\Entity\Serie;
use App\Service\MyHttpClient;
use App\Service\StringManager;
use Symfony\Component\DomCrawler\Crawler;

class CrawlerPage
{


    /**
     * @param Importer $imp
     * @param string $url
     *
     * @return Serie
     */
    public static function crawlSerie(Importer $imp, string $url)
    {
        $serie = new Serie();
        $importSerie = $imp->getSerieImportConfig();
        $client = new MyHttpClient($url);
        $htmlBdgest = $client->getHtmlContent();
        if ($htmlBdgest !== false) {

            $myCrawler = new Crawler($htmlBdgest);
            //TODO: check if node exists
            $structure = $myCrawler->filter('.serie-info > li')->each(function (Crawler $crawler) {
                return [
                    'text' => $crawler->filter('li')->text(),
                    'html' => $crawler->filter('li')->html(),
                ];
            });
            if (count($myCrawler->filter('.bandeau-info.serie h1 a'))){
                $titre = $myCrawler->filter('.bandeau-info.serie h1 a')->text();
                $serie->setTitre($titre);
            }

            if (count($myCrawler->filter('.serie-image a > img'))) {
                $img = $myCrawler->filter('.serie-image a > img')->image();
                $serie->setImgPlanche($img->getUri());
            }
            if (count($myCrawler->filter('.single-content.serie > p'))) {
                $resume = $myCrawler->filter('.single-content.serie > p')->html();
                $serie->setResume($resume);
            }

            $imp->mapSerie($structure, $serie);
        }
        return $serie;
    }

    /**
     * @param Importer $imp
     * @param int $id
     * @return array
     */
    public static function crawlAlbum(Importer $imp, int $id)
    {
        $album = new Album();
        $urlSerie = '';
        $importAlbum = $imp->getAlbumImportConfig();
        $url = $importAlbum->getURL() . $id . '.html';

        $client = new MyHttpClient($url);
        $htmlBdgest = $client->getHtmlContent();
        if ($htmlBdgest !== false) {

            $myCrawler = new Crawler($htmlBdgest);
            //TODO: check if node exists
            $structure = $myCrawler->filter('.detail-album .infos-albums > li')->each(function (Crawler $crawler) {
                return [
                    'text' => $crawler->filter('li')->text(),
                    'html' => $crawler->filter('li')->html(),
                ];
            });
            if (count($myCrawler->filter('.bandeau-image.album a > img'))){
                $img = $myCrawler->filter('.bandeau-image.album a > img')->attr('src');
                $album->setImgCouverture($img);

                $content = file_get_contents($img);
                file_put_contents($imp->getAlbumImportProjectDir() . '/public/images/album/couverture/' . StringManager::getFileNameFromURL($img), $content);
            }
            if (count($myCrawler->filter('.bandeau-info.album h1 a'))){
                $urlSerie = $myCrawler->filter('.bandeau-info.album h1 a')->attr('href');
            }

            $imp->mapAlbum($structure, $album);
        }

        return [
            'album' => $album,
            'urlSerie' => $urlSerie
        ];
    }
}