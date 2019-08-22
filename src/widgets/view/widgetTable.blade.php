@extends('fe_widgets::widgetFrame')

@section('Widget_contents')
<table class="table table-striped">
    <thead>
        @isset($headers)
            @if(is_array($headers))
                <tr>
                    @foreach($headers as $header)
                        <th>{{$header}}</th>
                    @endforeach
                </tr>
            @else
                {{$headers}}
            @endif
        @endisset
    </thead>
    <tbody>

    </tbody>
</table>
    
@endsection