<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\SurveyResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SurveyResponseController extends Controller
{
    /**
     * Store survey responses and mark original user as completed.
     */
    public function store(Request $request)
    {
        $request->validate([
            'responses' => 'required|array',
            'responses.*.question_id' => 'required|exists:survey_questions,id',
            'responses.*.option_id' => 'nullable|exists:survey_question_options,id',
            'responses.*.text_answer' => 'nullable|string',
        ]);

        $user = Auth::user();

        try {
            DB::transaction(function () use ($request, $user) {
                foreach ($request->responses as $response) {
                    // Skip if both are empty (though JS should prevent this)
                    if (empty($response['option_id']) && empty($response['text_answer'])) {
                        continue;
                    }

                    SurveyResponse::updateOrCreate(
                        [
                            'user_id' => $user->id,
                            'question_id' => $response['question_id']
                        ],
                        [
                            'option_id' => $response['option_id'] ?? null,
                            'text_answer' => $response['text_answer'] ?? null
                        ]
                    );
                }

                // Mark user as completed so the popup never shows again
                $user->update(['survey_completed' => true]);
            });

            return response()->json([
                'status' => 'success',
                'message' => 'Thank you for your response!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong. Please try again later.'
            ], 500);
        }
    }
}
