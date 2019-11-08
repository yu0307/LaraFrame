@fePortlet([
    'id'=>'FeThemeManagement',
    'headerText'=>'<h1><strong>Theme</strong> Selected</h1>',
    'headerBackground'=>'transparent',
    'attr'=>'dataTarget="'.route('updateThemeSetting').'"'
    ])
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
@endfePortlet

@fePortlet([
    'id'=>'FeThemeSettings',
    'headerText'=>'<h2>Theme <strong>Settings</strong></h2>'
    ])
    {!!app()->FeFrame->RenderThemeSettings()!!}
    <div class="clearfix">
    </div>
@endfePortlet

<div>
    <button class="btn btn-primary pull-right m-b-10 btn_themesave">Update Theme Settings</button>
    <div class="clearfix">
    </div>
</div>