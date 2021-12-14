<?php

namespace App\Controller;

use App\Entity\Livre;
use App\Form\LivreType;
use App\Repository\LivreRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/livre")
 */
class LivreController extends AbstractController
{
    /**
     * @Route("/", name="livre_index", methods={"GET"})
     */
    public function index(LivreRepository $livreRepository): Response
    {
        return $this->render('livre/index.html.twig', [
            'livres' => $livreRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="livre_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $livre = new Livre();
        $form = $this->createForm(LivreType::class, $livre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($livre);
            $entityManager->flush();

            return $this->redirectToRoute('livre_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('livre/new.html.twig', [
            'livre' => $livre,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="livre_show", methods={"GET"})
     */
    public function show(Livre $livre): Response
    {
        return $this->render('livre/show.html.twig', [
            'livre' => $livre,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="livre_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Livre $livre, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(LivreType::class, $livre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('livre_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('livre/edit.html.twig', [
            'livre' => $livre,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="livre_delete", methods={"POST"})
     */
    public function delete(Request $request, Livre $livre, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $livre->getId(), $request->request->get('_token'))) {
            $entityManager->remove($livre);
            $entityManager->flush();
        }

        return $this->redirectToRoute('livre_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/filtrer/byDatesNote", name="livre_filtrer_dates_note", methods={"GET", "POST"})
     */
    public function filterByDatesNote(Request $request, LivreRepository $livreRepository)
    {
        $formDatesNote = $this->createFormBuilder(null)
            ->setAction($this->generateUrl('livre_filtrer_dates_note'))
            ->add('date_debut', DateType::class, [
                'attr' => [
                    'class' => 'datepicker'
                ],
                'widget' => 'single_text',
                'html5' => false
            ])
            ->add('date_fin', DateType::class, [
                'attr' => [
                    'class' => 'datepicker'
                ],
                'widget' => 'single_text',
                'html5' => false
            ])
            ->add('note_min', IntegerType::class, [
                'required' => false
            ])
            ->add('note_max', IntegerType::class, [
                'required' => false
            ])
            ->getForm();

        $formDatesNote->handleRequest($request);

        if ($formDatesNote->isSubmitted() && $formDatesNote->isValid()) {
            $date_debut = $request->request->get('form')['date_debut'];
            $date_fin = $request->request->get('form')['date_fin'];
            $note_min = $request->request->get('form')['note_min'];
            $note_max = $request->request->get('form')['note_max'];

            $livres = $livreRepository->findByDatesNote($date_debut, $date_fin, $note_min, $note_max);

            return $this->render('livre/index.html.twig', [
                "livres" => $livres
            ]);
        }
        return $this->render('livre/_filtrerByDateNote.html.twig', [
            'form' => $formDatesNote->createView(),
        ]);
    }

    /**
     * @Route("/filtrer/byTitre", name="livre_filtrer_titre", methods={"GET", "POST"})
     */
    public function filterByTitre(Request $request, LivreRepository $livreRepository)
    {
        $formTitre = $this->createFormBuilder(null)
            ->setAction($this->generateUrl('livre_filtrer_titre'))
            ->add('titre', TextType::class)
            ->getForm();

        $formTitre->handleRequest($request);

        if ($formTitre->isSubmitted() && $formTitre->isValid()) {
            $titre = $request->request->get('form')['titre'];

            $livres = $livreRepository->findByLikeTitre($titre);

            return $this->render('livre/index.html.twig', [
                "livres" => $livres
            ]);
        }
        return $this->render('livre/_filtrerByTitre.html.twig', [
            'form' => $formTitre->createView(),
        ]);
    }

    /**
     * @Route("/filtrer/byParite", name="livre_filtrer_parite", methods={"GET", "POST"})
     */
    public function filterByParite(Request $request, LivreRepository $livreRepository)
    {
        $formTitre = $this->createFormBuilder(null)
            ->setAction($this->generateUrl('livre_filtrer_parite'))
            ->add('parite', ChoiceType::class, [
                'choices' => [
                    'Oui' => true
                ],
                'label' => 'Parité Homme/Femme',
                'required' => true
            ])
            ->getForm();

        $formTitre->handleRequest($request);

        if ($formTitre->isSubmitted() && $formTitre->isValid()) {

            $livres = $livreRepository->findByParite();

            return $this->render('livre/index.html.twig', [
                "livres" => $livres
            ]);
        }
        return $this->render('livre/_filtrerByParite.html.twig', [
            'form' => $formTitre->createView(),
        ]);
    }

    /**
     * @Route("/filtrer/byNationalite", name="livre_filtrer_nationalite", methods={"GET", "POST"})
     */
    public function filterByDifferentNationalite(Request $request, LivreRepository $livreRepository)
    {
        $formTitre = $this->createFormBuilder(null)
            ->setAction($this->generateUrl('livre_filtrer_nationalite'))
            ->add('nationalite', ChoiceType::class, [
                'choices' => [
                    'Oui' => true
                ],
                'label' => 'Différent nationalité',
                'required' => true
            ])
            ->getForm();

        $formTitre->handleRequest($request);

        if ($formTitre->isSubmitted() && $formTitre->isValid()) {

            $livres = $livreRepository->findByDifferentNationalite();

            return $this->render('livre/index.html.twig', [
                "livres" => $livres
            ]);
        }
        return $this->render('livre/_filtrerByDifferentNationalite.html.twig', [
            'form' => $formTitre->createView(),
        ]);
    }

    /**
     * @Route("/{id}/note/augmenter", name="livre_note_augmenter", methods={"GET", "POST"})
     */
    public function augmenteNote(Request $request, Livre $livre, EntityManagerInterface $entityManager)
    {

        $livre = $entityManager->getRepository(Livre::class)->find($livre);
        if ($livre) {
            if ($livre->getNote() < 20) {
                $livre->setNote($livre->getNote() + 1);
                $entityManager->flush();
            }
            return $this->redirectToRoute('livre_show', [
                'id' => $livre->getId()
            ]);
        }
    }

    /**
     * @Route("/{id}/note/diminuer", name="livre_note_diminuer", methods={"GET", "POST"})
     */
    public function diminuerNote(Request $request, Livre $livre, EntityManagerInterface $entityManager)
    {

        $livre = $entityManager->getRepository(Livre::class)->find($livre);
        if ($livre) {
            if ($livre->getNote() > 0) {
                $livre->setNote($livre->getNote() - 1);
                $entityManager->flush();
            }
            return $this->redirectToRoute('livre_show', [
                'id' => $livre->getId()
            ]);
        }
    }
}
