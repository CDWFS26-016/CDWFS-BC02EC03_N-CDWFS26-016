<?php

namespace App\Controller;

use App\Entity\Event;
use App\Form\EventFormType;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/event')]
class EventController extends AbstractController
{
    #[Route('', name: 'event_list')]
    public function index(EventRepository $eventRepository): Response
    {
        $events = $eventRepository->findAll();

        return $this->render('event/index.html.twig', [
            'events' => $events,
        ]);
    }

    #[Route('/create', name: 'event_create')]
    #[IsGranted('ROLE_RESPONSABLE')]
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $event = new Event();
        $event->setResponsable($this->getUser());

        $form = $this->createForm(EventFormType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($event);
            $entityManager->flush();

            $this->addFlash('success', 'Événement créé avec succès');
            return $this->redirectToRoute('event_show', ['id' => $event->getId()]);
        }

        return $this->render('event/create.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'event_edit', requirements: ['id' => '\d+'])]
    public function edit(Event $event, Request $request, EntityManagerInterface $entityManager): Response
    {
        // Vérifier l'autorisation
        if ($event->getResponsable() !== $this->getUser() && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('Vous n\'avez pas le droit de modifier cet événement');
        }

        $form = $this->createForm(EventFormType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Événement modifié avec succès');
            return $this->redirectToRoute('event_show', ['id' => $event->getId()]);
        }

        return $this->render('event/edit.html.twig', [
            'form' => $form,
            'event' => $event,
        ]);
    }

    #[Route('/{id}/delete', name: 'event_delete', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function delete(Event $event, EntityManagerInterface $entityManager): Response
    {
        // Vérifier l'autorisation
        if ($event->getResponsable() !== $this->getUser() && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('Vous n\'avez pas le droit de supprimer cet événement');
        }

        $entityManager->remove($event);
        $entityManager->flush();

        $this->addFlash('success', 'Événement supprimé avec succès');
        return $this->redirectToRoute('event_list');
    }

    #[Route('/{id}', name: 'event_show', requirements: ['id' => '\d+'])]
    public function show(int $id, EventRepository $eventRepository): Response
    {
        $event = $eventRepository->find($id);

        if (!$event) {
            throw $this->createNotFoundException('Événement non trouvé');
        }

        // Récupérer les avis validés uniquement (sauf si c'est le responsable ou un admin)
        $reviews = $event->getReviews();
        if (!$this->isGranted('ROLE_ADMIN') && (!$this->getUser() || $event->getResponsable() !== $this->getUser())) {
            $reviews = $reviews->filter(fn($review) => $review->isValidated());
        }

        return $this->render('event/show.html.twig', [
            'event' => $event,
            'reviews' => $reviews,
        ]);
    }
}
