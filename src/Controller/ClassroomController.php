<?php

    namespace App\Controller;

    use App\Entity\Classroom;
    use App\Entity\Task;
    use App\Entity\UserClassrom;
    use App\Entity\User;
    use App\Repository\ClassroomRepository;
    use App\Repository\TaskRepository;
    use App\Repository\UserRepository;
    use Doctrine\ORM\EntityManager;
    use Doctrine\ORM\EntityManagerInterface;
    use phpDocumentor\Reflection\Types\Boolean;
    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
    use Symfony\Bundle\SecurityBundle\Security;
    use Symfony\Component\Finder\Exception\AccessDeniedException;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\HttpKernel\Exception\HttpException;
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
            $this->user = $security->getUser();
            $this->em = $em;
        }

        ## Méthodes & routes

        #[Route('/classroom', name: 'classroom_index')]
        public function index(): Response
        {
            $classrooms = $this->em->getRepository(Classroom::class)->findByUser($this->user->getId());

            return $this->render('classroom/index.html.twig', [
                'classrooms' => $classrooms,
            ]);
        }

        #[Route('/classroom/{id}', name: 'classroom_show')]
        public function show(int $id): Response
        {
            $this->checkUserIsInClassroom($id);

            $tasks =  $this->em->getRepository(Task::class)->findBy(
                ['id_class' => $id,
                'completed' => false
                ]
            ,
                ['date' => 'DESC']
            );
            // ajoute le nom de la personne ayant la tâche
            foreach ($tasks as &$task) {
                $user = $this->em->getRepository(User::class)->find($task->getIdUser());

                $task["nameUser"] = [
                    "firstName" => $user->getFirstName(),
                    "lastName" => $user->getLastName(),
                ];
            }

            dd($tasks);

            $classroom = $this->em->getRepository(Classroom::class)->find($id);

            $teacher = $this->em->getRepository(User::class)->find($classroom->getIdTeacher());

            $studentsInClass = $this->em->getRepository(User::class)->findByClassroom($id);
            // permet d'enlever LE prof, afin d'avoir les élèves
            $studentsInClass = array_filter($studentsInClass, fn($obj) => $obj !== $teacher);
            $studentsInClass = array_values($studentsInClass); // réindexe


            return $this->render('classroom/show.html.twig', [
                'tasks' => $tasks,
                'students' => $studentsInClass,
                'classroom' => $classroom,
                'teacher' => $teacher,
                'isTeacher' => $this->user->getId() === $teacher->getId(),
            ]);

        }

        #[Route('/classroom/{idClassroom}/{taskId}/check', name: 'classroom_task_check')]
        public function check(int $idClassroom, int $taskId):Response
        {
            $this->checkUserIsInClassroom($idClassroom);

            # check que task existe et qu'elle dans cette classe
            if ($this->em->getRepository(Task::class)->findBy([
                'id_class' => $idClassroom,
                'id' => $taskId,
                'completed' => false,
                    ]))
            {
                $this->em->getRepository(Task::class)->find($taskId)->setCompleted(true);
                $this->em->flush();

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
        public function add(int $idClassroom, int $taskId):Response
        {
            $this->checkUserIsInClassroom($idClassroom);

            return new Response('add');
        }


        ## à remplacer par Security/Vendor
        private function checkUserIsInClassroom(int $id): void
        {
            if ($this->em->getRepository(UserClassrom::class)->findBy([
                'idClassroom' => $id,
                'idUser' => $this->user->getId()
            ]) == null)
            {
                throw new NotFoundHttpException('Not found or access denied');
            }
        }
    }
