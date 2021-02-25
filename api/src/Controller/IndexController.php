<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class IndexController extends AbstractController
{
    #[Route('/', name: 'api_status', methods: ['GET'])]
    public function index(): JsonResponse
    {
        return new JsonResponse([
            'ok' => true,
        ]);
    }
}
