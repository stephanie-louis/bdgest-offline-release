<?php

namespace App\Controller;


use App\Entity\Album;
use App\Entity\Serie;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class BaseController extends AbstractController
{

    /**
     * @Route("/", name="accesDenied")
     */
    public function index(Security $security)
    {
        if ($security->getUser() !== null ) return $this->redirectToRoute('dashboard');

        return new Response('hello');
    }
    /**
     * @Route("/dashboard", name="dashboard")
     */
    public function dashboard(Security $security)
    {
        if ($security->getUser() === null) return $this->redirectToRoute('accesDenied');

        $em = $this->getDoctrine()->getManager();
        $albums = $em->getRepository(Album::class)
            ->findBy(
            [
                'whislist' => 0,
            ],
            [
                'dateachat' => 'DESC',
                'serie' => 'ASC',
                'tome' => 'ASC'
            ], 30);

        return $this->render('dashboard.html.twig', ['albums' => $albums]);
    }

    /**
     * @Route("/{collection}", name="albums", requirements={"collection"="albums|whishlist"})
     * @Route("/{collection}/{letter}", name="albums_by_letter", requirements={"collection"="albums|whishlist"})
     */
    public function collection(Security $security, $collection, ?string $letter = '')
    {
        if ($security->getUser() === null) return $this->redirectToRoute('accesDenied');

        $wishlist = 0;
        $link = $collection;
        $titre = 'Collection';
        if ($collection === 'whishlist'){
            $wishlist = 1;
            $titre = 'Whishlist';
        }
        $albums = $this->albums($wishlist);
        $total = count($albums);
        $letters = $this->getLettersFromTitles($albums, 'getSerie');
        if ($letter === null || ! in_array($letter, $letters))
            $letter = $letters[0];
        $albums = $this->albumsByLetter($wishlist, $letter);

        return $this->render('albums.html.twig',
            [
                'total' => $total,
                'titre' => $titre,
                'albums' => $albums,
                'link' => $link,
                'letters' => $letters,
                'letter' => $letter,
            ]
        );
    }

    /**
     * @Route("/series", name="series")
     * @Route("/series/{letter}", name="series_by_letter")
     */
    public function seriesList(Security $security, ?string $letter = '')
    {
        if ($security->getUser() === null) return $this->redirectToRoute('accesDenied');

        $series = $this->series();
        $total = count($series);
        $letters = $this->getLettersFromTitles($series, 'getTitre');
        if ($letter === null || ! in_array($letter, $letters))
            $letter = $letters[0];
        $series = $this->seriesByLetter($letter);

        return $this->render('series.html.twig',
            [
                'total' => $total,
                'series' => $series,
                'letters' => $letters,
                'letter' => $letter,
            ]
        );
    }

    /**
     * @Route("/toggle/collection/{id}", name="toCollection", requirements={"id"="\d+"})
     */
    public function moveFromWishlistToCollection(Security $security, $id)
    {
        if ($security->getUser() === null) return $this->redirectToRoute('accesDenied');

        $em = $this->getDoctrine()->getManager();
        $album = $em->getRepository(Album::class)->findAlbum($id);
        if ($album !== null) {
            if ($album->getWhislist()) {
                $album->setWhislist(0);
                $now = new DateTime('now');
                $dt = DateTime::createFromFormat('d/m/Y', $now->format('d/m/Y'));
                $album->setDateachat($dt);
                $em->persist($album);
                $em->flush();

                return $this->redirectToRoute('albums_by_letter',
                    [
                    'collection' => 'albums',
                    'letter' => strtolower(substr($album->getSerie()->getTitre(), 0, 1)),
                    ]
                );
            }
        }
        return $this->redirectToRoute('dashboard');
    }

    /**
     * @Route("/toggle/tosell/{id}", name="toSell", requirements={"id"="\d+"})
     */
    public function toggleToSell(Security $security, $id)
    {
        if ($security->getUser() === null) return $this->redirectToRoute('accesDenied');

        $em = $this->getDoctrine()->getManager();
        $album = $em->getRepository(Album::class)->findAlbum($id);
        if ($album !== null) {
            $album->setTosell(! $album->getTosell());
            $em->persist($album);
            $em->flush();
            if ($album->getTosell()) {
                return $this->redirectToRoute('albums_by_letter',
                    [
                        'collection' => 'albums',
                        'letter' => strtolower(substr($album->getSerie(), 0, 1)),
                    ]
                );
            }
            else {
                return $this->redirectToRoute('to-sell');
            }
        }
        return $this->redirectToRoute('dashboard');
    }

    /**
     * @Route("/delete/{id}", name="delete", requirements={"id"="\d+"})
     */
    public function deleteAlbum(Security $security, $id)
    {
        if ($security->getUser() === null) return $this->redirectToRoute('accesDenied');

        $em = $this->getDoctrine()->getManager();
        $album = $em->getRepository(Album::class)->findAlbum($id);
        if ($album !== null)
        {
            $collection = $album->getWhislist() ? 'whishlist':'albums';
            $em->remove($album);
            $em->flush();

            return $this->redirectToRoute('albums_by_letter',
                [
                    'collection' => $collection,
                    'letter' => strtolower(substr($album->getSerie(), 0, 1)),
                ]
            );
        }
        return $this->redirectToRoute('dashboard');
    }

    /**
     * @Route("/to-sell", name="to-sell")
     */
    public function tosell(Security $security)
    {
        if ($security->getUser() === null) return $this->redirectToRoute('accesDenied');

        $titre = 'Albums à vendre';
        $albums = $this->albumsToSell();
        $total = count($albums);

        return $this->render('albums_a_vendre.html.twig',
            [
                'total' => $total,
                'titre' => $titre,
                'albums' => $albums,
            ]
        );
    }

    /**
     * @Route("/album/{id}", name="album", requirements={"id"="\d+"})
     */
    public function album_detail(int $id, Security $security)
    {
        if ($security->getUser() === null) return $this->redirectToRoute('accesDenied');

        $em = $this->getDoctrine()->getManager();
        $album = $em->getRepository(Album::class)->findOneBy(['id' => $id]);

        return $this->render('album.html.twig',
            [
                'album' => $album,
            ]
        );
    }

    private function albums(int $whishlist)
    {
        $em = $this->getDoctrine()->getManager();
        return $em->getRepository(Album::class)->findBy(['whislist' => $whishlist]);
    }

    private function series()
    {
        $em = $this->getDoctrine()->getManager();
        return $em->getRepository(Serie::class)->findAll();
    }

    private function albumsToSell()
    {
        $em = $this->getDoctrine()->getManager();
        return $em->getRepository(Album::class)->findBy(['tosell' => true]);
    }

    private function albumsByLetter(int $whishlist, string $letter = '')
    {
        $em = $this->getDoctrine()->getManager();
        return $em->getRepository(Album::class)->findByLetter($whishlist, $letter);
    }

    private function seriesByLetter(string $letter = '')
    {
        $em = $this->getDoctrine()->getManager();
        return $em->getRepository(Serie::class)->findByLetter($letter);
    }

    private function getLettersFromTitles($tab, $method){
        $letters = [];
        foreach ($tab as $item){
            $letter = strtolower(substr($item->$method(), 0, 1));
            if (! in_array($letter, $letters))
                $letters[] = $letter;
        }
        sort($letters);
        return $letters;
    }
}