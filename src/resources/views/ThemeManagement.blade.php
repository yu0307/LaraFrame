<x-fe-portlet id="FeThemeManagement" dataTarget="{{route('updateThemeSetting')}}" header-bg="transparent">
    <x-slot name="header">
        <h3><h1><strong>Theme</strong> Selected</h1></h3>
    </x-slot>
    <div class="row">
        <div class="col-sm-12 col-md-4">
            <div class="alert alert-info">
                Select a Site Theme from the list below. 
            </div>
            <select name="fe_themeSelector" id="fe_themeSelector">
                @foreach (app()->FeFrame->GetThemes() as $theme)
                    <option {{(app()->FeFrame->GetCurrentTheme()->name()==$theme->name())?'SELECTED':''}} value="{{$theme->name()}}">{{$theme->name()}}</option>
                @endforeach
            </select>
            <div id="feThemeInfo">

            </div>
        </div>
        <div class="col-sm-12 col-md-8">
            <div id="feThemeDetails">

            </div>
        </div>
    </div>
</x-fe-portlet>

<x-fe-portlet id="FeThemeSettings">
    <x-slot name="header">
        <h2>Theme <strong>Settings</strong></h2>
    </x-slot>
    {!!app()->FeFrame->RenderThemeSettings()!!}
    <div class="clearfix">
    </div>
</x-fe-portlet>

<div>
    <button class="btn btn-primary pull-right m-b-10 btn_themesave">Update Theme Settings</button>
    <div class="clearfix">
    </div>
</div>