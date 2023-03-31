<?php



namespace App\Form;



use App\Entity\Formation;

use Symfony\Component\Form\AbstractType;

use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type as SFType;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;



class FormationType extends AbstractType

{

    public function buildForm(FormBuilderInterface $builder, array $options): void

    {

        $builder

            ->add('produit', EntityType::class, array('class'=>'App\Entity\Produit','choice_label'

            =>'libelle'))

            ->add('dateDebut')

            ->add('nbreHeures')

            ->add('departement')

            ->add('sommaire')//exercice 1

            ->add('Ajouter',SFType\SubmitType::class)

        ;

    }
}