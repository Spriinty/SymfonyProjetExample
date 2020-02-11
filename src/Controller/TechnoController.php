<?php

namespace App\Controller;

use App\Entity\Techno;
use App\Form\ProjetType;
use App\Form\TechnoType;
use App\Repository\TechnoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


    /**
     * @Route("/admin/techno")
     */
class TechnoController extends AbstractController
{
    /**
     * @Route("/", name="techno")
     */
    public function index(TechnoRepository $technoRepository)
    {
        $technos = $technoRepository->findAll();

        return $this->render('techno/index.html.twig', [
            'technos' => $technos,
        ]);
    }

        /**
     * @Route("/add", name="techno_add")
     */
    public function add(Request $request, TechnoRepository $technoRepository, EntityManagerInterface $entityManager)
    {
        $techno = new Techno();
        $form = $this->createForm(TechnoType::class, $techno);
        $form->handleRequest($request);

        // Si c'est valide et soumis :
        if ($form->isSubmitted() && $form->isValid()) {

             //On insère les données :
            $entityManager->persist($techno);

            //Commit de la base de données
            $entityManager->flush();

            return $this->redirectToRoute('techno');
        }

        return $this->render('techno/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

            /**
     * @Route("/edit/{id}", name="techno_edit", requirements={"id"="\d+"})
     */
    public function edit(Techno $techno, Request $request, TechnoRepository $technoRepository, EntityManagerInterface $entityManager)
    {
        $form = $this->createForm(TechnoType::class, $techno);
        $form->handleRequest($request);

        // Véification :
            // Si c'est valide et soumis :
        if ($form->isSubmitted() && $form->isValid()) {

            //Commit de la base de données
            $entityManager->flush();
            //On retourne sur la route projet
           return $this->redirectToRoute('techno');
       }

        return $this->render('techno/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

               /**
     * @Route("/delete/{id}", name="techno_delete", requirements={"id"="\d+"})
     */
    public function delete(Techno $techno, TechnoRepository $technoRepository, EntityManagerInterface $entityManager)
    {
        $entityManager->remove($techno);
        $entityManager->flush();
        return $this->redirectToRoute('techno');
    }
}