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
    <div class="panel-header bg-{{ $headerBg??'dark' }}">
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
                $id=$id??('datatable'.rand(100,99999));
            @endphp

            <table id="{{ $id }}" class="table dataTable table-striped filter-{{ $filterLocation??'footer'}} table-hover" style="width:100%;">
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
                $jsonSettings=array_merge($jsonSettings, ($jsSettins ?? []));
            @endphp

            @prepend('footerscripts')
                <script type="text/javascript">
                    var {{($id.'_setting')}}={!!json_encode($jsonSettings)!!};
                </script>
            @endprepend
            
            @pushonce('DocumentReady','dataTables')
                @if ($jsonSettings['serverSide']===true)
                    {{($id.'_setting')}}.ajax.data=function(data,settings){
                        data.page = settings.oInstance.DataTable().page.info().page + 1;
                        data._token = $('meta[name="csrf-token"]').attr('content');
                        if ({{($id.'_setting')}}.ajaxDataFunc!=undefined && {{($id.'_setting')}}.ajaxDataFunc instanceof Function) {
                            {{($id.'_setting')}}.ajaxDataFunc(data);
                        }
                    }
                    {{($id.'_setting')}}.ajax.dataSrc=function(json){
                        if ({{($id.'_setting')}}.ajaxDataSrcFunc!=undefined && {{($id.'_setting')}}.ajaxDataSrcFunc instanceof Function) {
                            json.data={{($id.'_setting')}}.ajaxDataSrcFunc(data);
                        }
                        return json.data;
                    }
                @endif
                var my_dataTable=$('#{{$id}}').DataTable({{($id.'_setting')}});
                $('.dataTable').on('click','button.dt_details',function(){
                    var DetailData=my_dataTable.row($(this).closest('tr')).data();
                    if(undefined!==DetailData[$(this).attr('dataTarget')]){
                        $('.shadow_tr').remove();
                        var header='';
                        var tableContents='';
                        $(DetailData[$(this).attr('dataTarget')]).each(function($index,$row){
                            tableContents+='<tr>';
                            $.each( $row, function( key, value ) {
                                if(key!=='pivot'){
                                    if($index==0){
                                        header+=('<th>'+key+'</th>');
                                    }
                                    tableContents+='<td>'+((value!=null)?value:'')+'</td>';
                                }
                            });
                            tableContents+='</tr>';
                        });
                        $('<tr class="shadow_tr"><td style="display:none" colspan="'+$(this).closest('tr').find('td').length+'">'+((tableContents.length>0)?('<table class="table table-striped table-mini table-hover"><tr>'+header+'</tr>'+tableContents+'</table>'):'<h5 style="text-align:center">There are no data associate with this record.</h5>')+'<button class="btn btn-danger btn-sm btn-mini pull-right closeShadowTr"> Close </button></td></tr>').insertAfter($(this).closest('tr')).find('td').slideDown(300);
                        
                    }
                });
                $('.dataTable').on('click','button.closeShadowTr',function(){
                    $(this).closest('tr').slideUp(300).remove();
                });
                @if (($enableHeaderSearch??false)===true)
                    var table=$('#{{$id}}');
                    var timer;
                    $(table).find('thead tr').clone(false).appendTo($(table).find('thead'));
                    $(table).find('thead tr:eq(1) th:last-child, thead tr:eq(1) th.disableFilter, thead tr:eq(1) th.sorting_disabled').html('');
                    $(table).find('thead tr:eq(1) th').each(function (i) {
                        if(!$(this).hasClass('disableFilter') && !$(this).hasClass('sorting_disabled')){
                            var title = $(this).removeClass('sorting').text();
                            $(this).html('<input type="text" placeholder="Search ' + title + '" />');
                            $('input', this).on('keyup change', function () {
                                if (my_dataTable.column(i).search() !== this.value) {
                                    clearTimeout(timer);
                                    var tarval = this.value;
                                    timer = setTimeout(function() {
                                        my_dataTable
                                            .column(i)
                                            .search(tarval)
                                            .draw();
                                    }, 700);
                                }
    
                            });
                        }
                    });
                @endif
            @endpushonce
        @else
            <h3>No table header is set, check component configuration.</h3>
        @endif
    </div>
</div>