<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        // returns your User object, or null if the user is not authenticated
        // use inline documentation to tell your editor yo  ur exact User class
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        // throw $this->createAccessDeniedException("You are not allowed to access this page.");
        if ($user) // si auth
        {
            return $this->render('home/my_classroom.html.twig', [
                'user_name' => $this->getUser()->getFirstName(),
            ]);
        }
        else
        {
            return $this->render('home/index.html.twig', [
                'controller_name' => 'HomeController',
            ]);
        }
    }
}
