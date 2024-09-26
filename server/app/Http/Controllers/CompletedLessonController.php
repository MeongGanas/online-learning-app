<?php

namespace App\Http\Controllers;

use App\Models\CompletedLesson;
use App\Models\Lesson;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class CompletedLessonController extends Controller
{
    public function track(Request $request, $lesson_id)
    {
        try {
            Lesson::where('id', $lesson_id)->firstOrFail();

            CompletedLesson::create([
                'lesson_id' => $lesson_id,
                'user_id' => $request->user()->id,
            ]);

            return response()->json([
                "status" => "success",
                "message" => "Lesson successfully completed"
            ], 200);
        } catch (ModelNotFoundException $error) {
            return response()->json([
                "status" => "not_found",
                "message" => "Resource not found"
            ], 404);
        }
    }
}
