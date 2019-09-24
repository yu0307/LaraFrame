<?php

namespace feiron\felaraframe\lib\traits;


trait DataTables{

    public function get_results(\Illuminate\Http\Request $request, \Illuminate\Database\Eloquent\Builder $QueryBuilder, $globalSearchTargets=[], callable $dataTableFormater=null): array
    {
        $datainfo = [];

        //Read request values
        // $draw = $request->input('draw');
        // $row = $request->input('start');
        // $rowperpage = $request->input('length'); // Rows display per page
        $searchValue = $request->input('search')['value']; // Search value
        // $page = $request->input('page'); // Page number
        $columnIndex = $request->input('order')[0]['column']; // Column index
        $columnName = $request->input('columns')[$columnIndex]['data']; // Column name
        $columnSortOrder = $request->input('order')[0]['dir']; // asc or desc
        

        $datainfo['recordsTotal'] = $QueryBuilder->count();
        $datainfo['draw'] = $request->input('draw');
        $datainfo['page'] = $request->input('page');
        $datainfo['rowperpage'] = $request->input('length');
        // Building column specific search--------------------------
        $QueryBuilder->where(
            function ($query) use ($request) {
                foreach ($request->input('columns') as $column) {
                    if (isset($column['search']['value'])) {
                        $query->where($column['data'],'like', ('%'.$column['search']['value'].'%'));
                    }
                }
            }

        );
        //Building Global search--------------------------
        if(strlen($searchValue)>0){
            if (count($globalSearchTargets) > 0) {
                $QueryBuilder->where(
                    function ($query) use ($globalSearchTargets, $searchValue) {
                        foreach ($globalSearchTargets as $column) {
                            $query->orWhere($column, 'like', ('%' . $searchValue . '%'));
                        }
                    }
                );
            }
        }
        

        $datainfo['recordsFiltered'] = $QueryBuilder->count();
        $datainfo['data'] = $QueryBuilder->orderBy($columnName, $columnSortOrder)->paginate($datainfo['rowperpage'])->flatten()->toArray();
        if(is_callable($dataTableFormater)===true){
            foreach($datainfo['data'] as $index=>$row){
                $datainfo['data'][$index]= $dataTableFormater($row);
            }
        }
        return $datainfo;
    }
}

?>