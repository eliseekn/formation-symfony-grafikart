<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Attribute\Route;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'contact.index', methods: ['GET', 'POST'])]
    public function index(Request $request, EntityManagerInterface $entityManager, MailerInterface $mailer): Response
    {
        $contact = new Contact();
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($contact);
            $entityManager->flush();

            $this->sendMail($contact, $mailer);
            $this->addFlash('success', 'Contact created successfully');

            return $this->redirectToRoute('contact.index');
        }

        return $this->render('contact/index.html.twig', [
            'form' => $form,
        ]);
    }

    protected function sendMail(Contact $contact, MailerInterface $mailer): void
    {
        $email = (new Email())
            ->from(new Address($contact->getEmail(), $contact->getName()))
            ->to('contact@mail.com')
            ->subject($contact->getSubject())
            ->text($contact->getMessage());

        try {
            $mailer->send($email);
        } catch (TransportExceptionInterface $e) {
            $this->addFlash('error', $e->getMessage());
        }
    }
}
