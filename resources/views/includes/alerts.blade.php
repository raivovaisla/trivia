<div class="alert-wrapper">
    @if(session('error'))
        <div class="alert alert-error">{{ Session::get('error') }}</div>
    @elseif($errors->any())
        @foreach ($errors->all() as $error)
                <div class="alert alert-error">{{ $error }}</div>
        @endforeach
    @elseif(session('success'))
        <div class="alert alert-success">{{ Session::get('success') }}</div>
    @endif
</div>