<?php

namespace App\Controller;

use App\Repository\BookRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class StatsController extends AbstractController
{
    /**
     * @Route(
     *     path="api/users/borrowsNumberPerUser",
     *     name="users_borrows_number",
     *     methods="GET"
     *     )
     * @param UserRepository $userRepository
     * @return JsonResponse
     */
    public function borrowsNumberPerUser(UserRepository $userRepository): JsonResponse
    {
        $borrowsNumberPerUser = $userRepository->borrowsNumberPerUser();
        return  $this->json($borrowsNumberPerUser);
    }

    /**
     * @Route(
     *     path="api/books/bestbooks",
     *     name="best_books",
     *     methods="GET"
     *     )
     * @param BookRepository $bookRepository
     * @return JsonResponse
     */
    public function bestBooks(BookRepository $bookRepository): JsonResponse
    {
        $bestBooks = $bookRepository->findBestBooks();
        return  $this->json($bestBooks);
    }


}
