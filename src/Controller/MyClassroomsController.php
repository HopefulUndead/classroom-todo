<?php

namespace App\Controller;

use App\Repository\ClassroomRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER', statusCode: 404)]
final class MyClassroomsController extends AbstractController
{
    #[Route('/myclassrooms', name: 'app_my_classrooms')]
    public function index(Security $security, ClassroomRepository $ClassroomRepository): Response
    {
        $user = $security->getUser();
        $classrooms = $ClassroomRepository->findByUser($user->getId());

        #dd($classrooms);
        return $this->render('my_classrooms/index.html.twig', [
            'classrooms' => $classrooms,
        ]);
    }
}
