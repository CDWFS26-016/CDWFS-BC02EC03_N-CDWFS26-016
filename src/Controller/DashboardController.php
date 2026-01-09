<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class DashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'dashboard')]
    #[IsGranted('ROLE_USER')]
    public function index(): Response
    {
        $user = $this->getUser();

        if ($this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('admin');
        }

        // Récupérer les avis de l'utilisateur
        $myReviews = $user->getReviews();

        // Récupérer les événements dont l'utilisateur est responsable
        $myEvents = $user->getEvents();

        return $this->render('dashboard/index.html.twig', [
            'myReviews' => $myReviews,
            'myEvents' => $myEvents,
            'user' => $user,
        ]);
    }
}
