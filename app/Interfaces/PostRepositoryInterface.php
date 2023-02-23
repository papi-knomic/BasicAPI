<?php

namespace App\Interfaces;

interface PostRepositoryInterface
{
    public function getAll();

    public function getUserPosts();

    public function getPost( int $id );

    public function create( array $data );

    public function update( int $id, array $data );

    public function delete( int $id );

    public function filterPosts( array $filters );
}
