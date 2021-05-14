<?php
namespace App\Application;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

Class ImportCSV
{
    protected $csvFile = '';
    protected $content;
    private $private_path = '../private/csv/';
    protected $mappingData = [
        'IdAlbum' => 'idbdgest',
        'ISBN' => 'isbn',
        'Serie' => 'serie',
        'Num' => 'tome',
        'NumA' => '',
        'Titre' => 'titre',
        'Editeur' => 'editeur',
        'Collection' => '',
        'EO' => '',
        'DL' => 'depotLegal',
        'AI' => '',
        'Cote' => '',
        'Etat' => '',
        'DateAchat' => 'dateachat',
        'PrixAchat' => 'prix',
        'Note' => '',
        'Scenariste' => 'scenariste',
        'Dessinateur' => 'dessinateur',
        'Wishlist' => 'whislist',
        'AVendre' => '',
        'Perso1' => '',
        'Perso2' => '',
        'Perso3' => '',
        'Perso4' => '',
        'Format' => 'format',
        'Suivi' => '',
        'Commentaire' => '',
        'Table' => ''];

    function  __construct($csv)
    {
        $this->csvFile = $_SERVER['DOCUMENT_ROOT'] . '/' . $this->private_path . $csv;
        $fs = new Filesystem();
        $finder = new Finder();
        $finder->name($csv)->in($_SERVER['DOCUMENT_ROOT'] . '/' . $this->private_path);
        if ($finder->count() === 1)
        foreach ($finder as $file) {
            $this->content = $file->getContents();
        }
    }

    /**
     * @return bool|mixed
     */
    public function serializeCsv()
    {
        if ($this->content !== false)
        {
            $serializer = new Serializer([new ObjectNormalizer()], [new CsvEncoder(['csv_delimiter' => ';'])]);
            $decoded = $serializer->decode($this->content,'csv');

            return $decoded;
        }
        return false;
    }

    /**
     * @param array $data
     * @return array
     */
    public function mapData(array $data): array
    {
        $albums = [];
        if (count($data) > 0 ) {
            foreach ($data as $line)
            {
                if (count($line) === count($this->mappingData)) {
                    $album = [];
                    $i = 0;
                    foreach ($this->mappingData as $key => $value) {
                        if ($value != '') $album[$value] = $line[$key];
                        $i++;
                    }
                    $albums[] = $album;
                }
                else{
                    print 'Album non importé: ' . $line['IdAlbum'] . '<br>';
                }
            }
            return $albums;
        }
        return [];
    }
}