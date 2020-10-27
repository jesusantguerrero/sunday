<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
// use Freesgen\Traits\Querify;

abstract class BaseController extends Controller
{
    protected $model;
    protected $createdMessage = "created";
    protected $searchable = ["id"];
    protected $validationRules = [];
    protected $sorts = [];
    protected $includes = [];
    protected $appends = [];
    protected $filters = [];

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validateLocal($request);

        $resource = $this->model::create($request->post());
        return [
            "message" => $this->createdMessage,
            "data" => $resource
        ];
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id, Request $request)
    {
        $queryParams = $request->query();
        $relationships = isset($queryParams['relationships']) ? $queryParams['relationships'] : [];
        $query = $this->model::with($relationships)->find($id);

        return $query;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $resource = $this->model::find($id);
        $resource->update($request->post());
        return $resource;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $resource = $this->model::find($id);
        $resource->delete();
        return $resource;
    }

    public function validateLocal(Request $request) {
       return $request->validate($this->validationRules);
    }
}
