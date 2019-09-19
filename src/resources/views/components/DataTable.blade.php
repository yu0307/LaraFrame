@pushonce('headerstyles','DataTables')
<link type="text/css" href="{{asset('feiron/felaraframe/plugins/datatables/dataTables.min.css')}}" rel="stylesheet">
<!-- DataTables -->
<link type="text/css" href="{{asset('feiron/felaraframe/plugins/select2/dist/css/select2.min.css')}}" rel="stylesheet">
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
        @php
            if(isset($headerList)&&!empty($headerList)){

            }else{

            }
        @endphp

        @if (isset($headerList)&&!empty($headerList))
            @php
            $footer='';
            @endphp
            <table id="{{ $tableID??('datatable'.rand(100,999))}}" class="table filter-{{ $FilterLocation??'footer'}} table-hover">
                <thead>
                    <tr>
                        @foreach ($headerList as $header)
                            <th>{{$header}}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>

                </tbody>
                <tfoot>
                    <tr>
                        {{$footer}}
                    </tr>
                </tfoot>
            </table>
        @else
            <h3>No table header is set, check component configuration.</h3>
        @endif
    </div>
</div>