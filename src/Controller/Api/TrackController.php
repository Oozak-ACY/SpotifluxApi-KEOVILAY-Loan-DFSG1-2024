<?php

namespace App\Controller\Api;

use App\Entity\Track;
use OpenApi\Attributes as OA;
use App\Repository\TrackRepository;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[OA\Tag(name: "Track")]
class TrackController extends AbstractController
{
    public function __construct(
        private TrackRepository $trackRepository,
        private EntityManagerInterface $em,
        private SerializerInterface $serializer,

    ) {
        //...
    }

    #[Route('/api/tracks', name: 'app_api_track', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Successful response',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Track::class, groups: ['read']))
        )
    )]
    public function index(PaginatorInterface $paginator, Request $request): JsonResponse
    {
        $tracks = $this->trackRepository->findAll();

        $data = $paginator->paginate(
            $tracks,
            $request->query->get('page', 1),
            25
        );

        return $this->json([
            'data' => $data,
        ], 200, [], [
            'groups' => ['read']
        ]);
    }

    #[Route('/api/track/{id}', name: 'app_api_track_get', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Successful response',
        content: new Model(type: Track::class, groups: ['read'])
    )]
    public function get(?Track $track = null): JsonResponse
    {
        if (!$track) {
            return $this->json([
                'error' => 'Ressource does not exist',
            ], 404);
        }

        return $this->json($track, 200, [], [
            'groups' => ['read']
        ]);
    }

    #[Route('/api/tracks', name: 'app_api_track_add',  methods: ['POST'])]
    #[OA\Response(
        response: 200,
        description: 'Successful response',
        content: new Model(type: Track::class, groups: ['read'])
    )]

    public function add(#[MapRequestPayload('json', ['groups' => ['create']])] Track $track): JsonResponse
    {
        $this->em->persist($track);
        $this->em->flush();

        return $this->json($track, 200, [], [
            'groups' => ['read']
        ]);
    }



    #[Route('/api/track/{id}', name: 'app_api_track_delete',  methods: ['DELETE'])]
    public function delete(?Track $track): JsonResponse
    {
        if (!$track) {
            return $this->json([
                'error' => 'Ressource does not exist',
            ], 404);
        }

        $this->em->remove($track);
        $this->em->flush();

        return $this->json(['message' => 'Track deleted'], 200);
    }




    #[Route('/api/track/{id}', name: 'app_api_tracks_update',  methods: ['PUT'])]
    #[OA\Response(
        response: 200,
        description: 'Successful response',
        content: new Model(type: Track::class, groups: ['read'])
    )]
    #[OA\Put(
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                ref: new Model(
                    type: Track::class,
                    groups: ['update']
                )
            )
        )
    )]
    public function update(Track $track, Request $request): JsonResponse
    {

        $data = $request->getContent();
        $this->serializer->deserialize($data, Track::class, 'json', [
            AbstractNormalizer::OBJECT_TO_POPULATE => $track,
            'groups' => ['update']
        ]);

        $this->em->flush();

        return $this->json($track, 200, [], [
            'groups' => ['read'],
        ]);
    }
}
