<?php

    namespace App\Controller;

    use App\Entity\Classroom;
    use App\Entity\Task;
    use App\Entity\UserClassrom;
    use App\Entity\User;
    use App\Form\ClassroomNewForm;
    use App\Form\TaskNewForm;
    use App\Repository\ClassroomRepository;
    use App\Repository\TaskRepository;
    use App\Repository\UserRepository;
    use Doctrine\Common\Collections\ArrayCollection;
    use Doctrine\ORM\EntityManager;
    use Doctrine\ORM\EntityManagerInterface;
    use phpDocumentor\Reflection\Types\Boolean;
    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
    use Symfony\Bundle\SecurityBundle\Security;
    use Symfony\Component\Finder\Exception\AccessDeniedException;
    use Symfony\Component\HttpFoundation\Request;
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

        ## Constructeur
        public function __construct(Security $security)
        {
            $this->user = $security->getUser();
        }

        ## Méthodes & routes

        #[Route('/classroom', name: 'classroom_index')]
        public function index(): Response
        {
            $classrooms = $this->user->getClassrooms();

            return $this->render('classroom/index.html.twig', [
                'classrooms' => $classrooms,
            ]);
        }

        #[Route('/classroom/create', name: 'classroom_create')]
        public function create(Request $request, EntityManagerInterface $entityManager): Response
        {
            $classroom = new Classroom();

            $form = $this->createForm(ClassroomNewForm::class, $classroom, ['current_user' => $this->user]); //! a bien spécifier le nouveau paramètre, sinon config/services.yaml
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $classroom->setTeacherId($this->user);

                $userInClassroom = $form->get('usersInClassroom')->getData();
                $userInClassroom->add($this->user);
                $classroom->setUsersInClassroom($userInClassroom);

                $entityManager->persist($classroom);
                $entityManager->flush();

                $this->addFlash(
                    'success',
                    'Congrats ! You create a classroom !'
                );
                return $this->redirectToRoute('classroom_index');
            }
            else if ($form->isSubmitted() && !$form->isValid()) {
                $this->addFlash(
                    'error',
                    'An error occurred while creating classroom'
                );
                return $this->redirectToRoute('classroom_index');
            }
            return $this->render('classroom/create.html.twig', [
                'classroomForm' => $form->createView(),
            ]);
        }

        #[Route('/classroom/{id}', name: 'classroom_show')]
        public function show(int $id, Request $request): Response
        {
            $this->checkUserIsInClassroom($id);

            $tasks =  $this->em->getRepository(Task::class)->findBy(
                ['id_class' => $id,
                'completed' => false
                ]
            ,
                ['date' => 'DESC']
            );
            // créer un tableau local du tableau d'entité
            $taskArray = [];
            // ajoute le nom de la personne ayant la tâche

            foreach ($tasks as $task) {
                $entry = [
                    'id' => $task->getId(),
                    'name' => $task->getName(),
                    'date' => $task->getDate(),
                    'completed' => $task->isCompleted(),
                    'idUser' => $task->getIdUser(),
                ];

                if ($task->getIdUser() === 0) {
                    $entry['nameUser'] = 'Classroom';
                } else {
                    $user = $this->em->getRepository(User::class)->find($task->getIdUser());
                    $entry['nameUser'] = $user->getFirstName() . ' ' . $user->getLastName();
                }

                $taskArray[] = $entry;
            }

            $classroom = $this->em->getRepository(Classroom::class)->find($id);

            $teacher = $this->em->getRepository(User::class)->find($classroom->getIdTeacher());

            $studentsInClass = $this->em->getRepository(User::class)->findByClassroom($id);
            // permet d'enlever LE prof, afin d'avoir les élèves
            $studentsInClass = array_filter($studentsInClass, fn($obj) => $obj !== $teacher);
            $studentsInClass = array_values($studentsInClass); // réindexe
            $isTeacher = $this->user->getId() === $teacher->getId();

            $form = null;
            if ($isTeacher)
            {
                $task = new Task();
                $form = $this->createForm(TaskNewForm::class, $task);
                $form->handleRequest($request);

                if ($form->isSubmitted() && $form->isValid()) {
                    $this->addFlash(
                        'success',
                        'Congrats ! You finished a task !'
                    );

                    $task->setIdClass($id);
                    $task->setCompleted(false);

                    $this->em->persist($task);
                    $this->em->flush();

                    return $this->redirectToRoute('classroom_show');
                }
            }

            return $this->render('classroom/show.html.twig', [
                'tasks' => $taskArray,
                'students' => $studentsInClass,
                'classroom' => $classroom,
                'teacher' => $teacher,
                'isTeacher' => $isTeacher,
                'tasknewform' => $form,
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
                    'success', # bootstrap alert styling
                    'Congrats ! You finished a task !'
                    );
            }
            else $this->addFlash(
                'danger', # bootstrap alert styling
                'An error occurred'
                );

            return $this->redirectToRoute('classroom_show', ['id' => $idClassroom]);
        }

        ## à remplacer par Security>Vendor
        private function checkUserIsInClassroom(int $id): void
        {
            # https://www.doctrine-project.org/projects/doctrine-collections/en/1.6/index.html
            $this->user->getClassrooms()->contains($this->user);

            if ($this->em->getRepository(UserClassrom::class)->findBy([
                'idClassroom' => $id,
                'idUser' => $this->user->getId()
            ]) == null)
            {
                throw new NotFoundHttpException('Not found or access denied');
            }
        }
    }
