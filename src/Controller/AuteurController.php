<?php

namespace App\Controller;

use App\Entity\Auteur;
use App\Form\AuteurType;
use App\Repository\AuteurRepository;
use App\Repository\GenreRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/auteur")
 */
class AuteurController extends AbstractController
{
    /**
     * @Route("/", name="auteur_index", methods={"GET"})
     */
    public function index(AuteurRepository $auteurRepository): Response
    {
        return $this->render('auteur/index.html.twig', [
            'auteurs' => $auteurRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="auteur_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $auteur = new Auteur();
        $form = $this->createForm(AuteurType::class, $auteur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($auteur);
            $entityManager->flush();

            return $this->redirectToRoute('auteur_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('auteur/new.html.twig', [
            'auteur' => $auteur,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="auteur_show", methods={"GET"})
     */
    public function show(Auteur $auteur, GenreRepository $genreRepository): Response
    {
        $genres = $genreRepository->createQueryBuilder('g')
        ->select('DISTINCT g')
        ->innerJoin('g.livres', 'livre')
        ->innerJoin('livre.auteurs', 'a')
        ->andWhere('a.id = :id')
        ->setParameter('id', $auteur->getId())
        ->getQuery()
        ->getResult();

        return $this->render('auteur/show.html.twig', [
            'auteur' => $auteur,
            'genres' => $genres,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="auteur_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Auteur $auteur, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(AuteurType::class, $auteur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('auteur_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('auteur/edit.html.twig', [
            'auteur' => $auteur,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="auteur_delete", methods={"POST"})
     */
    public function delete(Request $request, Auteur $auteur, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$auteur->getId(), $request->request->get('_token'))) {
            $entityManager->remove($auteur);
            $entityManager->flush();
        }

        return $this->redirectToRoute('auteur_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/filtrer/by3Livres", name="auteur_filtrer_3_livres", methods={"GET", "POST"})
     */
    public function filterBy3Livres(Request $request, AuteurRepository $auteurRepository)
    {
        $formLivre = $this->createFormBuilder(null)
            ->setAction($this->generateUrl('auteur_filtrer_3_livres'))
            ->add('livres', ChoiceType::class, [
                'choices' => [
                    'Oui' => true
                ],
                'label' => 'Auteurs ayant écrit dans plus de 3 livres',
                'required' => true
            ])
            ->getForm();

        $formLivre->handleRequest($request);

        if ($formLivre->isSubmitted() && $formLivre->isValid()) {

            $auteurs = $auteurRepository->findBy3Livres();

            return $this->render('auteur/index.html.twig', [
                "auteurs" => $auteurs
            ]);
        }
        return $this->render('auteur/_filtrerBy3Livres.html.twig', [
            'form' => $formLivre->createView(),
        ]);
    }
}
