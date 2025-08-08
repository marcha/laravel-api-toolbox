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
use Illuminate\Database\Eloquent\ModelNotFoundException;


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
    $model = $this->getModel();

    $model->fill($data);

    $model->save();

    return $model;
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
    return $this->getModel()->newQuery();
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
    if (is_object($model) && defined(get_class($model) . '::CREATED_AT')) {
      return $model::CREATED_AT ?: 'created_at';
    }
    return 'created_at';
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
   *
   * @param string          $keyField       // npr. 'id' ili 'code'
   * @param string          $displayField   // npr. 'name' ili 'title'
   * @param int|array|null  $selectedId     // može 15, [15,16], '15,16'
   * @param string|null     $q              // query string
   * @return array{selected:\Illuminate\Support\Collection,list:\Illuminate\Support\Collection}
   */
  public function lookup($keyField, $displayField, $selectedId, $q)
  {
    // 1) Normalizuj selectedId u niz int-ova
    if (is_string($selectedId)) {
      $selectedId = array_filter(array_map('trim', explode(',', $selectedId)));
    }
    if (!is_array($selectedId)) {
      $selectedId = $selectedId ? [$selectedId] : [];
    }
    $selectedIds = collect($selectedId)->map(fn($v) => (int) $v)->filter()->unique()->values()->all();

    // 2) SELECT sa aliasima tako da response bude { id, name }
    $select = [
      "{$keyField} as id",
      "{$displayField} as name",
    ];

    // 3) Učitaj selected (ako ima)
    $selected = collect();
    if (!empty($selectedIds)) {
      $selected = $this->getModel()
        ->select($select)
        ->whereIn($keyField, $selectedIds)
        ->orderBy($displayField)
        ->get();
    }

    // 4) Učitaj list po pretrazi (ograniči 20)
    $q = trim((string) $q);
    $listQuery = $this->getModel()
      ->select($select)
      ->when($q !== '', fn($qb) => $qb->where($displayField, 'LIKE', '%' . $q . '%'))
      ->orderBy($displayField)
      ->limit(20);

    $list = $listQuery->get();

    // 5) Ukloni duplikate ako je neki iz selected već u listi
    $list = $list->reject(fn($row) => $selected->contains('id', $row->id))->values();

    return [
      'selected' => $selected,
      'list'     => $list,
    ];
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
}
