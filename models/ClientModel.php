<?php
/**
 * Created by PhpStorm.
 * User: leila
 * Date: 23/05/2019
 * Time: 09:34
 */
require_once 'BaseModel.php';
class ClientModel extends BaseModel
{
    public function __construct()
    {
        parent::__construct();
        $this->tableName = 'clients';
    }

}
