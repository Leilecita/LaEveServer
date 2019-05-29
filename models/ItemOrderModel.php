<?php
/**
 * Created by PhpStorm.
 * User: leila
 * Date: 09/05/2019
 * Time: 15:32
 */
require_once 'BaseModel.php';
class ItemOrderModel extends BaseModel
{
    public function __construct()
    {
        parent::__construct();
        $this->tableName = 'items_order';
    }
}