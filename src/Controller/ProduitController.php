<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Form\ProduitType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/produit')]
class ProduitController extends AbstractController
{
    #[Route('/', name: 'app_produit')]
    public function index(EntityManagerInterface $em, Request $r): Response
    {
        $produit = new Produit();
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($r);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($produit);
            $em->flush();

            $this->addFlash('success', 'Produit ajouté');
            return $this->redirectToRoute('app_produit');
        }

        // récupération de la table Produit
        $produits = $em->getRepository(Produit::class)->findAll();

        return $this->render('produit/index.html.twig', [
            'produits' => $produits,
            'ajout_produit' => $form
        ]);
    }

    #[Route('/{id}', name: 'app_produit_show')]
    public function show(Produit $produit, Request $r, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($r);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($produit);
            $em->flush();

            $this->addFlash('success', 'Produit modifié');
            return $this->redirectToRoute('app_produit');
        }

        return $this->render('produit/show.html.twig', [
            'produit' => $produit,
            'form_edit' => $form
        ]);
    }

    #[Route('/{id}/delete', name: 'app_produit_delete')]
    public function delete(Produit $produit, EntityManagerInterface $em): Response
    {
        $em->remove($produit);
        $em->flush();

        $this->addFlash('danger', 'Produit supprimé');
        return $this->redirectToRoute('app_produit');
    }
}
