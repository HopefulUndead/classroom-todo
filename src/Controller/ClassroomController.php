<?php

    namespace App\Controller;

    use App\Repository\ClassroomRepository;
    use App\Repository\TaskRepository;
    use App\Repository\UserRepository;
    use phpDocumentor\Reflection\Types\Boolean;
    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
    use Symfony\Bundle\SecurityBundle\Security;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\Routing\Attribute\Route;
    use Symfony\Component\Security\Http\Attribute\IsGranted;
    use function Symfony\Component\String\b;

    #[IsGranted('ROLE_USER', statusCode: 404)]
    final class ClassroomController extends AbstractController
    {
        #[Route('/classroom/{id}', name: 'app_classroom')]
        public function index(int $id, Security $security, TaskRepository $taskRepository, ClassroomRepository $classroomRepository, UserRepository $userRepository): Response
        {
            $user = $security->getUser();

            //
            if ( $classroomRepository->userIsInClass(
                $user->getId(),
                $id)
            === false)
            throw $this->createAccessDeniedException("You are not allowed to access this page : you arent in this classroom");

            $tasks =  $taskRepository->findByClassroom($id);
            #dd($tasks);

            $usersInClass = $userRepository->findByClassroom($id);
            $classroom = $classroomRepository->findById($id);
            $teacher = $userRepository->findById($classroom->getIdTeacher());

            $isTeacher = $user->getId() === $teacher->getId();

            return $this->render('classroom/index.html.twig', [
                'tasks' => $tasks,
                'users' => $usersInClass,
                'classroom' => $classroom,
                'teacher' => $teacher,
                'isTeacher' => $isTeacher,
            ]);

        }
    }
