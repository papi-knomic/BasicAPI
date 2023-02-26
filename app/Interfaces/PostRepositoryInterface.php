<?php

namespace App\Interfaces;

interface PostRepositoryInterface
{
    public function getAll( array $filters, string $sort );

    public function create( array $data );

    public function update( int $id, array $data );
}
