<?php

namespace App\Controller;

use App\Entity\Conference;
use App\Repository\CommentRepository;
use App\Repository\ConferenceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Annotation\Route;

class ConferenceController extends AbstractController
{
    public function __construct(
        private ConferenceRepository $conferenceRepository,
    ) {}

    #[Route('/', name: 'homepage')]
    public function index(): Response
    {
        return $this->render('conference/index.html.twig', [
            'conferences' => $this->conferenceRepository->findAll(),
        ]);
    }

    #[Route('/conference/{id}', name: 'conference')]
    public function show(
        Conference $conference,
        #[MapQueryParameter(options: ['min_range' => 0])]
        int $offset = 0,
    ): Response
    {
        $paginator = $this->container->get(CommentRepository::class)->getCommentPaginator($conference, $offset);

        return $this->render('conference/show.html.twig', [
//            'conferences' => $this->conferenceRepository->findAll(),
            'conference' => $conference,
            'comments' => $paginator,
            'previous' => $offset - CommentRepository::PAGINATOR_PER_PAGE,
            'next' => min(count($paginator), $offset + CommentRepository::PAGINATOR_PER_PAGE),
        ]);
    }

    public static function getSubscribedServices(): array
    {
        $services = parent::getSubscribedServices();
        $services[] = CommentRepository::class;

        return $services;
    }
}
