<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Statuses;
use App\Http\Services\ApiServices;
use App\Http\Requests\StoreStatusRequest;
use App\Http\Requests\UpdateStatusRequest;


class StatusController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $status = Statuses::all();

            return ApiServices::statusCode200($status);

        } catch (Exception $e) {

            return ApiServices::statusCode500($e->getMessage());

        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreStatusRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Statuses $status)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Statuses $status)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateStatusRequest $request, Statuses $status)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Statuses $status)
    {
        //
    }
}
