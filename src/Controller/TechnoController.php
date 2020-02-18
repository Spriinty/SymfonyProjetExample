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
use Symfony\Component\HttpFoundation\File\Exception\FileException;


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

            $logoFile = $form->get('logo')->getData();

            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($logoFile) {
                $originalFilename = pathinfo($logoFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$logoFile->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $logoFile->move(
                        $this->getParameter('logo_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $techno->setLogo($newFilename);
            }

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

            $logoFile = $form->get('logo')->getData();

            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($logoFile) {
                $originalFilename = pathinfo($logoFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$logoFile->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $logoFile->move(
                        $this->getParameter('logo_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $techno->setLogo($newFilename);
            }
            
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