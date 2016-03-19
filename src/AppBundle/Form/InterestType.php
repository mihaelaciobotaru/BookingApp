<?php

namespace AppBundle\Form;

use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use AppBundle\Entity\Interest;

class InterestType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('type', ChoiceType::Class, array(
                  'choices' => array('Select type' => '', 'Accommodation' => Interest::TYPE_ACCOMMODATION, 'Event' => Interest::TYPE_EVENT),
                  'label_attr' => array('class' => 'col-sm-2 control-label'),
                  'attr' => array('class' => 'form-control', 'label' => 'Offer type')))
            ->add('name', TextType::Class, array('attr' => array('class' => 'form-control', 'label' => 'Title'),
                                                 'label_attr' => array('class' => 'col-sm-2 control-label'),))
            ->add('address', TextType::Class, array('attr' => array('class' => 'form-control', 'label' => 'Address'),
                                                    'label_attr' => array('class' => 'col-sm-2 control-label'),))
            ->add('capacity', IntegerType::Class, array('attr' => array('class' => 'form-control', 'label' => 'Maximum num of tenants'),
                                                        'label_attr' => array('class' => 'col-sm-2 control-label'),))
            ->add('price', NumberType::Class, array('attr' => array('class' => 'form-control', 'label' => 'Day price'),
                                                    'label_attr' => array('class' => 'col-sm-2 control-label'),))
            ->add('description', TextAreaType::Class, array('attr' => array('class' => 'form-control', 'label' => 'Description'),
                                                                            'label_attr' => array('class' => 'col-sm-2 control-label'),))
            ->add('images', HiddenType::Class, array('attr' => array('label' => 'Images'),
                                                     'label_attr' => array('class' => 'col-sm-2 control-label'),))
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Interest'
        ));
    }
}
