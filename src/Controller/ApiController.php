<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\PromotionRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api")
 */
class ApiController extends AbstractController
{
    private $objectManager;
    public function __construct(EntityManagerInterface $objectManager, RequestStack $request){
        $apiToken = $request->getCurrentRequest()->headers->get('api_token');
        $user = $objectManager->getRepository(User::class)->findOneBy([
            'api_token' => $apiToken,
        ]);

        if(!$user instanceof User){
            throw new HttpException(401, 'Unauthorized');
        }
    }

    public function index(): Response
    {
        return $this->render('api/index.html.twig', [
            'controller_name' => 'ApiController',
        ]);
    }

    /**
     * @Route("/promotion", name="promotion_findAll", methods={"GET"})
     * @param PromotionRepository $promotionRepository
     * @return Response
     */

    public function findAllPromotion(PromotionRepository $promotionRepository): Response
    {
        $promotions = $promotionRepository->findAll();
        $arrayCollection = array();
        foreach($promotions as $promotion) {
            $arrayCollection = array(
                'id'                => $promotion->getId(),
                'start'   => $promotion->getStart(),
                'exit' => $promotion->getExit()
            );
        }
        $response = new JsonResponse($arrayCollection);
        $response->headers->set('Content-Type', 'application/json');
        $response->headers->set('Access-Control-Allow-Origin', '*');
        return $response;

    }
}
