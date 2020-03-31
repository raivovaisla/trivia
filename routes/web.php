<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

$trivia = 'TriviaController';

Route::get('/', function () {
    return view('welcome');
});

Route::name('trivia.')->prefix('/trivia')->group(function () use ($trivia) {
    Route::get('', "$trivia@getTrivia")->name('get');
    Route::post('/start', "$trivia@startTrivia")->name('start');
    Route::post('/answer/{questionId}', "$trivia@answerTrivia")->name('answer');
    Route::post('/reset', "$trivia@resetTrivia")->name('reset');
});
