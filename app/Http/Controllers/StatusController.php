<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Statuses;
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

            return response()->json([
                "success" => [
                    "status" => "200", "title" => "OK", "detail" => $status
                ]
            ], 201);
        } catch (Exception $e) {

            return response()->json([
                "error" => [
                    "status" => "500",
                    "title" => "Internal Server Error",
                    "detail" => $e->getMessage(),
                ]
            ], 500);
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
