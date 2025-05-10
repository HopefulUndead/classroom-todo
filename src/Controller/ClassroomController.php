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
        #[Route('/classroom', name: 'classroom_index')]
        public function index(Security $security, ClassroomRepository $ClassroomRepository): Response
        {
            $user = $security->getUser();
            $classrooms = $ClassroomRepository->findByUser($user->getId());

            #dd($classrooms);
            return $this->render('classroom/index.html.twig', [
                'classrooms' => $classrooms,
            ]);
        }

            #[Route('/classroom/{id}', name: 'classroom_show')]
        public function show(int $id, Security $security, TaskRepository $taskRepository, ClassroomRepository $classroomRepository, UserRepository $userRepository): Response
        {
            $user = $security->getUser();

            if ( $classroomRepository->userIsInClass(
                $user->getId(),
                $id)
            === false)
            throw $this->createNotFoundException("You are not allowed to access this page : you arent in this classroom");

            $tasks =  $taskRepository->findByClassroom($id);
            $classroom = $classroomRepository->findById($id);
            $teacher = $userRepository->findById($classroom->getIdTeacher());
            $studentsInClass = $userRepository->findByClassroom($id); // all users
            // permet d'enlever LE prof, afin d'avoir les élèves
            $studentsInClass = array_filter($studentsInClass, fn($obj) => $obj !== $teacher);
            $studentsInClass = array_values($studentsInClass); // réindexe
            $isTeacher = $user->getId() === $teacher->getId();

            return $this->render('classroom/show.html.twig', [
                'tasks' => $tasks,
                'students' => $studentsInClass,
                'classroom' => $classroom,
                'teacher' => $teacher,
                'isTeacher' => $isTeacher,
            ]);

        }
    }
