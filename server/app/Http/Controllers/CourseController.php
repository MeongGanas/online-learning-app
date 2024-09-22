<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class CourseController extends Controller
{
    public function getAll()
    {
        return response()->json([
            "status" => "success",
            "message" => "Course retrived successfully",
            "data" => [
                "courses" => Course::all()
            ]
        ]);
    }

    public function getDetail($course_slug)
    {
        try {
            $course = Course::where('slug', $course_slug)->with(['sets.lessons'])->firstOrFail();

            return response()->json([
                "status" => "success",
                "message" => "Course retrived successfully",
                "data" => $course
            ]);
        } catch (ModelNotFoundException $error) {
            return response()->json([
                "status" => "not_found",
                "message" => "Resource not found",
            ], 404);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'name' => 'required|string|max:255',
                'slug' => 'required|string|unique:courses',
            ]);

            if ($request->has('description')) {
                $request->validate(['description' => 'string']);
                $data['description'] = $request->description;
            }

            $course = Course::create($data);

            return response()->json([
                "status" => "success",
                "message" => "Course successfully created",
                "data" => $course
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
     * Update the specified resource in storage.
     */
    public function update(Request $request, $course_slug)
    {
        try {
            $course = Course::where('slug', $course_slug)->firstOrFail();

            $data = $request->validate([
                'name' => 'required|string|max:255',
            ]);

            if ($request->has('description')) {
                $request->validate(['description' => 'string']);
                $data['description'] = $request->description;
            }

            if ($request->has('is_published')) {
                $request->validate(['is_published' => 'boolean']);
                $data['is_published'] = $request->is_published;
            }

            $course->update($data);

            $updatedCourse = $course->fresh();

            return response()->json([
                "status" => "success",
                "message" => "Course successfully updated",
                "data" => $updatedCourse
            ], 201);
        } catch (ModelNotFoundException $error) {
            return response()->json([
                "status" => "not_found",
                "message" => "Resource not found",
            ], 404);
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
    public function destroy($course_slug)
    {
        try {
            $course = Course::where('slug', $course_slug)->firstOrFail();

            $course->delete();

            return response()->json([
                "status" => "success",
                "message" => "Course successfully deleted",
            ], 201);
        } catch (ModelNotFoundException $error) {
            return response()->json([
                "status" => "not_found",
                "message" => "Resource not found",
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
