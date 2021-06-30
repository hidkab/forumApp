<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Subject;
use App\Form\SubjectType;
use App\Repository\SubjectRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

 /**
  * @IsGranted("IS_AUTHENTICATED_FULLY")
  */

class ForumController extends AbstractController
{
    #[Route('/forum', name: 'forum')]
    #[Route('/', name: 'index')]
    public function index(): Response
    {
        $subjectrepository = $this->getDoctrine()->getRepository(Subject::class);
        $subjects = $subjectrepository->findAll();
    
        return $this->render('forum/index.html.twig', [
            'subjects' => $subjects,
        ]);
    }


    #[Route('/forum/subject/{id}', name: 'single', requirements:["id" => "\d+"])]
    public function single(int $id=1, SubjectRepository $subjectrepository): Response
    {
        $subject = $subjectrepository->find($id);
    
        return $this->render('forum/single.html.twig', [
            "subject" => $subject
        ]);
    }


    #[Route('/forum/rules', name: 'rules')]
    public function rules(): Response
    {
        return $this->render('forum/rules.html.twig', [
        ]);
    }

    #[Route('/forum/subject/new', name: 'newSubject')]
    public function newSubject(Request $request): Response
    {
        $subject = new Subject;
        $form = $this->createForm(SubjectType::class, $subject);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $subject->setPublished(new \DateTime());

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($subject);
            $entityManager->flush();

            return $this->redirectToRoute('index');
            
        }

        return $this->render('forum/newSubject.html.twig', [
            "form" => $form->createView()
        ]);
    }
}
