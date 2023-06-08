<?php

namespace App\Form;

use App\Entity\Reponse;
use phpDocumentor\Reflection\Types\Integer;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReponseType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('id_question', IntegerType::class, [
                'data' => $options['data']->getIdQuestion(),
                'required' => true, // ou false selon vos besoins
            ])
            ->add('reponse')
            ->add('reponse_expected')
            ->add('save', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reponse::class,
        ]);
    }
}
