<?php

namespace App\Http\Controllers;

use App\Models\Set;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class SetController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $course_id)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255'
            ]);

            $lastOrder = Set::where('course_id', $course_id)->max('order');

            $sets = Set::create([
                'name' => $request->name,
                'course_id' => $course_id,
                'order' => $lastOrder + 1
            ]);

            return response()->json([
                "status" => "success",
                "message" => "Sets successfully added",
                "data" => $sets
            ], 201);
        } catch (ValidationException $error) {
            return response()->json([
                "status" => "error",
                "message" => $error->getMessage(),
                "errors" => $error->errors()
            ], 400);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Set $set)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Set $set)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Set $set)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Set $set)
    {
        //
    }
}
