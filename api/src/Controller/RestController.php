<?php

namespace App\Controller;

use FOS\RestBundle\Request\ParamFetcher;
use JMS\Serializer\SerializerBuilder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Pagerfanta\Pagerfanta;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormError;
use Symfony\Contracts\Translation\TranslatorInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Mapping\Entity;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

abstract class RestController extends AbstractController
{
    private $serializer;

    private $translator;

    public function __construct(
        SerializerInterface $serializer
    ) {
        $this->serializer = $serializer;
    }

    protected function createGetResponse($object, string $name = 'object'): JsonResponse
    {
        $this->checkFoundObject($object, $name);

        return new JsonResponse($this->serialize($object), Response::HTTP_OK);
    }

    protected function createFilteredResponse(ServiceEntityRepository $repository, ParamFetcher $paramFetcher): JsonResponse
    {
        $queryBuilder = $repository->filtered($paramFetcher);
        $this->checkFoundObject($queryBuilder, $repository->getClassName());


        $adapter = new DoctrineORMAdapter($queryBuilder);

        $params = $paramFetcher->all();

        $page = $params['page'] ?? null;
        $perPage = $params['per_page'] ?? null;

        $response = [];
        $havePager = !$page && !$perPage ? true: false;
        if ($havePager) {
            $pagerfanta = new Pagerfanta($adapter);
            $pagerfanta
                ->setMaxPerPage($perPage)
                ->setCurrentPage($page);

            $response['_meta'] = [
                'total' => $pagerfanta->getNbResults(),
                'page'  => $page,
                'nb_pages' => $pagerfanta->getNbPages(),
            ];
        }

        $response ['results'] = $this->serialize((array) $pagerfanta->getCurrentPageResults() ?? null);

        return new JsonResponse([$response], Response::HTTP_OK);
    }

    protected function createPostResponse(Request $request, $type, array $options = [], $object = null): JsonResponse
    {
        $form = $this->createForm($type, $object, $options);
        $form->submit(json_decode($request->getContent(), true));

        if ($form->isValid()) {
            $object = $form->getData();

            $em = $this->getDoctrine()->getManager();
            $em->persist($object);
            $em->flush();

            $objectSerialized = $this->serialize($object);

            return new JsonResponse($objectSerialized, Response::HTTP_CREATED);
        }

        return new JsonResponse([
            'status' => 'error',
            'errors' => $this->convertFormToArray($form),
        ], Response::HTTP_BAD_REQUEST);
    }

    protected function createPatchResponse(Request $request, $type, array $options = [], $object = null): JsonResponse
    {
        $form = $this->createForm($type, $object, $options);
        $form->submit(json_decode($request->getContent(), true), false);

        if ($form->isSubmitted() && $form->isValid()) {
            $object = $form->getData();

            $em = $this->getDoctrine()->getManager();
            $em->persist($object);
            $em->flush();

            return $this->noContent();
        }

        return $this->formError($form);
    }

    protected function createPutResponse(Request $request, $type, array $options = [], $object = null): JsonResponse
    {
        $form = $this->createForm($type, $object, $options);
        $form->submit(json_decode($request->getContent(), true));

        if ($form->isValid()) {
            $object = $form->getData();

            $em = $this->getDoctrine()->getManager();
            $em->persist($object);
            $em->flush();

            return $this->noContent();
        }

        return $this->formError($form);
    }

    protected function createDeleteResponse(Entity $object): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($object);
        $em->flush();

        return $this->noContent();
    }

    protected function serialize($object): array
    {
        $request = $this->get('request_stack')->getCurrentRequest();

        if (preg_match('/post_.+$/', $request->get('_route'))) {
            $group = str_replace('post_', 'get_', $request->get('_route'));
        } else {
            $group = $request->get('_route');
        }

        $context = SerializationContext::create()->setGroups([ $group ]);

        return $this->serializer->toArray($object, $context);
    }

    protected function serializeAudit($object): array
    {
        $serializer = SerializerBuilder::create()->build();

        return $serializer->toArray($object);
    }

    protected function convertFormToArray(FormInterface $data): array
    {
        $form = $errors = [];

        foreach ($data->getErrors() as $error) {
            $errors[] = $this->getErrorMessage($error, $this->translator);
        }

        if ($errors) {
            $form['errors'] = $errors;
        }

        $children = [];
        foreach ($data->all() as $child) {
            if ($child instanceof FormInterface) {
                $children[$child->getName()] = $this->convertFormToArray($child);
            }
        }

        if ($children) {
            $form['children'] = $children;
        }

        return $form;
    }

    private function getErrorMessage(FormError $error, TranslatorInterface $translator)
    {
        if (null !== $error->getMessagePluralization()) {
            return $translator->transChoice(
                $error->getMessageTemplate(),
                $error->getMessagePluralization(),
                $error->getMessageParameters(),
                'validators'
            );
        }

        return $translator->trans($error->getMessageTemplate(), $error->getMessageParameters(), 'validators');
    }

    private function formError(FormInterface $form): JsonResponse
    {
        return new JsonResponse([
            'status' => 'error',
            'errors' => $this->convertFormToArray($form)
        ], Response::HTTP_BAD_REQUEST);
    }

    private function noContent(): JsonResponse
    {
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    private function checkFoundObject($object, string $name): void
    {
        if (strchr($name, "\\")) {
            $name = explode("\\", $name);
            $name = strtolower(end($name));
        }

        $object = 0 !== count($object) ? $object : "Aucun ${name} trouvé";
        if (is_string($object)) {
            throw new NotFoundHttpException($object);
        }
    }
}
