<?php
/**
 * Created by PhpStorm.
 * User: leila
 * Date: 09/05/2019
 * Time: 15:29
 */
require_once 'BaseModel.php';
class ProductModel extends BaseModel
{
    public function __construct()
    {
        parent::__construct();
        $this->tableName = 'products';
    }

}