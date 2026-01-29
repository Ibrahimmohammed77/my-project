<?php

namespace App\Repositories;

use App\Models\Album;
use App\Repositories\Contracts\AlbumRepositoryInterface;
use Illuminate\Support\Collection;

class AlbumRepository implements AlbumRepositoryInterface
{
    protected $model;

    public function __construct(Album $model)
    {
        $this->model = $model;
    }

    public function all(): Collection
    {
        return $this->model->all();
    }

    public function find(int $id): ?Album
    {
        return $this->model->find($id);
    }

    public function create(array $data): Album
    {
        return $this->model->create($data);
    }

    public function update(Album $album, array $data): bool
    {
        return $album->update($data);
    }

    public function delete(Album $album): bool
    {
        return $album->delete();
    }

    public function findBySlug(string $slug): ?Album
    {
        return $this->model->where('slug', $slug)->first();
    }

    public function getForUser(int $userId): Collection
    {
        return $this->model->where('user_id', $userId)->get();
    }
}
