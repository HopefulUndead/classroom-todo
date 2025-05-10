<?php

namespace App\Controller;

use App\Repository\ClassroomRepository;
use App\Repository\TaskRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER', statusCode: 404)]
final class ClassroomController extends AbstractController
{
    #[Route('/classroom/{id}', name: 'app_classroom')]
    public function index(int $id, Security $security, TaskRepository $TaskRepository): Response
    {
        // !!vérifier que a le droit d'être l)
        $user = $security->getUser();
        $tasks =  $TaskRepository->findByClassroom($id);
        dd($tasks);
        return new Response('ok class');
    }
}
