<?php

namespace FeIron\LaraFrame\lib\traits;


trait DataTables{

    public function get_results(\Illuminate\Http\Request $request, \Illuminate\Database\Eloquent\Builder $QueryBuilder): array
    {
        $datainfo = [];

        //Read request values
        // $draw = $request->input('draw');
        // $row = $request->input('start');
        // $rowperpage = $request->input('length'); // Rows display per page
        // $searchValue = $request->input('search')['value']; // Search value
        // $page = $request->input('page'); // Page number
        $columnIndex = $request->input('order')[0]['column']; // Column index
        $columnName = $request->input('columns')[$columnIndex]['data']; // Column name
        $columnSortOrder = $request->input('order')[0]['dir']; // asc or desc
        

        $datainfo['recordsTotal'] = $QueryBuilder->get()->count();
        $datainfo['draw'] = $request->input('draw');
        $datainfo['page'] = $request->input('page');
        $datainfo['rowperpage'] = $request->input('length');
        $QueryBuilder->where(
            function ($query) use ($request) {
                foreach ($request->input('columns') as $column) {
                    if (isset($column['search']['value'])) {
                        $query->orwhere($column['data'],'like', ('%'.$column['search']['value'].'%'));
                    }
                }
            }

        );
        $datainfo['recordsFiltered'] = $QueryBuilder->count();
        $datainfo['data'] = $QueryBuilder->orderBy($columnName, $columnSortOrder)->paginate($datainfo['rowperpage'])->flatten()->toArray();
        

        return $datainfo;
    }
}

?>