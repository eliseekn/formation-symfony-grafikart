<?php

namespace App\Form;

use App\Entity\Contact;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Event\PostSubmitEvent;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', options: [
                'constraints' => new Length(min: 5)
            ])
            ->add('email', options: [
                'constraints' => new Length(min: 5)
            ])
            ->add('subject', options: [
                'constraints' => new Length(min: 10)
            ])
            ->add('message', options: [
                'constraints' => new Length(min: 100)
            ])
            ->add('save', SubmitType::class)
            ->addEventListener(FormEvents::POST_SUBMIT, $this->setTimestamps(...))
        ;
    }

    public function setTimestamps(PostSubmitEvent $event): void
    {
        $data = $event->getData();

        if ($data instanceof Contact) {
            $data->setCreatedAt(new \DateTimeImmutable());
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Contact::class,
        ]);
    }
}
