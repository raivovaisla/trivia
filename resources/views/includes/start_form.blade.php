<form action="{{ route('trivia.start') }}" method="post">
    {{ csrf_field() }}
    <button type="submit">Sākt</button>
</form>