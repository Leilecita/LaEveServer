<?php
/**
 * Created by PhpStorm.
 * User: leila
 * Date: 09/05/2019
 * Time: 14:51
 */


include __DIR__ . '/../config/config.php';
require __DIR__ . '/../libs/dbhelper.php';

use vielhuber\dbhelper\dbhelper;

abstract class BaseModel
{
    protected $tableName  = '';
    private $db;

    function __construct(){
        global $DBCONFIG;
        $this->db = new dbhelper();
        //$this->db->connect('pdo', 'mysql', '127.0.0.1', 'root', null, 'loa', 3306);
        $this->db->connect('pdo', 'mysql', $DBCONFIG['HOST'], $DBCONFIG['USERNAME'], $DBCONFIG['PASSWORD'],$DBCONFIG['DATABASE'],$DBCONFIG['PORT']);
    }

    function findById($id){
        return $this->db->fetch_row('SELECT * FROM '.$this->tableName.' WHERE id = ?',$id);
    }

    function findByClientId($client_id){
        return $this->db->fetch_row('SELECT * FROM '.$this->tableName.' WHERE client_id = ?',$client_id);
    }

    function findByIdAndZone($filters=array()){
        $conditions = join(' AND ',$filters);
        return $this->db->fetch_row('SELECT * FROM '.$this->tableName.( empty($filters) ?  '' : ' WHERE '.$conditions ));
    }

    public function getDb(){
        return $this->db;
    }

    function find($filters=array()){
        $conditions = join(' AND ',$filters);
        $query = 'SELECT * FROM '.$this->tableName .( empty($filters) ?  '' : ' WHERE '.$conditions ).' ORDER BY created DESC';
        return $this->db->fetch_row($query);
    }

    function findAll($filters=array(),$paginator=array()){
        $conditions = join(' AND ',$filters);
        $query = 'SELECT * FROM '.$this->tableName .( empty($filters) ?  '' : ' WHERE '.$conditions ).' ORDER BY created DESC LIMIT '.$paginator['limit'].' OFFSET '.$paginator['offset'];
        return $this->db->fetch_all($query);
    }




    function getSpinner($type){
        $query = 'SELECT DISTINCT '.$type.' FROM '.$this->tableName;
        return $this->db->fetch_all($query);

    }

    function findAllByDate($filters=array()){
        $conditions = join(' AND ',$filters);
        $query = 'SELECT * FROM '.$this->tableName .( empty($filters) ?  '' : ' WHERE '.$conditions ).' ORDER BY created DESC';

        return $this->db->fetch_all($query);
    }

    function getOrdersClient($filters=array(),$paginator=array(),$order_state){

        $conditions = join(' AND ',$filters);
        //$query = 'SELECT *, o.id as order_id FROM orders o JOIN clients c ON o.client_id = c.id '.( empty($filters) ?  '' : ' WHERE '.$conditions ).' ORDER BY o.'.$order_state.' DESC , o.created DESC LIMIT '.$paginator['limit'].' OFFSET '.$paginator['offset'];
        $query = 'SELECT *, o.id as order_id FROM orders o JOIN clients c ON o.client_id = c.id '.( empty($filters) ?  '' : ' WHERE '.$conditions ).' ORDER BY o.'.$order_state.' DESC, o.created DESC, o.state_prepare DESC LIMIT '.$paginator['limit'].' OFFSET '.$paginator['offset'];
        return $this->db->fetch_all($query);

    }

    function getOrdersClient2($filters=array(),$paginator=array(),$order_state){

        $conditions = join(' AND ',$filters);
        //$query = 'SELECT *, o.id as order_id FROM orders o JOIN clients c ON o.client_id = c.id '.( empty($filters) ?  '' : ' WHERE '.$conditions ).' ORDER BY o.'.$order_state.' DESC , o.created DESC LIMIT '.$paginator['limit'].' OFFSET '.$paginator['offset'];
        $query = 'SELECT *, o.id as order_id FROM orders o JOIN clients c ON o.client_id = c.id '.( empty($filters) ?  '' : ' WHERE '.$conditions ).' ORDER BY o.state DESC, o.created DESC, o.state_prepare DESC LIMIT '.$paginator['limit'].' OFFSET '.$paginator['offset'];
        return $this->db->fetch_all($query);

    }

    function findAllOrder($filters=array(),$paginator=array(),$order_state){
        $conditions = join(' AND ',$filters);
       // $query = 'SELECT * FROM '.$this->tableName .( empty($filters) ?  '' : ' WHERE '.$conditions ).' ORDER BY state_check DESC LIMIT '.$paginator['limit'].' OFFSET '.$paginator['offset'];
        $query = 'SELECT * FROM '.$this->tableName .( empty($filters) ?  '' : ' WHERE '.$conditions ).' ORDER BY '.$order_state.' DESC LIMIT '.$paginator['limit'].' OFFSET '.$paginator['offset'];
        return $this->db->fetch_all($query);
    }

    function findAllItems($filters=array()){
        $conditions = join(' AND ',$filters);
        $query = 'SELECT * FROM '.$this->tableName .( empty($filters) ?  '' : ' WHERE '.$conditions ).' ORDER BY billing DESC,created ASC';
        return $this->db->fetch_all($query);
    }

    function findAllItemsCheckComplete($filters=array()){
        $conditions = join(' AND ',$filters);
        $query = 'SELECT * FROM '.$this->tableName .( empty($filters) ?  '' : ' WHERE '.$conditions ).' ORDER BY created ASC';
        return $this->db->fetch_all($query);
    }

    function findAllByName($filters=array(),$paginator=array()){
        $conditions = join(' AND ',$filters);
        $query = 'SELECT * FROM '.$this->tableName .( empty($filters) ?  '' : ' WHERE '.$conditions ).' ORDER BY name ASC LIMIT '.$paginator['limit'].' OFFSET '.$paginator['offset'];
        return $this->db->fetch_all($query);
    }

    function findAllByNameCli($filters=array(),$paginator=array()){
        $conditions = join(' AND ',$filters);
        $query = 'SELECT * FROM '.$this->tableName .( empty($filters) ?  '' : ' WHERE '.$conditions ).' ORDER BY nomcli ASC LIMIT '.$paginator['limit'].' OFFSET '.$paginator['offset'];
        return $this->db->fetch_all($query);
    }

    function findAllByDesc($filters=array(),$paginator=array()){
        $conditions = join(' AND ',$filters);
        $query = 'SELECT * FROM '.$this->tableName .( empty($filters) ?  '' : ' WHERE '.$conditions ).' ORDER BY desart ASC LIMIT '.$paginator['limit'].' OFFSET '.$paginator['offset'];
        return $this->db->fetch_all($query);
    }

    function findAllByDebt($filters=array(),$paginator=array()){
        $conditions = join(' AND ',$filters);
        $query = 'SELECT * FROM '.$this->tableName .( empty($filters) ?  '' : ' WHERE '.$conditions ).' ORDER BY debt ASC LIMIT '.$paginator['limit'].' OFFSET '.$paginator['offset'];
        return $this->db->fetch_all($query);
    }

    function findAllByClientId($filters=array(),$paginator=array()){
        $conditions = join(' AND ',$filters);
        $query = 'SELECT * FROM '.$this->tableName .( empty($filters) ?  '' : ' WHERE '.$conditions ).' ORDER BY created DESC LIMIT '.$paginator['limit'].' OFFSET '.$paginator['offset'];
        return $this->db->fetch_all($query);
    }

    function findAllByEmployeeId($filters=array(),$paginator=array()){
        $conditions = join(' AND ',$filters);
        $query = 'SELECT * FROM '.$this->tableName .( empty($filters) ?  '' : ' WHERE '.$conditions ).' ORDER BY created DESC LIMIT '.$paginator['limit'].' OFFSET '.$paginator['offset'];
        return $this->db->fetch_all($query);
    }


    function sum($client_id){
        $response = $this->db->fetch_row('SELECT SUM(value) AS total FROM '.$this->tableName.' WHERE client_id = ? ORDER BY created ASC',$client_id);
        if($response['total']!=null){
            return $response;
        }else{
            $response['total']=0;
            return $response;
        }
    }

    function sumAllOperations(){
        $response = $this->db->fetch_row('SELECT SUM(value) AS total FROM '.$this->tableName.' ORDER BY created ASC');
        if($response['total']!=null){
            return $response;
        }else{
            $response['total']=0;
            return $response;
        }
    }



    function save($data){
        return $this->db->insert($this->tableName, $data );
    }

    function update($id, $data){
        return  $this->db->update($this->tableName, $data,['id' => "$id"]);
    }

    function delete($id){
        return ($this->db->delete($this->tableName, ['id' => $id]) == 1);
    }

    function deleteByOrderId($order_id){
        return ($this->db->delete($this->tableName, ['order_id' => $order_id]) == 1);
    }

    function deleteAllByOrderId($order_id){
        return ($this->db->delete($this->tableName, ['order_id' => $order_id]));
    }

    function base64_to_jpeg($base64_string, $output_file) {
        // open the output file for writing
        $ifp = fopen( $output_file, 'wb' );

        fwrite( $ifp, base64_decode( $base64_string ) );

        // clean up the file resource
        fclose( $ifp );

        return $output_file;
    }
}