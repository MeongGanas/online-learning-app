<?php

namespace App\Http\Controllers;

use App\Models\LessonContent;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class OptionController extends Controller
{
    public function checkAnswer(Request $request, $lesson_id, $content_id)
    {
        try {
            $request->validate([
                'option_id' => 'required|exists:options,id'
            ]);

            $data = LessonContent::where("id", $content_id)->where("lesson_id", $lesson_id)->with('options')->firstOrFail();

            if ($data["type"] !== "quiz") {
                return response()->json([
                    "status" => "error",
                    "message" => "Only for quiz content"
                ], 400);
            }

            return response()->json([
                "status" => "success",
                "message" => "Check answer success",
                "data" => [
                    "question" => $data["content"],
                    "user_answer" => $data["options"]["option_text"],
                    "is_correct" => $data["options"]["is_correct"] === 1 ? true : false
                ]
            ], 200);
        } catch (ModelNotFoundException $error) {
            return response()->json([
                "status" => "not_found",
                "message" => "Resource not found"
            ], 404);
        }
    }
}
