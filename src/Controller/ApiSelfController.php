<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class ApiSelfController extends AbstractController
{
    #[Route('/api/self', name: 'app_api_self')]
    public function index(): JsonResponse
    {
        $user = $this->getUser();
        if ($user instanceof UserInterface === false) {
            throw $this->createAccessDeniedException();
        }

        return $this->json([
            'user' => $user->getUserIdentifier(),
        ]);
    }
}
