<?php

    namespace App\Controller;

    use App\Entity\Classroom;
    use App\Entity\Task;
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


    // !! dans controlleur jamais attributs utilisateurs !!
    // utilisateur n'est pas censé être connu à l'extérieur d'une route
    // sous méthode d'une route transmetttre utilisateur OK, mais pas classe d'un controlleur

    #[IsGranted('ROLE_USER', statusCode: 404)]
    final class ClassroomController extends AbstractController
    {
        ## Attributs

        ## Constructeur
        // atributs déclarés dans constructeurs et accessible dans fichier php8
    public function __construct(
        private readonly EntityManagerInterface $em)

    {

    }
        ## Méthodes & routes

        #[Route('/classroom', name: 'classroom_index')]
        public function index(): Response
        {
            $classroomsRaw = $this->getUser()->getClassrooms();

            $classrooms = [];
            foreach ($classroomsRaw as $classroom)
            {
                    $classrooms[] = [
                    'classroom' => $classroom,
                    'userIsOwner' => $classroom->getTeacherId()->getId() == $this->getUser()->getId()
                    ];
            }

            return $this->render('classroom/index.html.twig', [
                'classrooms' => $classrooms,
            ]);
        }
        #[Route('/classroom/delete/{id}', name: 'classroom_delete', methods: ['GET'])]
        public function delete(int $id, EntityManagerInterface $em): Response
        {
           $this->checkUserIsInClassroom($id);

           $classroom = $em->getRepository(Classroom::class)->find($id);
           if ($classroom->getTeacherId()->getId() != $this->getUser()->getId())
               return throw new AccessDeniedException();

            //https://symfony.com/doc/current/doctrine.html#deleting-an-object
            $em->remove($classroom);
            $em->flush();

            $this->addFlash('success', 'Classroom has been deleted !');

            return $this->redirectToRoute('classroom_index');
        }

        #[Route('/classroom/create', name: 'classroom_create')]
        public function create(Request $request, EntityManagerInterface $entityManager): Response
        {
            $classroom = new Classroom();

            $form = $this->createForm(ClassroomNewForm::class, $classroom, ['current_user' => $this->getUser()]); //! a bien spécifier le nouveau paramètre, sinon config/services.yaml
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $classroom->setTeacherId($this->getUser());

                $userInClassroom = $form->get('usersInClassroom')->getData();
                $userInClassroom->add($this->getUser());
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

            $tasks = $this->em->getRepository(Task::class)->findBy([
                'classId' => $id,
                'completed' => false ],
                ['date' => 'ASC']
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
                    '$userId' => $task->getUserId(),
                ];
                if ($task->getUserId() === 0) {
                    $entry['nameUser'] = 'Classroom';
                } else {
                    $user = $this->em->getRepository(User::class)->find($task->getUserId());
                    $entry['nameUser'] = $user->getFirstName() . ' ' . $user->getLastName();
                }
                $taskArray[] = $entry;
            }

            $classroom = $this->em->getRepository(Classroom::class)->find($id);

            $teacher = $this->em->getRepository(User::class)->find($classroom->getTeacherId());

            $studentsInClass = $classroom->getUsersInClassroom();

            $isTeacher = $this->getUser()->getId() === $teacher->getId();

            $form = null;
            if ($isTeacher)
            {
                $task = new Task();
                $task->setDate(new \DateTime()); // placeholder date du jour
                $form = $this->createForm(TaskNewForm::class, $task, ['classroom' => $classroom]);
                $form->handleRequest($request);

                if ($form->isSubmitted() && $form->isValid())
                {
                    $task->setClassId($this->em->getRepository(Classroom::class)->find($id));
                    $task->setCompleted(false);

                    $this->em->persist($task);
                    $this->em->flush();

                    $this->addFlash(
                        'success',
                        'Congrats ! You created a task !'
                    );
                    return $this->redirectToRoute('classroom_show', ['id' => $id]);
                }
            }

            $studentsInClass->removeElement($teacher); //permet d'afficher dans la vue sans le prof mais sans le flush() non plus dans me formulaire

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
        public function check(int $idClassroom, int $taskId ):Response
        {
            $this->checkUserIsInClassroom($idClassroom);

            # check que task existe et qu'elle dans cette classe
            if ($this->em->getRepository(Task::class)->findBy([
                'classId' => $idClassroom,
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
            $classroom = $this->em->getRepository(Classroom::class)->find($id);

            if (!$classroom) {
                throw new NotFoundHttpException('Classroom not found');
            }

            if (!$classroom->getUsersInClassroom()->contains($this->getUser())) {
                throw new NotFoundHttpException('Access denied');
            }
        }
    }