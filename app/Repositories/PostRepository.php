<?php

namespace App\Repositories;

use App\Interfaces\PostRepositoryInterface;
use App\Models\Job;
use App\Models\Post;

class PostRepository implements PostRepositoryInterface
{

    public function getAll()
    {
        return Post::paginate(10);
    }

    public function getUserPosts()
    {
        // TODO: Implement getUserPosts() method.
    }

    public function getPost(int $id)
    {
        // TODO: Implement getPost() method.
    }

    public function create(array $data)
    {
        return Post::create($data);
    }

    public function update(int $id, array $data)
    {
        $job = Post::find($id);
        $job->update($data);

        return $job;
    }

    public function delete(int $id)
    {
        // TODO: Implement delete() method.
    }

    public function filterPosts(array $filters)
    {
        // TODO: Implement filterPosts() method.
    }
}
