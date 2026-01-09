<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\Review;
use App\Form\ReviewFormType;
use App\Repository\ReviewRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/review')]
class ReviewController extends AbstractController
{
    #[Route('/create/{eventId}', name: 'review_create', requirements: ['eventId' => '\d+'])]
    #[IsGranted('ROLE_USER')]
    public function create(
        int $eventId,
        Request $request,
        EntityManagerInterface $entityManager,
        \App\Repository\EventRepository $eventRepository
    ): Response {
        $event = $eventRepository->find($eventId);
        if (!$event) {
            throw $this->createNotFoundException('Événement non trouvé');
        }

        $review = new Review();
        $review->setEvent($event);
        $review->setAuthor($this->getUser());

        $form = $this->createForm(ReviewFormType::class, $review);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Les avis sont non validés par défaut
            $review->setIsValidated(false);

            $entityManager->persist($review);
            $entityManager->flush();

            $this->addFlash('success', 'Votre avis a été publié. Il sera visible après validation par le responsable de l\'événement.');
            return $this->redirectToRoute('event_show', ['id' => $eventId]);
        }

        return $this->render('review/create.html.twig', [
            'form' => $form,
            'event' => $event,
        ]);
    }

    #[Route('/{id}/edit', name: 'review_edit', requirements: ['id' => '\d+'])]
    public function edit(
        Review $review,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        $event = $review->getEvent();

        // Vérifier l'autorisation
        $isOwner = $review->getAuthor() === $this->getUser();
        $isResponsible = $event->getResponsable() === $this->getUser();
        $isAdmin = $this->isGranted('ROLE_ADMIN');

        // Propriétaire peut éditer son avis
        // Responsable peut modérer les avis de son événement
        // Admin peut modérer tous les avis
        if ($isOwner) {
            // L'auteur ne peut que modifier son avis, pas le valider
            $isModerating = false;
        } elseif ($isResponsible || $isAdmin) {
            // Responsable/Admin peuvent modérer
            $isModerating = true;
        } else {
            throw $this->createAccessDeniedException('Vous n\'avez pas le droit de modifier cet avis');
        }

        $form = $this->createForm(ReviewFormType::class, $review, [
            'is_moderation' => $isModerating,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Si c'est une modération et le checkbox est coché, valider l'avis
            if ($isModerating && $form->has('validated') && $form->get('validated')->getData()) {
                $review->setIsValidated(true);
            }

            $entityManager->flush();

            $message = $isOwner ? 'Votre avis a été modifié' : 'L\'avis a été modéré avec succès';

            $this->addFlash('success', $message);
            return $this->redirectToRoute('event_show', ['id' => $event->getId()]);
        }

        return $this->render('review/edit.html.twig', [
            'form' => $form,
            'review' => $review,
            'event' => $event,
            'isResponsible' => $isModerating,
        ]);
    }

    #[Route('/{id}/delete', name: 'review_delete', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function delete(Review $review, EntityManagerInterface $entityManager): Response
    {
        $event = $review->getEvent();

        // Vérifier l'autorisation
        $isResponsible = $event->getResponsable() === $this->getUser();
        $isAdmin = $this->isGranted('ROLE_ADMIN');

        if (!($isResponsible || $isAdmin)) {
            throw $this->createAccessDeniedException('Vous n\'avez pas le droit de supprimer cet avis');
        }

        $entityManager->remove($review);
        $entityManager->flush();

        $this->addFlash('success', 'L\'avis a été supprimé');
        return $this->redirectToRoute('event_show', ['id' => $event->getId()]);
    }
}
