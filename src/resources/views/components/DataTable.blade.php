@pushonce('headerstyles','DataTables')
<link type="text/css" href="{{asset('feiron/felaraframe/plugins/datatables/dataTables.min.css')}}" rel="stylesheet">
<!-- DataTables -->
<link type="text/css" href="{{asset('feiron/felaraframe/plugins/select2/dist/css/select2.min.css')}}" rel="stylesheet">
<!-- SELECT 2 -->
<link type="text/css" href="{{asset('feiron/felaraframe/components/Datatables/general.css')}}" rel="stylesheet">
<!-- SELECT 2 -->
@endpushonce

@pushonce('footerscripts','DataTables')
<script type="text/javascript" src="{{asset('feiron/felaraframe/plugins/datatables/jquery.dataTables.min.js')}}">
</script> <!-- DataTables -->
<script type="text/javascript" src="{{asset('feiron/felaraframe/plugins/datatables/dataTables.bootstrap.min.js')}}">
</script> <!-- DataTables -->
<script type="text/javascript" src="{{asset('feiron/felaraframe/plugins/select2/dist/js/select2.full.min.js')}}">
</script> <!-- Select2 -->
@endpushonce

<div class="panel">
    <div class="panel-header bg-{{ $header_bg??'dark' }}">
        <div class="row">
            <div class="col-md-4 col-sm-12">
                <h3>{{ $header??''}}</h3>
            </div>
            <div class="col-md-8 col-sm-12">
                {{ $slot }}
            </div>
        </div>
    </div>
    <div class="panel-content">

        @if (isset($headerList)&&!empty($headerList))
            @php
                $footer='';
                $tableID=$tableID??('datatable'.rand(100,99999));
            @endphp

            <table id="{{ $tableID }}" class="table dataTable table-striped filter-{{ $FilterLocation??'footer'}} table-hover" style="width:100%;">
                <thead>
                    <tr>
                        @foreach ($headerList as $header)
                            <th>{{$header}}</th>
                            @php
                                $footer.='<th></th>';
                            @endphp
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                        {!!($tableData??'')!!}
                </tbody>
                <tfoot>
                    <tr>
                        {!!$footer!!}
                    </tr>
                </tfoot>
            </table>

            @php
                $jsonSettings=[
                    'serverSide'=>false,
                    'processing'=>true,
                    'lengthMenu'=>[10, 20, 50, 100],
                    'searchDelay'=>1500,
                    'columns'=>[]
                ];
                $jsonSettings=array_merge($jsonSettings, ($JsSettins ?? []));
            @endphp

            @prepend('footerscripts')
                <script type="text/javascript">
                    var {{($tableID.'_setting')}}={!!json_encode($jsonSettings)!!};
                </script>
            @endprepend
            
            @pushonce('DocumentReady','DataTables')
                @if ($jsonSettings['serverSide']===true)
                    {{($tableID.'_setting')}}.ajax.data=function(data,settings){
                        data.page = settings.oInstance.DataTable().page.info().page + 1;
                        data._token = $('meta[name="csrf-token"]').attr('content');
                        if ({{($tableID.'_setting')}}.ajaxDataFunc!=undefined && {{($tableID.'_setting')}}.ajaxDataFunc instanceof Function) {
                            {{($tableID.'_setting')}}.ajaxDataFunc(data);
                        }
                    }
                @endif
                $('#{{$tableID}}').DataTable({{($tableID.'_setting')}});
            @endpushonce
        @else
            <h3>No table header is set, check component configuration.</h3>
        @endif
    </div>
</div>