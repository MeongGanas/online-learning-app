<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Set;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class SetController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Course $course)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255'
            ]);

            $lastOrder = Set::where('course_id', $course->id)->max('order');

            $sets = Set::create([
                'name' => $request->name,
                'course_id' => $course->id,
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
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Course $course, $set_id)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255'
            ]);

            $set = Set::where('id', $set_id)->where('course_id', $course->id)->where('name', $request->name)->firstOrFail();

            $set->delete();

            return response()->json([
                "status" => "success",
                "message" => "Sets successfully deleted",
            ], 201);
        } catch (ModelNotFoundException $error) {
            return response()->json([
                "status" => "not_found",
                "message" => "Resource not found"
            ], 404);
        } catch (ValidationException $error) {
            return response()->json([
                "status" => "error",
                "message" => $error->getMessage(),
                "errors" => $error->errors()
            ], 400);
        }
    }
}
