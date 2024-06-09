<?php

namespace App\Form;

use App\Entity\Recipe;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Event\PostSubmitEvent;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\Length;

class RecipeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('description', options: [
                'constraints' => new Length(min: 100)
            ])
            ->add('duration', options: [
                'label' => 'Duration (min)',
                'constraints' => new GreaterThan(10)
            ])
            ->add('save', SubmitType::class)
            ->addEventListener(FormEvents::POST_SUBMIT, $this->setTimestamps(...))
        ;
    }

    public function setTimestamps(PostSubmitEvent $event): void
    {
        $recipe = $event->getData();

        if (!$recipe instanceof Recipe) {
            return;
        }

        if (!$recipe->getId()) {
            $recipe->setCreatedAt(new \DateTimeImmutable());
        } else {
            $recipe->setUpdatedAt(new \DateTimeImmutable());
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Recipe::class,
        ]);
    }
}