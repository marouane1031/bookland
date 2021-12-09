<?php

namespace App\Controller;

use App\Entity\Auteur;
use App\Entity\Genre;
use App\Entity\Livre;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/bookland")
 */
class InitController extends AbstractController
{
    /**
     * @Route("/init", name="init")
     */
    public function index(): Response
    {
        $genresFile = file_get_contents(\dirname(__DIR__).'/resources/init_genres.json');
        $genres = json_decode($genresFile);
        $auteursFile = file_get_contents(\dirname(__DIR__).'/resources/init_auteurs.json');
        $auteurs = json_decode($auteursFile);
        $livresFile = file_get_contents(\dirname(__DIR__).'/resources/init_livres.json');
        $livres = json_decode($livresFile);

        $entityManager = $this->getDoctrine()->getManager();

        $genreRepo = $entityManager->getRepository(Genre::class);
        $auteurRepo = $entityManager->getRepository(Auteur::class);
        $livreRepo = $entityManager->getRepository(Livre::class);

        foreach($genres as $g) {
            $genre = new Genre;
            $genre->setNom($g->nom);
            if(!$genreRepo->findOneByNom($genre->getNom()))
                $entityManager->persist($genre);
            $entityManager->flush();
        }

        foreach($auteurs as $a) {
            $auteur = new Auteur;
            $auteur->setNomPrenom($a->nom_prenom);
            $auteur->setSexe($a->sexe);

            $date = str_replace('/', '-', $a->date_de_naissance);
            $auteur->setDateDeNaissance(new DateTime(date('Y-m-d', strtotime($date))));
            $auteur->setNationalite($a->nationalite);
            if(!$auteurRepo->findOneBy(["nom_prenom" => $auteur->getNomPrenom()]))
                $entityManager->persist($auteur);
            $entityManager->flush();
        }

        foreach($livres as $l) {
            $livre = new Livre;
            $livre->setIsbn($l->isbn);
            $livre->setTitre($l->titre);
            $livre->setNbpages($l->nbpages);

            $date = str_replace('/', '-', $l->date_de_parution);
            $livre->setDateDeParution(new DateTime(date('Y-m-d', strtotime($date))));
            $livre->setNote($l->note);

            $findLivre = $livreRepo->findOneByIsbn($livre->getIsbn());
            if(!$findLivre){
                $entityManager->persist($livre);
                foreach($l->genres as $livreGenre) {
                    $livre->addGenre($genreRepo->findOneByNom($livreGenre->nom));
                }
    
                foreach($l->auteurs as $livreAuteur) {
                    $livre->addAuteur($auteurRepo->findOneBy(["nom_prenom" => $livreAuteur->nom_prenom ]));
                }
            }
            $entityManager->flush();
        }

        return $this->render('init/index.html.twig', [
            'controller_name' => 'InitController',
            'genres' => $genres
        ]);
    }
}
