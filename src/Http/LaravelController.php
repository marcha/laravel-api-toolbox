<?php

/**
 * Created by PhpStorm.
 * User: nikola.marcic
 * Date: 2/20/18
 * Time: 00:47
 */

namespace Erpmonster\Http;


use InvalidArgumentException;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Routing\Controller;
use Illuminate\Http\JsonResponse;
use Erpmonster\Utils\Architect\Architect;
use JsonSerializable;


abstract class LaravelController extends Controller
{
    /**
     * Defaults
     * @var array
     */
    protected $defaults = [];

    /**
     * @var array
     */
    protected $filters;

    /**
     * Create a json response
     * @param  mixed  $data
     * @param  integer $statusCode
     * @param  array  $headers
     * @param int $options
     * @return JsonResponse
     */
    protected function response($data, $statusCode = 200, array $headers = [], $options = 0)
    {
        if ($data instanceof Arrayable && !$data instanceof JsonSerializable) {
            $data = $data->toArray();
        }

        return new JsonResponse($data, $statusCode, $headers, $options);
    }

    /**
     * Parse data using architect
     * @param  mixed $data
     * @param  array  $options
     * @param  string $key
     * @return mixed
     */
    protected function parseData($data, array $options, $key = null)
    {
        if ($options['limit']) {
            return $this->parseDataWithPagination($data, $options, $key);
        }

        $architect = new Architect();

        return $architect->parseData($data, $options['modes'], $key);
    }

    protected function parseDataWithPagination($data, array $options, $key = null)
    {

        $architect = new Architect();

        $parsedData = $architect->parseData($data['data'], $options['modes'], $key);

        $paginator = $data['pagination'];

        $paginationData = [
            'current_page' => $paginator->currentPage(),
            'last_page' => $paginator->lastPage(),
            'total' => $paginator->total(),
            'per_page' => $paginator->perPage(),
            'from' => $paginator->firstItem(),
            'to' => $paginator->lastItem()
        ];

        return array_merge($parsedData, $paginationData);
    }

    /**
     * Page sort
     * @param array $sort
     * @return array
     */
    protected function parseSort(array $sort)
    {
        return array_map(function ($sort) {
            if (!isset($sort['direction'])) {
                $sort['direction'] = 'asc';
            }

            return $sort;
        }, $sort);
    }

    /**
     * Parse include strings into resource and modes
     * @param  array  $includes
     * @return array The parsed resources and their respective modes
     */
    protected function parseIncludes(array $includes)
    {
        $return = [
            'includes' => [],
            'modes' => []
        ];

        foreach ($includes as $include) {
            $explode = explode(':', $include);

            if (!isset($explode[1])) {
                $explode[1] = $this->defaults['mode'];
            }

            $return['includes'][] = $explode[0];
            $return['modes'][$explode[0]] = $explode[1];
        }

        return $return;
    }

    /**
     * Parse filter group strings into filters
     * Filters are formatted as key:operator(value)
     * Example: name:eq(esben)
     * @param  array  $filter_groups
     * @return array
     */
    protected function parseFilterGroups(array $filter_groups)
    {
        $return = [];

        foreach ($filter_groups as $group) {
            if (!array_key_exists('filters', $group)) {
                throw new InvalidArgumentException('Filter group does not have the \'filters\' key.');
            }

            $filters = array_map(function ($filter) {
                if (!isset($filter['not'])) {
                    $filter['not'] = false;
                }

                return $filter;
            }, $group['filters']);

            $return[] = [
                'filters' => $filters,
                'or' => isset($group['or']) ? $group['or'] : false
            ];
        }

        return $return;
    }

    /**
     * Parse GET parameters into resource options
     * @return array
     */
    protected function parseResourceOptions($request = null)
    {
        if ($request === null) {
            $request = app('request');
        }

        $this->defaults = array_merge([
            'includes' => [],
            'sort' => [],
            'limit' => null,
            'page' => null,
            'mode' => 'embed',
            'filter_groups' => []
        ], $this->defaults);

        $includes = $this->parseIncludes($request->get('includes', $this->defaults['includes']));
        $sort = $this->parseSort($request->get('sort', $this->defaults['sort']));
        $limit = $request->get('limit', $this->defaults['limit']);
        $page = $request->get('page', $this->defaults['page']);
        $filter_groups = $this->parseFilterGroups($request->get('filter_groups', $this->defaults['filter_groups']));

        if ($page !== null && $limit === null) {
            throw new InvalidArgumentException('Cannot use page option without limit option');
        }

        return [
            'includes' => $includes['includes'],
            'modes' => $includes['modes'],
            'sort' => $sort,
            'limit' => $limit,
            'page' => $page,
            'filter_groups' => $filter_groups
        ];
    }

    private function addToFilter($fieldName, $value, $operator,  $not = false)
    {
        if ($value) {
            $this->filters['filters'][] = ['key' => $fieldName, 'operator' => $operator, 'value' => $value, 'not' => $not];
        }
    }

    private function addCriteria(array $criteria){
        $this->addToFilter($criteria['field'], $criteria['value'], $criteria['operator'], $criteria['not']);
    }
    /**
     * Build filter by array 
     * 
     * @param array $criteria
     *  [
     *      [
     *          'field'=>'some_field_1', 
     *          'operator'=>'eq', 
     *          'value'=>'something',
     *          'not'=>false
     *      ],
     *      [
     *          'field'=>'some_field_2', 
     *          'operator'=>'ct', 
     *          'value'=>'word',
     *          'not'=>true
     *      ]
     *  ]
     */

    public function buildFilter(array $criteria)
    {
        $this->filters = [];
        foreach($criteria as $c){
            $this->addCriteria($c);
        }
        
        if (count($this->filters) > 0) {
            $filter_groups[0] = array_merge($this->filters, ['or' => false]);
        }


        return count($filter_groups) > 0 ? $filter_groups : null;
}
