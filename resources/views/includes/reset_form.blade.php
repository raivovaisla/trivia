<form action="{{ route('trivia.reset') }}" method="post">
    {{ csrf_field() }}
    <button type="submit">Sākt no jauna</button>
</form>