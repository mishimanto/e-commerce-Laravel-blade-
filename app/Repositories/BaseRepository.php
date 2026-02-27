<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

abstract class BaseRepository
{
    /**
     * @var Model
     */
    protected $model;

    /**
     * BaseRepository constructor.
     *
     * @param Model $model
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * Get all records
     *
     * @param array $columns
     * @param array $relations
     * @return Collection
     */
    public function all(array $columns = ['*'], array $relations = []): Collection
    {
        return $this->model->with($relations)->get($columns);
    }

    /**
     * Get paginated records
     *
     * @param int $perPage
     * @param array $columns
     * @param array $relations
     * @return LengthAwarePaginator
     */
    public function paginate(int $perPage = 15, array $columns = ['*'], array $relations = []): LengthAwarePaginator
    {
        return $this->model->with($relations)->paginate($perPage, $columns);
    }

    /**
     * Find record by id
     *
     * @param int $id
     * @param array $columns
     * @param array $relations
     * @return Model|null
     */
    public function find(int $id, array $columns = ['*'], array $relations = []): ?Model
    {
        return $this->model->with($relations)->find($id, $columns);
    }

    /**
     * Find record by id or fail
     *
     * @param int $id
     * @param array $columns
     * @param array $relations
     * @return Model
     */
    public function findOrFail(int $id, array $columns = ['*'], array $relations = []): Model
    {
        return $this->model->with($relations)->findOrFail($id, $columns);
    }

    /**
     * Find record by criteria
     *
     * @param array $criteria
     * @param array $columns
     * @param array $relations
     * @return Model|null
     */
    public function findBy(array $criteria, array $columns = ['*'], array $relations = []): ?Model
    {
        return $this->model->with($relations)->where($criteria)->first($columns);
    }

    /**
     * Get records by criteria
     *
     * @param array $criteria
     * @param array $columns
     * @param array $relations
     * @return Collection
     */
    public function getBy(array $criteria, array $columns = ['*'], array $relations = []): Collection
    {
        return $this->model->with($relations)->where($criteria)->get($columns);
    }

    /**
     * Create new record
     *
     * @param array $data
     * @return Model
     */
    public function create(array $data): Model
    {
        return $this->model->create($data);
    }

    /**
     * Update record
     *
     * @param int $id
     * @param array $data
     * @return Model
     */
    public function update(int $id, array $data): Model
    {
        $model = $this->findOrFail($id);
        $model->update($data);
        
        return $model;
    }

    /**
     * Update or create record
     *
     * @param array $criteria
     * @param array $data
     * @return Model
     */
    public function updateOrCreate(array $criteria, array $data): Model
    {
        return $this->model->updateOrCreate($criteria, $data);
    }

    /**
     * Delete record
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $model = $this->findOrFail($id);
        
        return $model->delete();
    }

    /**
     * Delete records by criteria
     *
     * @param array $criteria
     * @return bool
     */
    public function deleteBy(array $criteria): bool
    {
        return $this->model->where($criteria)->delete() > 0;
    }

    /**
     * Get count of records
     *
     * @param array $criteria
     * @return int
     */
    public function count(array $criteria = []): int
    {
        if (empty($criteria)) {
            return $this->model->count();
        }
        
        return $this->model->where($criteria)->count();
    }

    /**
     * Check if records exist
     *
     * @param array $criteria
     * @return bool
     */
    public function exists(array $criteria): bool
    {
        return $this->model->where($criteria)->exists();
    }

    /**
     * Get fresh model instance
     *
     * @return Model
     */
    public function getModel(): Model
    {
        return $this->model;
    }

    /**
     * Get new query builder instance
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query()
    {
        return $this->model->newQuery();
    }
}