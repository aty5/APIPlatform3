<?php

namespace App\Controller;

use App\Repository\PostRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class PostCountController
{
    public function __construct(private PostRepository $postRepository)
    {
    }

    public function __invoke(Request $request): int
    {
        $onlineQuery = $request->get('online');
        $conditions = [];

        if ($onlineQuery !== null) {
            $conditions = ['online' => $onlineQuery == '1' ? true : false];
        }
        // TODO: Implement __invoke() method.
        return $this->postRepository->count($conditions);
    }
}