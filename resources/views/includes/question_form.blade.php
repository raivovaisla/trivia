@php
    $trivia = session('trivia');
    $currentQuestion = $trivia['current_question'];
@endphp

<form action="{{ route('trivia.answer', ['questionId' => $currentQuestion]) }}" method="post">
    {{ csrf_field() }}
    <h3>Jautājums {{ $currentQuestion }}.</h3>
    <p class="questionText">{{ $trivia['questions'][$currentQuestion] }}</p>
    <label for="answer">Atbilde</label>
    <input type="number" name="answer" id="answer" required autocomplete="off"
           value="{{ $gameOver ? $answer : '' }}" {{ $gameOver ? 'disabled' : '' }}>
    @if(!$gameOver)
        <button type="submit">Atbildēt</button>
    @endif
</form>