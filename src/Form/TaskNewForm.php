<?php

namespace App\Form;

use App\Entity\Classroom;
use App\Entity\Task;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
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

    private EntityManagerInterface $em;
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
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

            ->add('userId', EntityType::class, [
                    'class' => User::class,
                    'choices' => $options['classroom']->getUsersInClassroom(),
                    'choice_value' => 'id',
                    'expanded' => true,
                    'choice_label' => function ($user) {
                        return $user->getLastName();
                    },
            ])

            ->add('date', DateType::class, [
                'widget' => 'choice',
                'format' => 'dd-MM-yyyy',
                'attr' => ['placeholder' => 'jj/mm/aaaa'],
                'html5' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => "Please enter the task's name"
                    ]),
                ]
            ])
        ;
    }

    //https://symfony.com/doc/current/forms.html#passing-options-to-forms
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Task::class,
            'classroom' => null,
        ]);
        $resolver->setRequired('classroom');
        $resolver->setAllowedTypes('classroom', [Classroom::class]);    }
}
