<?php

    namespace App\Controller;

    use App\Entity\Classroom;
    use App\Entity\Task;
    use App\Entity\UserClassrom;
    use App\Repository\ClassroomRepository;
    use App\Repository\TaskRepository;
    use App\Repository\UserRepository;
    use Doctrine\ORM\EntityManager;
    use Doctrine\ORM\EntityManagerInterface;
    use http\Client\Curl\User;
    use phpDocumentor\Reflection\Types\Boolean;
    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
    use Symfony\Bundle\SecurityBundle\Security;
    use Symfony\Component\Finder\Exception\AccessDeniedException;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
    use Symfony\Component\Routing\Attribute\Route;
    use Symfony\Component\Security\Http\Attribute\IsGranted;
    use function Symfony\Component\String\b;

    #[IsGranted('ROLE_USER', statusCode: 404)]
    final class ClassroomController extends AbstractController
    {
        ## Attributs
        private User $user;
        private EntityManagerInterface $em;

        ## Constructeur
        public function __construct(Security $security, EntityManagerInterface $em)
        {
            $this->user = $security(>);
            $this->em = $em;
        }

        ## Méthodes & routes

        #[Route('/classroom', name: 'classroom_index')]
        public function index(): Response
        {
            $classrooms = $entityManager->getRepository(Classroom::class)->findByUser($user->getId());

            #dd($classrooms);
            return $this->render('classroom/index.html.twig', [
                'classrooms' => $classrooms,
            ]);
        }

            #[Route('/classroom/{id}', name: 'classroom_show')]
        public function show(int $id, Security $security, EntityManagerInterface $entityManager,TaskRepository $taskRepository, ClassroomRepository $classroomRepository, UserRepository $userRepository): Response
        {
            if( $this->userIsNotInClassroom($id, $security, $classroomRepository)) throw $this->createNotFoundException("You are not allowed to access this page : you arent in this classroom");

            #https://symfony.com/doc/current/doctrine.html#fetching-objects-from-the-database
            $tasks =  $entityManager->getRepository(Task::class)->findBy(
                ['id_class' => $id,
                'completed' => false
                ]);


            $classroom = $classroomRepository->findById($id);
            $teacher = $userRepository->findById($classroom->getIdTeacher());
            $studentsInClass = $userRepository->findByClassroom($id); // all users
            // permet d'enlever LE prof, afin d'avoir les élèves
            $studentsInClass = array_filter($studentsInClass, fn($obj) => $obj !== $teacher);
            $studentsInClass = array_values($studentsInClass); // réindexe
            $user = $security->getUser();
            $isTeacher = $user->getId() === $teacher->getId();

            return $this->render('classroom/show.html.twig', [
                'tasks' => $tasks,
                'students' => $studentsInClass,
                'classroom' => $classroom,
                'teacher' => $teacher,
                'isTeacher' => $isTeacher,
            ]);

        }

        #[Route('/classroom/{idClassroom}/{taskId}/check', name: 'classroom_task_check')]
        public function check(int $idClassroom, int $taskId,  Security $security, EntityManagerInterface $entityManager, ClassroomRepository $classroomRepository):Response
        {
            # check que l'utilisateur est bien dans la classe
            if ($this->userIsNotInClassroom($idClassroom,$security, $classroomRepository)) throw new AccessDeniedException('You are not allowed to access this page.');

            # check que task existe et qu'elle dans cette classe (2eme verif superflue ?)
            if ($entityManager->getRepository(Task::class)->findBy([
                'id_class' => $idClassroom,
                'id' => $taskId,
                'completed' => false,]))
            {
                $entityManager->getRepository(Task::class)->find($taskId)->setCompleted(true);
                $entityManager->flush();

                # flash message, equivalent alert() en js
                $this->addFlash(
                    'success ', # bootstrap alert styling
                    'Congrats ! You finished a task !'
                );
            }
            else $this->addFlash(
                'danger', # bootstrap alert styling
                'An error occurred'
            );

            return $this->redirectToRoute('classroom_show', ['id' => $idClassroom]);
        }

        #[Route('/classroom/{idClassroom}/add', name: 'classroom_task_add')]
        public function add(int $idClassroom, int $taskId,  Security $security, EntityManagerInterface $entityManager, ClassroomRepository $classroomRepository):Response
        {
            # check que l'utilisateur est bien dans la classe
            $this->checkUserIsInClassroom();

            return new Response('add');
        }

        private function checkUserIsInClassroom(int $id, Security $security, EntityManagerInterface $entityManager)  {

            if ($entityManager->getRepository(UserClassrom::class)->findBy([
                'idClassroom' => $id,
                'idUser' => $security->getUser()->getId()
            ]))
            {
                throw new NotFoundHttpException('Not found or access denied');
            }
        }
    }
