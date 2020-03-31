<?php

namespace App\Http\Controllers;

use App\Exceptions\Trivia\AlreadyStarted;
use App\Exceptions\Trivia\InvalidQuestion;
use App\Services\Trivia;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class TriviaController extends Controller
{
    /**
     * @var Trivia
     */
    protected $triviaService;

    /**
     * TriviaController constructor.
     * @param Trivia $trivia
     */
    public function __construct(Trivia $trivia)
    {
        $this->triviaService = $trivia;
    }

    /**
     * Trivia route.
     *
     * @return Factory|View
     */
    public function getTrivia()
    {
        $variables = $this->triviaService->getTriviaVariables();
        return view('pages.trivia', $variables);
    }

    /**
     * Begin the trivia game.
     *
     * @return RedirectResponse
     */
    public function startTrivia()
    {
        try {
            $this->triviaService->prepareTrivia();
        } catch (AlreadyStarted $e) {
            session()->flash('error', $e->getMessage());
        }
        return redirect()->back();
    }

    /**
     * Answer a trivia question.
     *
     * @param Request $request
     * @param int $questionId
     * @return RedirectResponse
     */
    public function answerTrivia(Request $request, int $questionId)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'answer' => "required|integer",
            ]
        );

        if ($validator->fails()) {
            session()->flash('error', 'Kļūda formas ievadē!');
            return redirect()->back();
        }

        try {
            list($type, $message) = $this->triviaService->answerTrivia($questionId, $request->get('answer'));
        } catch (InvalidQuestion $e) {
            session()->flash('error', $e->getMessage());
            return redirect()->back();
        }

        session()->flash($type, $message);
        return redirect()->back();
    }

    /**
     * Reset progress of the game.
     *
     * @return RedirectResponse
     */
    public function resetTrivia()
    {
        $this->triviaService->resetTrivia();
        return redirect()->back();
    }
}
