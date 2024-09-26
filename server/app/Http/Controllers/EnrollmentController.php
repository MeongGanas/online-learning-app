<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Enrollment;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class EnrollmentController extends Controller
{
    public function register(Request $request, $course_slug)
    {
        try {
            $course = Course::where('slug', $course_slug)->firstOrFail();

            $isEnrolled = Enrollment::where('user_id', $request->user()->id)->where('course_id', $course->id)->exists();

            if (!$isEnrolled) {
                Enrollment::create([
                    "user_id" => $request->user()->id,
                    "course_id" => $course->id
                ]);
            }

            return response()->json([
                "status" => "success",
                "message" => "User registered successful"
            ], 201);
        } catch (ModelNotFoundException $error) {
            return response()->json([
                "status" => "not_found",
                "message" => "Resource not found"
            ], 404);
        }
    }

    public function getProgress(Request $request)
    {
        return response()->json([
            "status" => "success",
            "message" => "User progress retrived successfully",
            "data" => [
                "progress" => $request->user()->enrollments->pluck("course"),
                "completed_lessons" => $request->user()->completed_lessons->pluck("lesson")
            ]
        ]);
    }
}
