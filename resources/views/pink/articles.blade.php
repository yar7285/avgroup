@extends(env('THEME').'.layouts.site')

@section('navigation')
    {!! $navigation !!}
@endsection
@section('content')
    {!! $content !!}
@endsection
@section('sidebar')
    {!! $rightBar !!}
@endsection
@section('footer')
    {!! $footer !!}
@endsection