<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Form\ProduitType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

use Symfony\Component\HttpFoundation\File\UploadedFile;

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

            /** @var UploadedFile $imageFile */
            $imageFile = $form->get('image')->getData();

            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($imageFile) {
                $newFilename = uniqid() . '.' . $imageFile->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $imageFile->move(
                        $this->getParameter('upload_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                    $this->addFlash('danger', "Impossible d'uploader le fichier");
                    return $this->redirectToRoute('app_produit');
                }

                // updates the 'imageFilename' property to store the PDF file name
                // instead of its contents
                $produit->setImage($newFilename);
            }

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
