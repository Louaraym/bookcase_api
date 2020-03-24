<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    /**
     * @Route(
     *     path="api/user/{id}/borrows/count",
     *     name="user_borrows_count",
     *     methods="GET",
     *     defaults={
     *            "_controller"="App\Controller\UserController::userBorrowsNumber",
     *            "_api_resource_class"="App\Entity\User",
     *            "api_item_operation_name"="getBorrowsNumber"
     *          }
     *     )
     * @param User $data
     * @return JsonResponse
     */
    public function userBorrowsNumber(User $data): JsonResponse
    {
        $count = $data->getBorrows()->count();
        return $this->json([
            'id' =>$data->getId(),
            'borrows_number' =>$count
        ]);
    }
}
