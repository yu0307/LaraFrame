@extends('felaraframe::layout')

@section('content')
    @fePortlet([
        'id'=>'controlPanel',
        'headerBackground'=>'dark',
        'headerText'=>'<h3>Admin Control Panel</h3>'
        ])
        <ul class="nav nav-tabs nav-primary">
            <li class="active"><a href="#General" data-toggle="tab">General</a></li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane fade active in" id="General">
                <p class="light">General configuration goes here. sort of as a catch it all.</p>
            </div>
        </div>
    @endfePortlet
@endsection