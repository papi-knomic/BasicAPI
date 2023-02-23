<?php

namespace App\Repositories;

use App\Interfaces\PostRepositoryInterface;
use App\Models\Post;

class PostRepository implements PostRepositoryInterface
{

    public function getAll()
    {
        $sort = request('sortBy', 'latest');
        if ( !in_array( $sort, ['latest', 'popular'] ) ){
            $sort = 'latest';
        }
        return Post::filter(request(['tag', 'search'], $sort))->paginate(10);
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

}
