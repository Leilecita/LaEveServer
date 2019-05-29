<?php
/**
 * Created by PhpStorm.
 * User: leila
 * Date: 09/05/2019
 * Time: 14:51
 */

require_once 'BaseModel.php';

class UserModel extends BaseModel
{
    public function __construct()
    {
        parent::__construct();
        $this->tableName = 'users';
    }
}