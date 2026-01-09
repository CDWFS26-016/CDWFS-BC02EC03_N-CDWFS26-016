<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Entity\Event;
use App\Entity\Review;
use App\Repository\UserRepository;
use App\Repository\EventRepository;
use App\Repository\ReviewRepository;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class DashboardController extends AbstractDashboardController
{
    public function __construct(
        private UserRepository $userRepository,
        private EventRepository $eventRepository,
        private ReviewRepository $reviewRepository,
    ) {
    }

    #[Route('/admin', name: 'admin')]
    #[IsGranted('ROLE_ADMIN')]
    public function index(): Response
    {
        $users = $this->userRepository->findAll();
        $events = $this->eventRepository->findAll();
        $reviews = $this->reviewRepository->findAll();
        $unvalidatedReviews = array_filter($reviews, fn($r) => !$r->isValidated());

        return $this->render('admin/dashboard.html.twig', [
            'users' => $users,
            'events' => $events,
            'reviews' => $reviews,
            'unvalidatedReviewsCount' => count($unvalidatedReviews),
        ]);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('🏰 Relais et Châteaux - Administration')
            ->setFaviconPath('data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 128 128%22><text y=%221.2em%22 font-size=%2296%22>🏰</text></svg>')
            ->generateRelativeUrls();
    }

    public function configureMenuItems(): iterable
    {
        return [
            MenuItem::linkToDashboard('Tableau de bord', 'fa fa-home'),
            MenuItem::section('Gestion'),
            MenuItem::linkToCrud('Utilisateurs', 'fa fa-users', User::class),
            MenuItem::linkToCrud('Événements', 'fa fa-calendar', Event::class),
            MenuItem::linkToCrud('Avis', 'fa fa-star', Review::class),
        ];
    }
}
