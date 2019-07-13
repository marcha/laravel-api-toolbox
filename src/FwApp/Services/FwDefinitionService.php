<?php
/**
 * Created by PhpStorm.
 * User: nikola.marcic
 * Date: 3/5/18
 * Time: 08:10
 */

namespace Erpmonster\FwApp\Services;


use Erpmonster\FwApp\Repositories\FwDefinitionRepository;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Database\DatabaseManager;
use Illuminate\Support\Facades\Cache;

class FwDefinitionService
{
    /**
     * @var DatabaseManager
     */
    private $databaseManager;
    /**
     * @var Dispatcher
     */
    private $dispatcher;
    /**
     * @var FwDefinitionRepository
     */
    private $repository;

    public function __construct(
        DatabaseManager $databaseManager,
        Dispatcher $dispatcher,
        FwDefinitionRepository $repository)
    {

        $this->databaseManager = $databaseManager;
        $this->dispatcher = $dispatcher;
        $this->repository = $repository;
    }

    public function getDefinition($objId)
    {
        $cacheName = 'definition-'.$objId;

        $data = Cache::tags(['fw'])->remember($cacheName, 1440, function() use ($objId) {

            $searchFields = [];

            $headers = [];
            $form = [];

            $definition = null;
            $defaultItem = [];

            $fwObject =  $this->repository->getById($objId);

            $fwTableFields  = $fwObject->tableFields;
            $fwEditFields   = $fwObject->editFields;
            $object = [
                'objId'         => $fwObject->id,
                'title'         => $fwObject->title,
                'resourceUrl'   => $fwObject->resource_url,
                'keyField'      => $fwObject->key_field,
                'masterField'   => $fwObject->master_field,
                'checkboxSelect'=> $fwObject->checkbox_select,
            ];

            foreach ($fwTableFields as $field)
            {
                $data['text'] = $field->title;
                $data['value'] = $field->field_name;
                $data['sortable'] = $field->sortable;
                $data['control'] = $field->td_control;
                $data['class'] = $field->td_class_name;
                if ($field->searchable) {
                    $searchFields[] = $field->field_name;
                }
                $headers[] = $data;
            }
            $data = [];
            foreach ($fwEditFields as $field)
            {
                $data['id']     = $field->id;
                $data['name']   = $field->field_name;
                $data['text']   = $field->title;
                $data['type']   = $field->input_type;
                $data['validation'] = $field->validation_cli;
                $data['lookupFwObjId'] = $field->lookup_fw_object_id;
                $data['lookupKeyFieldName'] = $field->lookup_key_field_name;
                $data['lookupDisplayFieldName'] = $field->lookup_display_field_name;
                $form[]=$data;

                if ($field->input_type == 'checkbox' || $field->input_type == 'boolean'){
                    $defaultItem[$field->field_name]= $field->default_value==1;
                }
                else{
                    $defaultItem[$field->field_name] = $field->default_value;
                }

            }

            $object['searchFields'] = $searchFields;

            $definition = $object;

            $definition['headers'] = $headers;
            $definition['formItems'] = $form;
            $definition['defaultItem'] = $defaultItem;
            return $definition;
        });
        return $data;
    }
}
