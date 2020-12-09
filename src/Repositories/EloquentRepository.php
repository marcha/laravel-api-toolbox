<?php

/**
 * Created by PhpStorm.
 * User: nikola.marcic
 * Date: 2/20/18
 * Time: 00:10
 */

namespace Erpmonster\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Erpmonster\Database\Eloquent\EloquentBuilderTrait;
use Erpmonster\Transformers\ResourceKeyDataSerializer;
use Erpmonster\Utils\Architect\Utility;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use League\Fractal\TransformerAbstract;
use League\Fractal\Manager as FractalManager;
use League\Fractal\Resource\Collection as FractalCollection;
use League\Fractal\Resource\Item as FractalItem;

abstract class EloquentRepository
{
    use EloquentBuilderTrait;

    protected $paginator;

    /**
     * @var $model Model|object
     */
    protected $model;

    protected $sortProperty = null;

    // 0 = ASC, 1 = DESC
    protected $sortDirection = 0;

    /**
     * @return Model|object
     */
    abstract protected function getModel();

    final public function __construct()
    {
        $this->model = $this->getModel();
    }

    /**
     * Get all resources
     * @param  array $options
     * @return Collection|mixed
     */
    public function get(array $options = [])
    {
        $query = $this->createBaseBuilder($options);

        if (key_exists('limit', $options) && $options['limit']) {

            $pagination = $query->paginate(intval($options['limit']));

            $data = $pagination->items();

            return ['data' => $data, 'pagination' => $pagination];
        }

        return $query->get();
    }


    /**
     * Get a resource by its primary key
     * @param  mixed $id
     * @param  array $options
     * @return Model|object
     */
    public function getById($id, array $options = [])
    {
        $query = $this->createBaseBuilder($options);

        return $query->find($id);
    }

    /**
     * Get all resources ordered by recentness
     * @param  array $options
     * @return Collection
     */
    public function getRecent(array $options = [])
    {
        $query = $this->createBaseBuilder($options);

        $query->orderBy($this->getCreatedAtColumn(), 'DESC');

        return $query->get();
    }

    /**
     * Get all distinct resources ordered by recentness
     * @param  array $columns
     * @param  array $options
     * @return Collection
     */
    public function getRecentDistinct(array $columns = ['*'], array $options = [])
    {
        $query = $this->createBaseBuilder($options);

        $query->select($columns)->orderBy($this->getCreatedAtColumn(), 'DESC');

        return $query->distinct()->get();
    }

    /**
     * Get all resources by a where clause ordered by recentness
     * @param  string $column
     * @param  mixed $value
     * @param  array  $options
     * @return Collection
     */
    public function getRecentWhere($column, $value, array $options = [])
    {
        $query = $this->createBaseBuilder($options);
        $query->where($column, $value);
        $query->orderBy($this->getCreatedAtColumn(), 'DESC');
        return $query->get();
    }

    /**
     * Get latest resource
     * @param  array $options
     * @return Model|object
     */
    public function getLatest(array $options = [])
    {
        $query = $this->createBaseBuilder($options);

        $query->orderBy($this->getCreatedAtColumn(), 'DESC');

        return $query->first();
    }

    /**
     * Get latest resource by a where clause
     * @param  string $column
     * @param  mixed $value
     * @param  array  $options
     * @return Model|object
     */
    public function getLatestWhere($column, $value, array $options = [])
    {
        $query = $this->createBaseBuilder($options);
        $query->where($column, $value);
        $query->orderBy($this->getCreatedAtColumn(), 'DESC');
        return $query->first();
    }

    /**
     * Get resources by a where clause
     * @param  string $column
     * @param  mixed $value
     * @param  array $options
     * @return Collection
     */
    public function getWhere($column, $value, array $options = [])
    {
        $query = $this->createBaseBuilder($options);

        $query->where($column, $value);

        return $query->get();
    }

    /**
     * Get resources by multiple where clauses
     * @param  array  $clauses
     * @param  array $options
     * @deprecated
     * @return Collection
     */
    public function getWhereArray(array $clauses, array $options = [])
    {
        $query = $this->createBaseBuilder($options);

        $query->where($clauses);

        return $query->get();
    }

    /**
     * Get resources where a column value exists in array
     * @param  string $column
     * @param  array  $values
     * @param  array $options
     * @return Collection
     */
    public function getWhereIn($column, array $values, array $options = [])
    {
        $query = $this->createBaseBuilder($options);

        $query->whereIn($column, $values);

        return $query->get();
    }

    /**
     * @param $data array
     * @return static|mixed
     */
    public function create(array $data)
    {
        $this->model->fill($data);

        $this->model->save();

        return $this->model;
    }

    /**
     * Delete a resource by its primary key
     * @param  mixed $id
     * @return void
     */
    public function ddelete($id)
    {
        $query = $this->createQueryBuilder();

        $query->where($this->getPrimaryKey($query), $id);
        $query->delete();
    }

    /**
     * Delete resources by a where clause
     * @param  string $column
     * @param  mixed $value
     * @return void
     */
    public function deleteWhere($column, $value)
    {
        $query = $this->createQueryBuilder();

        $query->where($column, $value);
        $query->delete();
    }

    /**
     * Delete resources by multiple where clauses
     * @param  array  $clauses
     * @return void
     */
    public function deleteWhereArray(array $clauses)
    {
        $query = $this->createQueryBuilder();

        $this->applyWhereArray($query, $clauses);
        $query->delete();
    }

    /**
     * Creates a new query builder with options set
     * @param  array $options
     * @return Builder
     */
    protected function createBaseBuilder(array $options = [])
    {
        $query = $this->createQueryBuilder();

        $this->applyResourceOptions($query, $options);

        if (empty($options['sort'])) {
            $this->defaultSort($query);
        }

        return $query;
    }

    /**
     * Creates a new query builder
     * @return Builder
     */
    protected function createQueryBuilder()
    {
        return $this->model->newQuery();
    }

    /**
     * Get primary key name of the underlying model
     * @param  Builder $query
     * @return string
     */
    protected function getPrimaryKey(Builder $query)
    {
        return $query->getModel()->getKeyName();
    }

    /**
     * Order query by the specified sorting property
     * @param  Builder $query
     * @return void
     */
    protected function defaultSort(Builder $query)
    {
        if (isset($this->sortProperty)) {
            $direction = $this->sortDirection === 1 ? 'DESC' : 'ASC';
            $query->orderBy($this->sortProperty, $direction);
        }
    }

    /**
     * Get the name of the "created at" column.
     * More info to https://laravel.com/docs/5.4/eloquent#defining-models
     * @return string
     */
    protected function getCreatedAtColumn()
    {
        $model = $this->model;
        return ($model::CREATED_AT) ? $model::CREATED_AT : 'created_at';
    }

    /**
     * @param $id mixed
     * @return Collection
     */
    public function getRequested($id)
    {
        $model = $this->getById($id);

        if (is_null($model)) {
            throw new ModelNotFoundException();
        }

        return $model;
    }

    /**
     * Lookup for select remote field
     * @param $keyField
     * @param $displayField
     * @param $selectedId
     * @param $q
     * @return mixed
     */
    public function lookup($keyField, $displayField, $selectedId, $q)
    {

        $data['selected'] = [];

        $data['list'] = [];
        if ($selectedId) {
            $data['selected'] = $this->model->select([$keyField, $displayField])->where($keyField, '=', $selectedId)->get();
        }

        $data['list'] = $this->model->select([$keyField, $displayField])
            ->where($displayField, 'LIKE', '%' . $q . '%')
            ->orderBy($displayField)
            ->limit(20)
            ->get();

        return $data;
    }

    /**
     * @param array $keys
     * @param array $data
     * @return mixed
     */
    public function updateOrCreate(array $keys, array $data)
    {
        $model = $this->getModel();
        return $model->updateOrCreate($keys, $data);
    }

    /**
     * @param Builder $query
     * @param array $clauses
     */
    protected function applyWhereArray(Builder $query, array $clauses)
    {
        foreach ($clauses as $key => $value) {
            if (is_array($value)) {
                $query->whereIn($key, $value);
            } else if (is_null($value)) {
                $query->whereNull($key);
            } else {
                $query->where($key, $value);
            }
        }
    }

    /**
     * Get resources count by a options
     * @param  array  $options
     * @return Model|object
     */
    public function count(array $options = [])
    {
        $query = $this->createQueryBuilder();

        $this->applyResourceOptions($query, $options);

        return $query->count();
    }


    /**
     * Get resources by logRowId
     * @param  string $value
     * @return Model|object
     */
    public function getByLogRowId($value)
    {
        $query = $this->createBaseBuilder();
        $query->where('log_row_id', $value);
        return $query->first();
    }

    /**
     * Update resources by logRowId (sync)
     * @param mixed $syncModel
     * @return Model|object
     */
    public function updateSync($syncModel)
    {
        $model = $this->getByLogRowId($syncModel->log_row_id);
        $data = $syncModel->toArray();
        if ($model) {
            $model->fill($data);
            $model->save();
        }
        return $model;
    }

    /**
     * Create resources by sync
     * @param mixed $syncModel
     * @return Model|object
     */
    public function createSync($syncModel)
    {
        $data = $syncModel->toArray();

        $model = $this->getModel();

        $model->fill($data);

        $model->save();

        return $model;
    }

    /**
     * Delete resources by sync
     * @param mixed $syncModel
     */
    public function deleteSync($syncModel)
    {
        $this->deleteWhere('log_row_id', $syncModel->log_row_id);
    }

    /**
     * Transform resources with "data" key in results
     * This is default fractal transforming
     * 
     * @param Collection|Model $data
     * @param TransformerAbstract $transformer 
     */
    public function transform($data, TransformerAbstract $transformer)
    {
        $resource = $this->getResourceByType($data, $transformer);

        $fractal = new FractalManager();

        $transformedData = $fractal->createData($resource)->toArray(); // Transform data

        return $transformedData;
    }

    /**
     * Transform resources with custom or without key resource key in results
     * This function use custom ResourceKeyDataSerializer 
     * 
     * @param Collection|Model $data
     * @param TransformerAbstract $transformer 
     * @param bool $toArray default false
     */
    public function transformWithCustomKey($data, TransformerAbstract $transformer, $resourceKey = null)
    {
        $resource = $this->getResourceByType($data, $transformer, $resourceKey);

        $fractal = new FractalManager();

        $fractal->setSerializer(new ResourceKeyDataSerializer());

        $transformedData = $fractal->createData($resource)->toArray();

        return $transformedData;
    }

    private function getResourceByType($data, $transformer, $resourceKey = null)
    {
        if (Utility::isCollection($data)) {
            return new FractalCollection($data, $transformer, $resourceKey); // Create a resource collection transformer
        } else {
            return new FractalItem($data, $transformer, $resourceKey); // Create a resource collection transformer
        }
    }
}
