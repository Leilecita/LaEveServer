<?php
/**
 * Created by PhpStorm.
 * User: leila
 * Date: 28/09/2020
 * Time: 13:05
 */

require_once 'BaseModel.php';


class AssignedZoneModel extends BaseModel
{
    public function __construct()
    {
        parent::__construct();
        $this->tableName = 'assigned_zones';
    }


    function findAssignedZones($filters=array()){
        $conditions = join(' AND ',$filters);
        $query = 'SELECT z.name as name, a.id as id FROM zones z JOIN assigned_zones a ON a.zone_id = z.id '.( empty($filters) ?  '' : ' WHERE '.$conditions ).' ORDER BY a.created DESC ';
        return $this->getDb()->fetch_all($query);
    }
}