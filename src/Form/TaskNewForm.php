<?php

namespace App\Form;

use App\Entity\Classroom;
use App\Entity\Task;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class TaskNewForm extends AbstractType
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name',TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => "Please enter the task's name"
                    ])
                ]
            ])

            // normalement entityType mais besoin de relation many à doctrine...
            ->add('idUser', ChoiceType::class, [
                    'choices' => $this->userRepository->findByClassroom($options['classroomId']),
                    'choice_value' => 'id',
                    'choice_label' => function ($user) {
                        return $user->getLastName();
                    },
            ])

            ->add('date', DateType::class, [
                'widget' => 'choice',
                'format' => 'dd-MM-yyyy',
                'html5' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => "Please enter the task's name"
                    ]),
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Task::class,
        ]);
    }
}
