<?php

namespace Viduc\CasBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Viduc\CasBundle\Entity\Persona;

class PersonaType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $tabValeur = [1 => '1', 2 => '2', 3 => "3", 4 => '4', 5 => '5'];
        $photos = ['test1' => 'test1', 'test2' => 'test2', 'test3' => "test3", 'test4' => 'test4', 'test5' => 'test5'];
        $builder
            ->add('username', null, ['required' =>true])
            ->add('prenom', TextType::class, [
                'required' => true
            ])
            ->add('nom', TextType::class, [
                'required' => true
            ])
            ->add('age', IntegerType::class, [
                'required' => true
            ])
            ->add('lieu', TextType::class, [
                'required' => true
            ])
            ->add('aisanceNumerique',ChoiceType::class, [
                'choices'  => $tabValeur,
                'expanded' => true,
                'data' => '1',
            ])
            ->add('expertiseDomaine',ChoiceType::class, [
                'choices'  => $tabValeur,
                'expanded' => true,
                'data' => '1',
            ])
            ->add('frequenceUsage',ChoiceType::class, [
                'choices'  => $tabValeur,
                'expanded' => true,
                'data' => '1',
            ])
            ->add('metier', TextType::class, [
                'required' => true
            ])
            ->add('citation', TextType::class, [
                'required' => true
            ])
            ->add('histoire', TextareaType::class, [
                'attr' => ['class' => 'tinymce'],
                'required' => true
            ])
            ->add('photo', FileType::class, [
                'label' => 'Photo',
                'mapped' => false,
                'required' => false,
                'attr' => ['class' => ''],
                'constraints' => [
                    new File([
                         'maxSize' => '1024k',
                         'mimeTypes' => [
                             'image/jpeg',
                             'image/png',
                         ],
                         'mimeTypesMessage' => 'Le format image doit être jpeg ou png',
                         'maxSizeMessage' => 'Le fichier ne doit pas dépasser 1024 KB',
                     ])
                ],
            ])
            ->add('urlPhoto', HiddenType::class,['required' => false])
            ->add('save', SubmitType::class)
            /*->add('roles')*/;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
           'data_class' => Persona::class,
       ]);
    }

    public function getBlockPrefix()
    {
        return 'cas_bundle_persona_type';
    }
}
