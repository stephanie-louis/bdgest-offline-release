<?php

namespace App\Controller;


use App\Application\ImportCSV;
use App\Application\Importer;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Security;

class ImportCSVController extends AbstractController
{
    /**
     * @Route("/importCSV", name="import_csv")
     */
    public function index(Security $security)
    {
        if ($security->getUser() === null) return $this->redirectToRoute('accesDenied');

        $em = $this->getDoctrine()->getManager();
        $csv = new ImportCSV('ma-collection.csv');

        $content = $csv->serializeCsv();
        if ($content !== false)
        {
            // TODO : n'est pas de la responsabilité de ImportCSV
            $albums = $csv->mapData($content);
        }
        foreach ($albums as $album) {
            $imp = new Importer();
            $result[] = $imp->importAlbumAndSerieFromAlbumObject($album, $em);
        }
        return $this->render('import.html.twig', ['content' => $result]);

    }

}