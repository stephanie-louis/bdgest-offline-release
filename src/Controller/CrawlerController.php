<?php

namespace App\Controller;

use App\Application\Importer;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Security;

class CrawlerController extends AbstractController
{
    /**
     * @Route("/crawler", name="crawler_memo")
     */
    public function index(Security $security)
    {
        if ($security->getUser() === null) $this->redirectToRoute('accesDenied');

        return $this->render('crawler/memo_url.html.twig');
    }

    /**
     * @Route("/crawler/album/{id_bdgest}", name="crawler_album")
     */
    public function crawlerAlbum($id_bdgest, Importer $imp, Security $security)
    {
        if ($security->getUser() === null) $this->redirectToRoute('accesDenied');

        $em = $this->getDoctrine()->getManager();
        $result = $imp->importAlbumAndSerieFromIdAlbum($id_bdgest, $em);

        return $this->render('crawler/album.html.twig', [
            'album' => $result['myalbum'],
            'serie' => $result['serie'],
            'exists' => [ 'album' => $result['albumExists'], 'serie' => $result['seriExists'] ],
        ]);
    }

    /**
     * @Route("/crawler/album/wishlist/{id_bdgest}", name="crawler_album_wishlist")
     */
    public function crawlerAlbumWishlist($id_bdgest, Importer $imp, Security $security)
    {
        if ($security->getUser() === null) return $this->redirectToRoute('accesDenied');

//        $imp = new Importer();
        $em = $this->getDoctrine()->getManager();
        $result = $imp->importAlbumAndSerieFromIdAlbum($id_bdgest, $em, true);

        return $this->render('crawler/album.html.twig', [
            'album' => $result['myalbum'],
            'serie' => $result['serie'],
            'exists' => [ 'album' => $result['albumExists'], 'serie' => $result['seriExists'] ],
        ]);
    }


}