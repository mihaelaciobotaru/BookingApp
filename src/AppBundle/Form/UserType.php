<?php
/**
 * Created by PhpStorm.
 * User: mihaela
 * Date: 06/03/16
 * Time: 02:34
 */

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use AppBundle\Entity\User;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', TextType::class, array('attr' => array('class' => 'form-control', 'placeHolder' => 'Username')))
            ->add('email', EmailType::class, array('attr' => array('class'=>'form-control', 'placeHolder'=>'Email')))
            ->add('plainPassword', RepeatedType::class, array(
                    'type' => PasswordType::class,
                    'first_options'  => array('attr' => array('class' => 'form-control', 'placeHolder' => 'Password')),
                    'second_options' => array('attr' => array('class' => 'form-control', 'placeHolder' => 'Retype Password')),
                ))
            ->add('type', ChoiceType::class, array(
                'choices' => array('Select type of user' => '', 'Landlord' => User::TYPE_LANDLORD, 'Tenant' => User::TYPE_TENANT),
                'attr' => array('class' => 'form-control'),
                ))
            ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\User',
        ));
    }
}