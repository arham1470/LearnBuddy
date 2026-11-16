<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminQuizController extends Controller
{
    public function index(Request $request): View
    {
        $query = Quiz::with(['course', 'questions'])->latest();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('title', 'like', "%{$search}%");
        }

        $quizzes = $query->paginate(15)->withQueryString();
        $totalAttempts = QuizAttempt::count();

        return view('admin.quizzes.index', compact('quizzes', 'totalAttempts'));
    }
}
