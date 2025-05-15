<?php

namespace App\Form;

use App\Entity\Classroom;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;


class ClassroomNewForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options ): void
    {
        $current = $options['current_user'];

        $builder
            ->add('name', TextType::class,  [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a name for the classroom',
                    ]),
                    new Length([
                        'maxMessage' => 'Too long !',   
                        'max' => 20,
                    ]),
                ],
            ])

            ->add('usersInClassroom', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'last_name',
                'multiple' => true,
                'required' => true,
                'expanded' => true,
                'placeholder' => 'Choose the students',
                #https://symfony.com/doc/current/reference/forms/types/entity.html#using-a-custom-query-for-the-entities
                // permet de ne pas montrer prof dans choix des élèves, en soit pas grave car on l'ajoute ensuite mais esthétiuement c pas fou
                'query_builder' => static function (UserRepository $r) use ($current) {
                    return $r->createQueryBuilder('u')
                        ->where('u.id != :id')
                        ->setParameter('id', $current?->getId() ?? 0);
                }
            ])
        ;
    }

    #https://symfony.com/doc/current/form/create_custom_field_type.html#adding-configuration-options-for-the-form-type
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class'   => Classroom::class,
            'current_user' => null,
        ]);
        $resolver->setAllowedTypes('current_user', [User::class, 'null']);
    }
}
