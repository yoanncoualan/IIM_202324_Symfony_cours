<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Form\CategorieType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CategorieController extends AbstractController
{
    #[Route('/categorie', name: 'app_categorie')]
    public function index(EntityManagerInterface $em, Request $request): Response
    {
        // Création d'un objet vide pour le formulaire
        $categorie = new Categorie();
        // Création du formulaire en utilisant l'objet vide
        $form = $this->createForm(CategorieType::class, $categorie);
        // Analyse la requete HTTP
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Si le formulaire a été soumis et qu'il a passé les vérifications, on le sauvegarde en base

            $em->persist($categorie); // Prepare en PDO
            $em->flush(); // Execute

            $this->addFlash('success', 'Catégorie ajoutée');

            // Redirige vers la liste des catégories pour qu'il recharge la liste des catégories
            return $this->redirectToRoute('app_categorie');
        }

        // Récupération de la table categorie
        $categories = $em->getRepository(Categorie::class)->findAll();

        return $this->render('categorie/index.html.twig', [
            'categories' => $categories,
            'form_ajout' => $form
        ]);
    }

    #[Route('/categorie/{id}', name: 'app_categorie_show')]
    public function show(Categorie $categorie, Request $request, EntityManagerInterface $em): Response
    {
        // $categorie va directement récupérer en base une catégorie basée sur l'id reçu en paramètre

        $form = $this->createForm(CategorieType::class, $categorie);
        // Analyse la requete HTTP
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Si le formulaire a été soumis et qu'il a passé les vérifications, on le sauvegarde en base

            $em->persist($categorie); // Prepare en PDO
            $em->flush(); // Execute

            $this->addFlash('success', 'Catégorie modifiée');

            return $this->redirectToRoute('app_categorie_show', [
                'id' => $categorie->getId()
            ]);
        }

        return $this->render('categorie/show.html.twig', [
            'categorie' => $categorie,
            'form_edit' => $form
        ]);
    }

    #[Route('/categorie/{id}/delete', name: 'app_categorie_delete')]
    public function delete(Categorie $categorie, EntityManagerInterface $em)
    {
        // Suppression de l'objet trouvé en paramètre
        $em->remove($categorie);
        $em->flush();

        $this->addFlash('danger', 'Catégorie supprimée');

        return $this->redirectToRoute('app_categorie');
    }
}
