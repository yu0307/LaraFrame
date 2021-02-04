<!DOCTYPE html>
<html lang="en">

@php
    foreach(app()->FeFrame->getInitBlocks()??[] as $initBlock){
        $initBlock->execute(request());
    }
@endphp

@includeIf($siteInfo['theme'].'::header')
@includeIf($siteInfo['theme'].'::footer')
@includeIf($siteInfo['theme'].'::sidebar')

@php
    $resoucesList=[];
@endphp
@foreach (app()->FeFrame->getResources() as $target=>$Resources)
        @if ($target=='push')
            @foreach ($Resources as $location=>$assets)
                @push($location)
                    @foreach ($assets as $key=>$asset)
                        @if (false=== in_array($key,$resoucesList))
                            @php
                                array_push($resoucesList,$key)
                            @endphp
                            {!!$asset!!}
                        @endif
                    @endforeach
                @endpush
            @endforeach
        @else
            @foreach ($Resources as $location=>$assets)
                @prepend($location)
                    @foreach ($assets as $key=>$asset)
                        @if (false=== in_array($key,$resoucesList))
                            @php
                                array_push($resoucesList,$key)
                            @endphp
                            {!!$asset!!}
                        @endif
                    @endforeach
                @endprepend
            @endforeach
        @endif
@endforeach
<head>
    <title>@yield('title')</title>
    <meta charset="utf-8">
    @yield('header')
    @stack('headerstyles')
    @stack('headerscripts')
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<!-- BEGIN BODY -->
<body>
    @include($siteInfo['theme'].'::index')
    @yield('footer')
    @stack('footerscripts')
</body>
</html>