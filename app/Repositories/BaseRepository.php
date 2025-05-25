<?php

namespace App\Repositories;

use App\Models\BaseModel;
use App\Params\BaseParam;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

abstract class BaseRepository
{
    /**
     * BaseModel.
     */
    protected BaseModel $model;

    /**
     * construct.
     */
    public function __construct()
    {
        $this->model = app($this->model());
    }

    public function create(array $data): ?BaseModel
    {
        return $this->model->create($data);
    }

    public function update(array $data, int $id): ?BaseModel
    {
        $model = $this->getById($id);
        if (empty($model)) {
            return null;
        }
        if (!$model->update($data)) {
            return null;
        }

        return $model;
    }

    public function delete(int $id): int
    {
        return $this->getById($id)->delete();
    }

    public function getById(int $id, array $withs = []): ?BaseModel
    {
        $query = $this->model->query();

        if (!empty($withs)) {
            $query->with($withs);
        }

        return $query->find($id);
    }

    public function getByParam(BaseParam $param): Collection
    {
        return $this->getQueryByParam($param)->get();
    }

    public function getPaginatorByParam(BaseParam $param): LengthAwarePaginator
    {
        // page init
        extract($this->getPaginateParams($param));

        return $this->getQueryByParam($param)->paginate($perPage, $columns, $pageName, $page);
    }

    abstract protected function model(): string;

    protected function getQueryByParam(BaseParam $param): Builder
    {
        $query = $this->model->query();

        // withs
        $withs = $param->getWiths();
        if (!empty($withs)) {
            $query->with($withs);
        }

        // with counts
        $withCounts = $param->getWithCounts();
        if (!empty($withCounts)) {
            $query->withCount($withCounts);
        }

        // sort
        $sortBy = $param->getSortBy();
        if (!empty($sortBy)) {
            foreach ($sortBy as $sort => $isDesc) {
                $query->orderBy($this->getSortByFullColumnName($sort), $isDesc ? 'desc' : 'asc');
            }
        }

        return $query;
    }

    protected function getPaginateParams(BaseParam $param): array
    {
        // per page
        $perPage = config('app.per_page');
        if (method_exists($param, 'getPerPage')) {
            $perPage = $param->getPerPage();
            if (!empty($perPage)) {
                $perPage = (int) $perPage;
            }
        }

        // page
        $page = 1;
        if (method_exists($param, 'getPage')) {
            $page = $param->getPage();
            if (!empty($page)) {
                $page = (int) $page;
            }
        }

        return [
            'perPage' => $perPage,
            'columns' => ['*'],
            'pageName' => 'page',
            'page' => $page,
        ];
    }

    protected function getSortByFullColumnName(string $sortBy): string
    {
        return $this->model->qualifyColumn($sortBy);
    }
}
