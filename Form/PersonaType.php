<?php declare(strict_types=1);
/******************************************************************************/
/*                                  CASBUNDLE                                 */
/*     Auteur: Tristan Fleury - https://github.com/viduc - viduc@mail.fr      */
/*                              Licence: Apache-2.0                           */
/******************************************************************************/

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

define('REQUIRED', 'required');
define('CHOICES', 'choices');
define('EXPANDED', 'expanded');
define('DATA', 'data');
define('REQUIS', [REQUIRED => true]);
define('PAS_REQUIS', [REQUIRED => false]);

class PersonaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $tabValeur = [1 => '1', 2 => '2', 3 => "3", 4 => '4', 5 => '5'];
        $builder
            ->add('username', null, REQUIS)
            ->add('prenom', TextType::class, REQUIS)
            ->add('nom', TextType::class, REQUIS)
            ->add('age', IntegerType::class, REQUIS)
            ->add('lieu', TextType::class, REQUIS)
            ->add('aisanceNumerique', ChoiceType::class, [
                CHOICES  => $tabValeur,
                EXPANDED => true,
                'empty_data' => '1'
            ])
            ->add('expertiseDomaine', ChoiceType::class, [
                CHOICES  => $tabValeur,
                EXPANDED => true
            ])
            ->add('frequenceUsage', ChoiceType::class, [
                CHOICES  => $tabValeur,
                EXPANDED => true
            ])
            ->add('metier', TextType::class, REQUIS)
            ->add('citation', TextType::class, REQUIS)
            ->add(
                'buts',
                TextType::class,
                [REQUIRED => false, 'attr' => array('readonly' => true)]
            )
            ->add(
                'personnalite',
                TextType::class,
                [REQUIRED => false, 'attr' => array('readonly' => true)]
            )
            ->add('histoire', TextareaType::class, [
                'attr' => ['class' => 'tinymce'],
                REQUIRED => true
            ])
            ->add('photo', FileType::class, [
                'label' => 'Photo',
                'mapped' => false,
                REQUIRED => false,
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
            ->add('urlPhoto', HiddenType::class,[REQUIRED => false])
            ->add('roles', ChoiceType::class, [
                'choices' => $options['rolesListe'],
                'expanded'  => false, // liste déroulante
                'multiple'  => true, // choix multiple
            ])
            ->add('save', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
           'data_class' => Persona::class,
           'rolesListe' => null
       ]);
    }

    public function getBlockPrefix()
    {
        return 'cas_bundle_persona_type';
    }
}
