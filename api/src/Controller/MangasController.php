<?php

namespace App\Controller;

use App\Entity\Mangas;
use App\Form\MangasType;
use App\Controller\RestController;
use App\Repository\MangasRepository;
use FOS\RestBundle\Request\ParamFetcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/mangas')]
class MangasController extends RestController
{
    #[Route('/', name: 'mangas_index', methods: ['GET'])]
    public function index(MangasRepository $mangasRepository, ParamFetcher $paramFetcher): Response
    {
        return $this->createGetResponse($mangasRepository->findAll(), $mangasRepository->getClassName());
    }

    #[Route('/new', name: 'mangas_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $manga = new Mangas();
        $form = $this->createForm(MangasType::class, $manga);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($manga);
            $entityManager->flush();

            return $this->redirectToRoute('mangas_index');
        }

        return $this->render('mangas/new.html.twig', [
            'manga' => $manga,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'mangas_show', methods: ['GET'])]
    public function show(Mangas $manga): Response
    {
        return $this->render('mangas/show.html.twig', [
            'manga' => $manga,
        ]);
    }

    #[Route('/{id}/edit', name: 'mangas_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Mangas $manga): Response
    {
        $form = $this->createForm(MangasType::class, $manga);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('mangas_index');
        }

        return $this->render('mangas/edit.html.twig', [
            'manga' => $manga,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'mangas_delete', methods: ['DELETE'])]
    public function delete(Request $request, Mangas $manga): Response
    {
        if ($this->isCsrfTokenValid('delete'.$manga->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($manga);
            $entityManager->flush();
        }

        return $this->redirectToRoute('mangas_index');
    }
}
