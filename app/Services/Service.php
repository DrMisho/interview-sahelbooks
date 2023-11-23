<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;

abstract class Service
{

    /**
     * @var
     */
    protected $model;
    protected bool $count = false;

    /**
     * @param array $array
     * @return mixed
     */
    abstract protected function queryResult(array $array);

    /**
     * Get List
     *
     * @param array $array
     * @return Collection
     */
    public function getList(array $array = []): Collection
    {
        
        if(key_exists('order_by', $array))
        {
            return $this->queryResult($array)->orderBy($array['order_by'], $array['order_dir']?? 'asc')->get();
        }
        return $this->queryResult($array)->get();
    }

    /**
     * @param $id
     * @param $slug
     * @return mixed
     */
    public function getSingle($id, $slug = ''): mixed
    {
        if ($id ?? false) {
            $data = $this->queryResult(['id' => $id])->first();
            if($data)
                return $data;
            failResponse("not found", 404)->send();
            exit;
        } else {
            if (!empty($slug)) {
                $data = $this->queryResult(['slug' => $slug])->first();
                if(! is_null($data) )
                    return $data;
                failResponse("not found", 404)->send();
                exit;
            }
        }
    }

    /**
     * @param $query
     * @return int
     */
    public function getCount(array $query = []): int
    {
        if (array_key_exists('page', $query)) {
            unset($query['page']);
        }

        if (array_key_exists('limit', $query)) {
            unset($query['limit']);
        }

        if (array_key_exists('order_by', $query)) {
            unset($query['order_by']);
        }
        $this->count = true;
        return $this->queryResult($query)->get()->count();
    }

    /**
     * @param array $form
     * @return Model
     */
    public function store(array $form): Model
    {
        return $this->model->create($form)->refresh();
    }

    /**
     * Edit Model
     *
     * @param array $form
     * @param [type] $model
     * @return Model
     */
    public function edit(array $form, &$model): Model
    {
        $model->update($form);
        return $model->refresh();
    }

    /**
     * Delete Model
     *
     * @param [type] $model
     * @return boolean
     */
    public function delete(&$model): bool
    {
        return $model->delete();
    }

}