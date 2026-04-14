<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SurveyQuestion;
use App\Models\SurveyQuestionOption;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SurveyController extends Controller
{
    public function index()
    {
        $questions = SurveyQuestion::with(['options'])->withCount('options')->latest()->paginate(10);
        return view('admin.surveys.index', compact('questions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'question' => 'required|string',
            'type' => 'required|in:options,text',
            'is_required' => 'boolean',
            'options' => 'required_if:type,options|array',
            'options.*.text' => 'required_if:type,options|string',
        ]);

        DB::transaction(function () use ($request) {
            $question = SurveyQuestion::create([
                'question' => $request->question,
                'type' => $request->type,
                'is_required' => $request->is_required ?? false,
                'is_active' => true,
            ]);

            if ($request->type === 'options' && $request->has('options')) {
                foreach ($request->options as $optionData) {
                    if (!empty($optionData['text'])) {
                        SurveyQuestionOption::create([
                            'question_id' => $question->id,
                            'option_text' => $optionData['text'],
                        ]);
                    }
                }
            }
        });

        return response()->json(['message' => 'Question created successfully!']);
    }


    public function update(Request $request, SurveyQuestion $survey)
    {
        $request->validate([
            'question' => 'required|string',
            'type' => 'required|in:options,text',
            'is_required' => 'boolean',
            'options' => 'required_if:type,options|array',
            'options.*.text' => 'required_if:type,options|string',
        ]);

        DB::transaction(function () use ($request, $survey) {
            $survey->update([
                'question' => $request->question,
                'type' => $request->type,
                'is_required' => $request->is_required ?? false,
            ]);

            if ($request->type === 'options') {
                $survey->options()->delete();
                foreach ($request->options as $optionData) {
                    if (!empty($optionData['text'])) {
                        SurveyQuestionOption::create([
                            'question_id' => $survey->id,
                            'option_text' => $optionData['text'],
                        ]);
                    }
                }
            } else {
                $survey->options()->delete();
            }
        });

        return response()->json(['message' => 'Question updated successfully!']);
    }

    public function destroy(SurveyQuestion $survey)
    {
        $survey->delete();
        return response()->json(['message' => 'Question deleted successfully!']);
    }

    /**
     * Toggle active status
     */
    public function toggleStatus(SurveyQuestion $survey)
    {
        $survey->update(['is_active' => !$survey->is_active]);
        return response()->json(['message' => 'Status updated successfully!', 'is_active' => $survey->is_active]);
    }

    /**
     * View all user responses
     */
    public function responses()
    {
        $responses = \App\Models\SurveyResponse::with(['user', 'question', 'option'])
            ->latest()
            ->paginate(20);

        return view('admin.surveys.responses', compact('responses'));
    }
}
