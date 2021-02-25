<?php

namespace App\Controller;

use App\Entity\Scan;
use App\Form\ScanType;
use App\Controller\RestController;
use App\Repository\ScanRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

#[Route('/scan')]
class ScanController extends RestController
{
    #[Route('/', name: 'scan_index', methods: ['GET'])]
    public function index(ScanRepository $scanRepository): JsonResponse
    {
        return $this->createGetResponse($scanRepository->findAll(), $scanRepository->getClassName());
    }

    #[Route('/new', name: 'scan_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $scan = new Scan();
        $form = $this->createForm(ScanType::class, $scan);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($scan);
            $entityManager->flush();

            return $this->redirectToRoute('scan_index');
        }

        return $this->render('scan/new.html.twig', [
            'scan' => $scan,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'scan_show', methods: ['GET'])]
    public function show(Scan $scan): Response
    {
        return $this->render('scan/show.html.twig', [
            'scan' => $scan,
        ]);
    }

    #[Route('/{id}/edit', name: 'scan_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Scan $scan): Response
    {
        $form = $this->createForm(ScanType::class, $scan);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('scan_index');
        }

        return $this->render('scan/edit.html.twig', [
            'scan' => $scan,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'scan_delete', methods: ['DELETE'])]
    public function delete(Request $request, Scan $scan): Response
    {
        if ($this->isCsrfTokenValid('delete'.$scan->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($scan);
            $entityManager->flush();
        }

        return $this->redirectToRoute('scan_index');
    }
}
