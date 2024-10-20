<?php

namespace App\Controller;

use App\Entity\Post;

class PostPublishController
{

    public function __invoke(Post $data): Post
    {
        $data->setOnline(true);

        // TODO: Implement __invoke() method.

        return $data;
    }
}