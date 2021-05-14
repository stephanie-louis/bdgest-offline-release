<?php

namespace App\Application;

use App\Entity\Album;
use App\Entity\Serie;
use App\Service\StringManager;
use Doctrine\Persistence\ObjectManager;

class Importer
{

    protected $importerAlbum;
    protected $importerSerie;
    protected $projectDir;

    public function  __construct($projectDir)
    {
        $this->projectDir = $projectDir;
        $this->importerAlbum = new Import();
        $this->importerAlbum->setEntity('Album');
        $this->importerAlbum->setURL('https://www.bedetheque.com/BD-titre-');

        $this->importerSerie = new Import();
        $this->importerSerie->setEntity('Serie');
        $this->importerSerie->setURL('https://www.bedetheque.com/serie-XX-BD-titre.html');

    }

    public function getAlbumImportProjectDir(){
        return $this->projectDir;
    }
    public function getAlbumImportConfig(){
        return $this->importerAlbum;
    }
    public function getSerieImportConfig(){
        return $this->importerSerie;
    }

    /**
     * @param array $structure
     * @param Serie $serie
     */
    public function mapSerie(array $structure, Serie &$serie)
    {
        foreach ($structure as $ligne){
            $sm = new StringManager();
            $field = $sm->getLabel($ligne['html']);
            $field = $sm->cleanLabel($field);
            $value = $sm->getValueOfLabel($ligne['html']);
            switch (strtolower($field)){
                case 'identifiant':
                    $serie->setIdBdgest(strip_tags($value));
                    break;
                case 'genre':
                    $serie->setGenre(strip_tags($value));
                    break;
                case 'parution':
                    $encours = (strpos(strip_tags($value), 'cours') > 0);
                    $serie->setEnCours($encours);
                    break;
                default:
                    break;
            }
        }
        return;
    }

    /**
     * @param array $structure
     * @param Album $album
     */
    public function mapAlbum(array $structure, Album &$album)
    {
        foreach ($structure as $ligne){
            $sm = new StringManager();
            $field = $sm->getLabel($ligne['html']);
            $field = $sm->cleanLabel($field);
            $value = $sm->getValueOfLabel($ligne['html']);
            $value = strip_tags($value);
//            if ($value === null) $value='';
            switch (mb_strtolower($field)){
                case 'titre':
                    $album->setTitre($value);
                    break;
                case 'tome':
                    $album->setTome($value);
                    break;
                case 'identifiant':
                    $album->setIdBdgest($value);
                    break;
                case 'dépot légal':
                    $album->setDepotLegal($value);
                    break;
                case 'isbn':
                    $album->setIsbn($value);
                    break;
                case 'planches':
                    $album->setPlanches((int)$value);
                    break;
                case 'format':
                    $album->setFormat($value);
                    break;
                case 'editeur':
                    $album->setEditeur($value);
                    break;
                case 'scénario':
                    $album->setScenariste($value);
                    break;
                case 'dessin':
                    $album->setDessinateur($value);
                    break;
                default:
                    break;
            }
        }
        return;
    }

    /**
     * @param int $id_bdgest
     * @param ObjectManager $em
     * @param bool $wishlist
     * @return array
     */
    public function importAlbumAndSerieFromIdAlbum(int $id_bdgest, ObjectManager $em, bool $wishlist = false): array
    {
        $album = CrawlerPage::crawlAlbum($this, $id_bdgest);
        $myalbum = $album['album'];
        $serie = CrawlerPage::crawlSerie($this, $album['urlSerie']);

        $seriExists = $em->getRepository(Serie::class)->findOneBy(['idbdgest' => $serie->getIdbdgest()]);
        if ($seriExists === null) {
            $myalbum->setSerie($serie);
            $em->persist($serie);
        } else {
            $myalbum->setSerie($seriExists);
        }
        $albumExists = $em->getRepository(Album::class)->findOneBy(['idbdgest' => $myalbum->getIdbdgest()]);
        if ($albumExists === null) {
            if (! $wishlist && $myalbum->getDateachat() !== '' && $myalbum->getDateachat() !== null) {
                $dt = \DateTime::createFromFormat('d/m/Y', $myalbum->getDateachat());
                $myalbum->setDateachat($dt);
            }
            elseif (! $wishlist) {
                $now = new \DateTime('now');
                $dt = \DateTime::createFromFormat('d/m/Y', $now->format('d/m/Y'));
                $myalbum->setDateachat($dt);
            }
            $myalbum->setWhislist($wishlist);
            $em->persist($myalbum);
        }

        $em->flush();

        return [
            'myalbum' => $myalbum,
            'serie' => $serie,
            'albumExists' => $albumExists,
            'seriExists' => $seriExists,
        ];
    }

    public function importAlbumAndSerieFromAlbumObject(array $talbum, ObjectManager $em)
    {
        $retour = '';
        $albumExists = $em->getRepository(Album::class)->findOneBy(['idbdgest' => $talbum['idbdgest']]);
        if ($albumExists === null)
        {
            // Crawl album page in order to get url serie and crawl serie page.
            $myalbum = CrawlerPage::crawlAlbum($this, $talbum['idbdgest']);
            $serie = CrawlerPage::crawlSerie($this, $myalbum['urlSerie']);

            // Create Album
            $album = new Album();
            $album->setIdbdgest($talbum['idbdgest']);
            $album->setIsbn($talbum['isbn']);
            $album->setTome($talbum['tome']);
            $album->setTitre($talbum['titre']);
            $album->setEditeur($talbum['editeur']);
            $album->setDepotLegal($talbum['depotLegal']);
            if ($talbum['dateachat'] !== '') {
                $dt = \DateTime::createFromFormat('d/m/Y', $talbum['dateachat']);
                $album->setDateachat($dt);
            }
            $album->setPrix((float)$talbum['prix']);
            $album->setScenariste($talbum['scenariste']);
            $album->setDessinateur($talbum['dessinateur']);
            $album->setWhislist($talbum['whislist']);
            $album->setFormat($talbum['format']);
            $album->setPlanches($myalbum['album']->getPlanches());
            $album->setImgCouverture($myalbum['album']->getImgCouverture());

            $seriExists = $em->getRepository(Serie::class)->findOneBy(['idbdgest' => $serie->getIdbdgest()]);
            if ($seriExists === null) {
                $album->setSerie($serie);
                $em->persist($serie);
                $retour .= '<br>Série Importée: '. $talbum['serie'];
            } else {
                $album->setSerie($seriExists);
                $retour .= '<br>Série Déjà présente: '. $talbum['serie'];
            }
            $em->persist($album);
            $retour .= '<br>Album Importé (' . $talbum['idbdgest'] . '): ' . $talbum['titre'];
            print 'Album importé: ' .  $talbum['idbdgest'] . '): ' . $talbum['titre'] . '<br>';
            $em->flush();
        }
        else {
            $retour = '<br>Album Déjà présent (' . $talbum['idbdgest'] . '): ' . $talbum['titre'];
            print 'Album existe déjà: ' .  $talbum['idbdgest'] . '): ' . $talbum['titre'] . '<br>';
        }

        return $retour;
    }
}