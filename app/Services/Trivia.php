<?php

namespace App\Services;

use App\Exceptions\Trivia\AlreadyStarted;
use App\Exceptions\Trivia\InvalidQuestion;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;

class Trivia
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * Length of the trivia game.
     *
     * @var int
     */
    protected $questionAmount = 20;

    /**
     * @var string
     */
    protected $triviaRoute = 'http://numbersapi.com/random/trivia?fragment&min=1&max=1000';

    /**
     * Trivia constructor.
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * If trivia is started, return necessary variables.
     *
     * @return array
     */
    public function getTriviaVariables()
    {
        $trivia = session('trivia');
        if ($trivia) {
            $questionNumber = $trivia['current_question'];
            $question = $trivia['questions'][$questionNumber];
            $answer = $trivia['answers'][$questionNumber];
            $gameOver = $trivia['game_over'];

            return compact('questionNumber', 'question', 'answer', 'gameOver');
        }

        return [];
    }

    /**
     * Creates trivia session key and creates the first question.
     *
     * @throws AlreadyStarted
     */
    public function prepareTrivia()
    {
        if (session()->has('trivia')) {
            throw new AlreadyStarted('Trivija jau ir uzsākta!');
        }

        session(['trivia' => [
            'questions' => [],
            'answers' => [],
            'current_question' => 0,
            'game_over' => false
        ]]);

        $this->makeQuestion();
    }

    /**
     * Make the next question and store it in session.
     */
    /**
     * @return mixed
     */
    public function makeQuestion()
    {
        $question = $this->fetchQuestion();
        $questionText = $question['text'];

        $trivia = session('trivia');
        //Retry if the question has already been played.
        if (in_array($questionText, $trivia['questions'])) {
            return $this->makeQuestion();
        }

        $questionNr = $trivia['current_question'] + 1;

        $trivia['questions'][$questionNr] = $question['text'];
        $trivia['answers'][$questionNr] = $question['number'];
        $trivia['current_question'] = $questionNr;

        session(['trivia' => $trivia]);
        return true;
    }

    /**
     * Gets trivia question from an API call.
     *
     * @return mixed
     */
    public function fetchQuestion()
    {
        $request = new Request('GET', $this->triviaRoute);
        $response = $this->client->send(
            $request,
            [
                'headers' => ['Content-type' => 'application/json'],
            ]
        );

        return json_decode($response->getBody(), true);
    }

    /**
     * Process answer of trivia's question
     *
     * @param int $questionId
     * @param int $answer
     * @return array
     * @throws InvalidQuestion
     */
    public function answerTrivia(int $questionId, int $answer)
    {
        $trivia = session('trivia');

        if (!$trivia) {
            throw new InvalidQuestion('Nevar atbildēt uz jautājumu, neuzsākot spēli!');
        }

        $currentQuestion = $trivia['current_question'];

        if ($currentQuestion !== $questionId) {
            throw new InvalidQuestion('Atbildēts uz nepareizo jautājumu!');
        }

        $correctAnswer = $trivia['answers'][$questionId];
        if ($correctAnswer !== $answer) {
            $trivia['game_over'] = true;
            session(['trivia' => $trivia]);

            return ['error', 'Atbildēts nepareizi.'];
        } elseif ($currentQuestion === $this->questionAmount) {
            $trivia['game_over'] = true;
            session(['trivia' => $trivia]);

            return ['success', 'Apsveicu, esiet atbildējuši pareizi uz visiem jautājumiem!'];
        }

        $this->makeQuestion();

        return ['success', 'Atbildēts pareizi.'];
    }

    /**
     * Resets trivia progress by unsetting trivia's session key.
     */
    public function resetTrivia()
    {
        session()->forget('trivia');
    }
}