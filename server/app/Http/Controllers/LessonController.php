<?php

namespace App\Http\Controllers;

use App\Models\Lesson;
use App\Models\LessonContent;
use App\Models\Option;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class LessonController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'set_id' => 'required|integer|exists:sets,id',
                'contents' => 'required|array',
                'contents.type' => ['required', Rule::in(['quiz', 'learn'])],
                'contents.content' => 'required|string',

                'contents.options' => ['required_if:contents.type,learn', 'array'],
                'contents.options.option_text' => ['required_if:contents.type,learn', 'string'],
                'contents.options.is_correct' => ['required_if:contents.type,learn', 'boolean'],
            ]);

            $lessonOrder = Lesson::where("set_id", $validatedData["set_id"])->max("order");

            $lesson = Lesson::create([
                "set_id" => $validatedData["set_id"],
                "name" => $validatedData["name"],
                "order" => $lessonOrder + 1
            ]);

            $lessonContentOrder = LessonContent::where("lesson_id", $lesson->id)->max("order");

            $lessonContent = LessonContent::create([
                "lesson_id" => $lesson->id,
                "type" => $validatedData["contents"]["type"],
                "content" => $validatedData["contents"]["content"],
                "order" => $lessonContentOrder + 1
            ]);

            if ($validatedData["contents"]["type"] === "quiz") {
                Option::create([
                    "lesson_content_id" => $lessonContent->id,
                    "option_text" => $validatedData["contents"]["options"]["option_text"],
                    "is_correct" => $validatedData["contents"]["options"]["is_correct"],
                ]);
            }

            return response()->json([
                "status" => "success",
                "message" => "Lesson successfully added",
                "data" => $lesson
            ]);
        } catch (ValidationException $error) {
            return response()->json([
                "status" => "error",
                "message" => $error->getMessage(),
                "errors" => $error->errors()
            ]);
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($lesson_id)
    {
        try {
            $lesson = Lesson::where("id", $lesson_id)->firstOrFail();

            $lesson->delete();

            return response()->json([
                "status" => "success",
                "message" => "Lesson successfully deleted"
            ]);
        } catch (ModelNotFoundException $error) {
            return response()->json([
                "status" => "not_found",
                "message" => "Resource not found"
            ]);
        }
    }
}
