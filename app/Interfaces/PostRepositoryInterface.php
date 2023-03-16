<?php

namespace App\Interfaces;

interface PostRepositoryInterface
{
    public function getAll( array $filters );

    public function create( array $data );

    public function update( int $id, array $data );
}
