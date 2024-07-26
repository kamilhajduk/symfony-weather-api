<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\WeatherApi;

class IndexController extends AbstractController
{
    #[Route('/average_temp', methods: ['POST'])]
    public function index(Request $request, WeatherApi $api): JsonResponse
    {
        $data = $request->toArray();

        if (!isset($data['date']) || !isset($data['cities'])) {
            return new JsonResponse(
                ['error' => 'Missing required fields: date and cities.'],
                JsonResponse::HTTP_BAD_REQUEST
            );
        }

        try {
            $averageTemp = $api->getAverageTempBulk($data['date'], $data['cities']);
        } catch (\Exception $e) {
            return new JsonResponse(
                ['error' => $e->getMessage()],
                JsonResponse::HTTP_BAD_REQUEST
            );
        }

        return new JsonResponse(['average_temp' => $averageTemp]);
    }
}
