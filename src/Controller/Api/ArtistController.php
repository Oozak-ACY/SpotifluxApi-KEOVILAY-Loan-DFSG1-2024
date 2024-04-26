<?php

namespace App\Controller\Api;

use App\Entity\Artist;
use OpenApi\Attributes as OA;
use App\Repository\ArtistRepository;
use Nelmio\ApiDocBundle\Annotation\Model;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[OA\Tag(name: "Artist")]
class ArtistController extends AbstractController
{
    public function __construct(
        private ArtistRepository $artistRepository,
        private EntityManagerInterface $em,
        private SerializerInterface $serializer,

    ) {
        //...
    }

    #[Route('/api/artists', name: 'app_api_artist', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Successful response',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Artist::class, groups: ['read']))
        )
    )]
    public function index(PaginatorInterface $paginator, Request $request): JsonResponse
    {
        $artists = $this->artistRepository->findAll();

        $data = $paginator->paginate(
            $artists,
            $request->query->get('page', 1),
            25
        );

        return $this->json([
            'data' => $data,
        ], 200, [], [
            'groups' => ['read']
        ]);
    }

    #[Route('/api/artist/{id}', name: 'app_api_artist_get', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Successful response',
        content: new Model(type: Artist::class, groups: ['read'])
    )]
    public function get(?Artist $artist = null): JsonResponse
    {
        if (!$artist) {
            return $this->json([
                'error' => 'Ressource does not exist',
            ], 404);
        }

        return $this->json($artist, 200, [], [
            'groups' => ['read']
        ]);
    }

    #[Route('/api/artists', name: 'app_api_artist_add',  methods: ['POST'])]
    #[OA\Response(
        response: 200,
        description: 'Successful response',
        content: new Model(type: Artist::class, groups: ['read'])
    )]

    public function add(#[MapRequestPayload('json', ['groups' => ['create']])] Artist $artist): JsonResponse
    {
        $this->em->persist($artist);
        $this->em->flush();

        return $this->json($artist, 200, [], [
            'groups' => ['read']
        ]);
    }



    #[Route('/api/artist/{id}', name: 'app_api_artist_delete',  methods: ['DELETE'])]
    public function delete(?Artist $artist): JsonResponse
    {
        if (!$artist) {
            return $this->json([
                'error' => 'Ressource does not exist',
            ], 404);
        }

        $this->em->remove($artist);
        $this->em->flush();

        return $this->json(['message' => 'Artist deleted'], 200);
    }




    #[Route('/api/artist/{id}', name: 'app_api_artists_update',  methods: ['PUT'])]
    #[OA\Response(
        response: 200,
        description: 'Successful response',
        content: new Model(type: Artist::class, groups: ['read'])
    )]
    #[OA\Put(
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                ref: new Model(
                    type: Artist::class,
                    groups: ['update']
                )
            )
        )
    )]
    public function update(Artist $artist, Request $request): JsonResponse
    {

        $data = $request->getContent();
        $this->serializer->deserialize($data, Artist::class, 'json', [
            AbstractNormalizer::OBJECT_TO_POPULATE => $artist,
            'groups' => ['update']
        ]);

        $this->em->flush();

        return $this->json($artist, 200, [], [
            'groups' => ['read'],
        ]);
    }
}
