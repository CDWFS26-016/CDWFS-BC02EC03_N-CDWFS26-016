<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
class AdminController extends AbstractController
{
    // Ce contrôleur est volontairement vide
    // Toute la gestion admin se fait via EasyAdmin (src/Controller/Admin/)
}


