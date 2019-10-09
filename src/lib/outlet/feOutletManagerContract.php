<?php
namespace feiron\felaraframe\lib\outlet;

interface feOutletManagerContract{

    public function FetchOutlet($outletName);

    public function bindOutlet($outletName, \feiron\felaraframe\lib\outlet\feOutletContract $outlet);

    public function replaceOutlet($outletName, \feiron\felaraframe\lib\outlet\feOutletContract $outlet);

    public function removeOutlet($outletName,$target);

    public function hasOutlet($outletName);

    public function registerOutlet($outletName);

    public function getAvailableOutlets();

    public function OutletCalls($outletName, $params);

    public function OutletResources($outletName);

    public function OutletRenders($outletName, $asObjects);
}

?>