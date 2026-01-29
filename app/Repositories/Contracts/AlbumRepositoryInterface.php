<?php

namespace App\Repositories\Contracts;

use App\Models\Album;
use Illuminate\Support\Collection;

interface AlbumRepositoryInterface
{
    public function all(): Collection;
    public function find(int $id): ?Album;
    public function create(array $data): Album;
    public function update(Album $album, array $data): bool;
    public function delete(Album $album): bool;
    public function findBySlug(string $slug): ?Album;
    public function getForUser(int $userId): Collection;
}
