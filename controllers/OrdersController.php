<?php
/**
 * Created by PhpStorm.
 * User: leila
 * Date: 09/05/2019
 * Time: 15:24
 */

require_once 'BaseController.php';
require_once  __DIR__.'/../models/OrderModel.php';
require_once __DIR__.'/../models/ItemOrderModel.php';
require_once __DIR__.'/../models/ClientModel.php';

class OrdersController extends BaseController
{
    private $items_order;
    private $clients;

    private $state_order;

    function __construct(){
        parent::__construct();
        $this->model = new OrderModel();
        $this->items_order= new ItemOrderModel();
        $this->clients= new ClientModel();

        $this->state_order="";
    }


    public function delete()
    {
        //TODO ver estado del pedido, si no esta entregado, se borra todo

        if(isset($_GET['id'])){
            $this->items_order->deleteAllByOrderId($_GET['id']);
        }
        parent::delete(); // TODO: Change the autogenerated stub
    }

    function changeFormatDate($origDate){

        $date = str_replace('/', '-', $origDate );
        $newDate = date("Y-m-d", strtotime($date));
            return $newDate;
    }

    function asingFilters(){
        $filters=array();

        if(isset($_GET['delivery_date'])) {
            $filters[] = 'delivery_date = "' . $_GET['delivery_date'] . '"';
            //$filters[] = 'delivery_date = "' .$this->changeFormatDate($_GET['delivery_date']). '"';
        }

        if(isset($_GET['state'])){
            if($_GET['state']== 'tocheck'){
                $this->state_order="state_check";
                $filters[] = 'tocheck = "true"';
                if(isset($_GET['state_order']) && ($_GET['state_order']) == "pendients"){
                    $filters[] = 'state_check = "tocheck"';
                }

            }else if ($_GET['state']== 'toprepare'){
                $this->state_order="state_prepare";
                $filters[] = 'toprepare = "true"';
                if(isset($_GET['state_order']) && ($_GET['state_order']) == "pendients"){
                    $filters[] = 'state_prepare = "toprepare"';
                }

            }else if ($_GET['state']== 'tobilling'){
                $this->state_order="state_billing";
                $filters[] = 'tobilling = "true"';
                if(isset($_GET['state_order']) && ($_GET['state_order']) == "pendients"){
                    $filters[] = 'state_billing = "tobilling"';
                }

            }else if ($_GET['state']== 'todelivery'){
                $this->state_order="state_delivery";
                $filters[] = 'todelivery = "true"';
                if(isset($_GET['state_order']) && ($_GET['state_order']) == "pendients"){
                    $filters[] = 'state_delivery = "todelivery"';
                }

            }
        }

        if(isset($_GET['zone']) && !empty($_GET['zone'])){
            //$filters[] = 'loccli like "%'.$_GET['zone'].'%"';
            $filters[] = 'assigned_zone = "' . $_GET['zone'] . '"';
        }


        if(isset($_GET['query']) && !empty($_GET['query'])){
           // $filters[] = 'comcli like "%'.$_GET['query'].'%"';
            $filters[] = '(comcli like "%'.$_GET['query'].'%" OR nomcli like "%'.$_GET['query'].'%")';
        }


        return $filters;
    }

    function miniFilter(){
        $filters=array();

        if(isset($_GET['query']) && !empty($_GET['query'])){
            //$filters[] = 'comcli like "%'.$_GET['query'].'%"';
            $filters[] = '(comcli like "%'.$_GET['query'].'%" OR nomcli like "%'.$_GET['query'].'%")';
        }

        if(isset($_GET['zone']) && !empty($_GET['zone'])){
            //$filters[] = 'loccli like "%'.$_GET['zone'].'%"';

            $filters[] = 'assigned_zone = "' . $_GET['zone'] . '"';
        }

        return $filters;
    }
    function listAllOrders(){

            $listReport = array();

            $list_orders_by_deliver_date = $this->getModel()->getAllOrders($this->miniFilter(),$this->getPaginator());

            $this->returnSuccess(200, $this->getReport($list_orders_by_deliver_date,$listReport));
    }


    function getReport($list_orders_by_deliver_date,$listReport){

        for ($j = 0; $j < count($list_orders_by_deliver_date); ++$j) {

            $items_order_list = $this->items_order->findAllItems(array('order_id = "' .$list_orders_by_deliver_date[$j]['order_id'].'"'));

            $array_item_product = array();

            $array_item_product_rem = array();

            $array_item_product_add = array();

            $total_amount=0;
            for ($i = 0; $i < count($items_order_list); ++$i) {


                if($items_order_list[$i]['billing'] == "remito"){
                    $array_item_product_rem[] = array('item_order_id' => $items_order_list[$i]['id'],'product_descr' => $items_order_list[$i]['product_descr'], 'price' => $items_order_list[$i]['price'],
                        'preci1' => $items_order_list[$i]['preci1'],'preci2' => $items_order_list[$i]['preci2'],'preci3' => $items_order_list[$i]['preci3'],'preci4' => $items_order_list[$i]['preci4'],'preci5' => $items_order_list[$i]['preci5'],
                        'quantity' => $items_order_list[$i]['quantity'],'loaded' => $items_order_list[$i]['loaded'],'reasigned_quantity' => $items_order_list[$i]['reasigned_quantity'],
                        'pendient_stock' => $items_order_list[$i]['pendient_stock'],'billing' => $items_order_list[$i]['billing']);

                }else if($items_order_list[$i]['billing'] == "factura"){
                    $array_item_product[] = array('item_order_id' => $items_order_list[$i]['id'],'product_descr' => $items_order_list[$i]['product_descr'], 'price' => $items_order_list[$i]['price'],
                        'preci1' => $items_order_list[$i]['preci1'],'preci2' => $items_order_list[$i]['preci2'],'preci3' => $items_order_list[$i]['preci3'],'preci4' => $items_order_list[$i]['preci4'],'preci5' => $items_order_list[$i]['preci5'],
                        'quantity' => $items_order_list[$i]['quantity'],'loaded' => $items_order_list[$i]['loaded'],'reasigned_quantity' => $items_order_list[$i]['reasigned_quantity'],
                        'pendient_stock' => $items_order_list[$i]['pendient_stock'],'billing' => $items_order_list[$i]['billing']);
                }else{
                    $array_item_product_add[] = array('item_order_id' => $items_order_list[$i]['id'],'product_descr' => $items_order_list[$i]['product_descr'], 'price' => $items_order_list[$i]['price'],
                        'preci1' => $items_order_list[$i]['preci1'],'preci2' => $items_order_list[$i]['preci2'],'preci3' => $items_order_list[$i]['preci3'],'preci4' => $items_order_list[$i]['preci4'],'preci5' => $items_order_list[$i]['preci5'],
                        'quantity' => $items_order_list[$i]['quantity'],'loaded' => $items_order_list[$i]['loaded'],'reasigned_quantity' => $items_order_list[$i]['reasigned_quantity'],
                        'pendient_stock' => $items_order_list[$i]['pendient_stock'],'billing' => $items_order_list[$i]['billing']);
                }

                if($items_order_list[$i]['loaded'] == "true"){

                    $total_amount=$total_amount+($items_order_list[$i]['price']*$items_order_list[$i]['quantity']);
                }

            }

            $listReport[] = array('order_created' => $list_orders_by_deliver_date[$j]['created'],
                'order_obs' => $list_orders_by_deliver_date[$j]['observation'],'order_id' => $list_orders_by_deliver_date[$j]['order_id'],
                'order_state' => $list_orders_by_deliver_date[$j]['state'],
                'order_state_check' => $list_orders_by_deliver_date[$j]['state_check'],
                'order_state_prepare' => $list_orders_by_deliver_date[$j]['state_prepare'],
                'order_state_billing' => $list_orders_by_deliver_date[$j]['state_billing'],
                'order_state_delivery' => $list_orders_by_deliver_date[$j]['state_delivery'],
                'order_signed' => $list_orders_by_deliver_date[$j]['signed'],
                'order_paid_out' => $list_orders_by_deliver_date[$j]['paid_out'],
                'order_paid_amount' => $list_orders_by_deliver_date[$j]['paid_amount'],
                'client_id' => $list_orders_by_deliver_date[$j]['client_id'],
                'client_nomcli' => $list_orders_by_deliver_date[$j]['nomcli'],
                'client_dircli' =>$list_orders_by_deliver_date[$j]['dircli'],
                'client_loccli' => $list_orders_by_deliver_date[$j]['assigned_zone'],
                'client_comcli' => $list_orders_by_deliver_date[$j]['comcli'],
                'client_telcli' => $list_orders_by_deliver_date[$j]['telcli'],
                'delivery_date' => $list_orders_by_deliver_date[$j]['delivery_date'], 'items' => $array_item_product,'items_rem' => $array_item_product_rem,'items_add' => $array_item_product_add,
                'amount_order' => $total_amount,
                'loaded_in' => $list_orders_by_deliver_date[$j]['loaded_in'],
                'loaded_by' => $list_orders_by_deliver_date[$j]['loaded_by'],
                'prepared_by' => $list_orders_by_deliver_date[$j]['prepared_by'],
                'delivery_by' => $list_orders_by_deliver_date[$j]['delivery_by']
            );
        }

        return $listReport;
    }

    function getOrdersClient(){

        if(isset($_GET['delivery_date'])) {
            $listReport = array();

            $list_orders_by_deliver_date = $this->getModel()->getOrdersClient($this->asingFilters(),$this->getPaginator(),$this->state_order);

            $this->returnSuccess(200, $this->getReport($list_orders_by_deliver_date,$listReport));
        }else{
            $this->returnError(404,"ENTITY NOT FOUND");
        }
    }


    function deleteRemainingProducts($order_id){

        $filtersItem=array();
        $filtersItem[] = 'order_id = "' . $order_id . '"';
        $filtersItem[] = 'loaded = "false"';

        $items_order_list = $this->items_order->findAllItems($filtersItem);
        for ($i = 0; $i < count($items_order_list); ++$i) {
            $this->items_order->delete($items_order_list[$i]['id']);
        }
    }

    function createNewOrderWithReasigneditems($order_id){

        $order=$this->getModel()->findById($order_id);

        $items_order_list = $this->items_order->findAllItems(array('order_id = "' .$order_id.'"'));

        $next_date = date('Y-m-d', strtotime($order['delivery_date'].' +1 day'));

        $newOrder =array('user_id'=>1,'client_id' => $order['client_id'],
            'state' => "",
            'state_check' => "check",
            'state_prepare' => "toprepare",
            'state_billing' => "tobilling",
            'state_delivery' => "todelivery",
            'tocheck' => "true",
            'toprepare' => "true",
            'tobilling' => "false",
            'todelivery' => "false",
            'observation' => "",
            'total_amount' => 0.0,
            'delivery_date'=> $next_date,
            'loaded_by'=> $order['loaded_by'],
            'delivery_by' => "",
            'prepared_by' => "",
            'assigned_zone' => $order['assigned_zone'],
            'loaded_in' => $order['loaded_in'],
            'signed' => "false",
            'paid_out' => "false",
            'paid_amount' => 0.0,
            'order_reasigned_id' => -1);


        $res=$this->model->save($newOrder);

        if($res>= 0){
            //me guardo el id de la orden a la que van a ser reasignados los productos.

            $this->getModel()->update($order['id'],array('order_reasigned_id' => $res));

            for ($i = 0; $i < count($items_order_list); ++$i) {
                if($items_order_list[$i]['loaded'] == "false"){
                    //aca hay que duplicar este item a la nueva orden , porque sino se pirde.

                    $newItem=$items_order_list[$i];
                    $newItem['order_id']= $res;
                    $newItem['reasigned_quantity']= "false"; // lo pongo en false para que se pueda cargar en la orden nueva

                    unset($newItem['id']);

                    $resItem = $this->items_order->save($newItem);

                    $this->items_order->update($items_order_list[$i]['id'],array('reasigned_quantity' => "true"));

                }
            }
        }
    }


    function deleteOrderWithReasignedItems($order_id){

        $order=$this->model->findById($order_id);
        if($order['order_reasigned_id'] >= 0){

            $this->items_order->deleteAllByOrderId($order['order_reasigned_id']);

            $this->model->delete($order['order_reasigned_id']);

            $this->model->update($order_id,array('order_reasigned_id' => -1));


            $items_order_list = $this->items_order->findAllItems(array('order_id = "' .$order_id.'"'));
            for ($i = 0; $i < count($items_order_list); ++$i) {
                if($items_order_list[$i]['reasigned_quantity'] == "true"){

                    $this->items_order->update($items_order_list[$i]['id'],array('reasigned_quantity' => "false"));

                }
            }
        }


    }

    function checkFullOrder(){
        $res=$this->items_order->countItemsLoaded("false",$_GET['order_id']);
        if($res == 0){
            $resp=array('fullOrder'=>"completa",'cant' => $res);
            $this->returnSuccess(200,$resp);

        }else{

            $resp2=array('fullOrder'=>"falta",'cant' => $res);
            $this->returnSuccess(200,$resp2);
        }
    }

     function isFullOrderCharged(){

        $res=$this->items_order->countItemsLoaded("false",$_GET['order_id']);
        if($res == 0){
            return "completa";
        }else{
            return "falta";
        }
    }

    function changeStateOrder(){

        if(isset($_GET['state'])){
            if($_GET['state_name'] == 'tocheck'){

                $this->getModel()->update($_GET['order_id'],array('state_check' => $_GET['state']));
                if($_GET['state'] == "check"){
                    $this->getModel()->update($_GET['order_id'],array('toprepare' => "true"));
                }else{
                    $this->getModel()->update($_GET['order_id'],array('toprepare' => "false"));
                }

            }else if($_GET['state_name'] == 'toprepare'){

                $this->getModel()->update($_GET['order_id'],array('state_prepare' => $_GET['state']));
                if($_GET['state'] == "prepare"){

                    if($this->isFullOrderCharged() == "falta"){

                        $this->createNewOrderWithReasigneditems($_GET['order_id']);
                    }

                    $this->getModel()->update($_GET['order_id'],array('tobilling' => "true"));
                }else{

                    $this->deleteOrderWithReasignedItems($_GET['order_id']);

                    $this->getModel()->update($_GET['order_id'],array('tobilling' => "false"));
                }

            }else if($_GET['state_name'] == 'tobilling'){

                $this->getModel()->update($_GET['order_id'],array('state_billing' => $_GET['state']));
                if($_GET['state'] == "billing"){
                    $this->getModel()->update($_GET['order_id'],array('todelivery' => "true"));
                }else{
                    $this->getModel()->update($_GET['order_id'],array('todelivery' => "false"));
                }

            }else if($_GET['state_name'] == 'todelivery'){
                $this->getModel()->update($_GET['order_id'],array('state_delivery' => $_GET['state']));
            }

            $stateOrder=array('state'=>$_GET['state']);

            if(isset($_GET['prepared_by'])) {
                $this->getModel()->update($_GET['order_id'], array('prepared_by' => $_GET['prepared_by']));
            }

            if(isset($_GET['delivery_by'])) {
                $this->getModel()->update($_GET['order_id'], array('delivery_by' => $_GET['delivery_by']));
            }

            $this->returnSuccess(200,$stateOrder);
        }else{
            $this->returnError(400,"No se pudo actualizar");
        }
    }

    function getOrdersValues(){
        $checked=0;
        $pendients=0;

        if($_GET['state_name']== 'tocheck'){

            $checked=$this->getModel()->countCheck($_GET['delivery_date'],'check');
            $pendients=$this->getModel()->countCheck($_GET['delivery_date'],'tocheck');
        }else if ($_GET['state_name']== 'toprepare'){
            $checked=$this->getModel()->countPrepare($_GET['delivery_date'],'prepare');
            $pendients=$this->getModel()->countPrepare($_GET['delivery_date'],'toprepare');

        }else if ($_GET['state_name']== 'todelivery'){

            $checked=$this->getModel()->countDelivery($_GET['delivery_date'],'delivery');
            $pendients=$this->getModel()->countDelivery($_GET['delivery_date'],'todelivery');
        }else if ($_GET['state_name']== 'tobilling'){

            $checked=$this->getModel()->countBilling($_GET['delivery_date'],'billing');
            $pendients=$this->getModel()->countBilling($_GET['delivery_date'],'tobilling');
        }
        $resp=array('pendients' => $pendients, 'checked' => $checked);

        $this->returnSuccess(200,$resp);
    }

    function  updatePaymentValue(){

        if(isset($_GET['order_id']) ){

            $this->getModel()->update($_GET['order_id'],array('paid_amount' => $_GET['value']));
            $this->getModel()->update($_GET['order_id'],array('paid_out' => "true"));

            $paymentData=array('state'=>"value", 'total_amount' => $_GET['value']);

            $this->returnSuccess(200,$paymentData);
        }else{
            $this->returnError(400,"No se pudo actualizar");
        }
    }

    function updatePayment(){

        $select="";
        if(isset($_GET['order_id']) ){

            $total_amount=0;

            if($_GET['paid'] == 'false'){
                $this->getModel()->update($_GET['order_id'],array('paid_out' => "false"));

                $this->getModel()->update($_GET['order_id'],array('paid_amount' => 0));
                $this->getModel()->update($_GET['order_id'],array('total_amount' => 0));
                $total_amount=0;

                $select="false";
            }else if($_GET['paid'] == 'true'){

                $this->getModel()->update($_GET['order_id'],array('paid_out' => "true"));

                $total_amount=$this->getTotalAmountByOrderId($_GET['order_id']);

                $this->getModel()->update($_GET['order_id'],array('paid_amount' => $total_amount));
                $this->getModel()->update($_GET['order_id'],array('total_amount' => $total_amount));

                $select="true";
            }

            if($_GET['signed'] == 'false'){
                $this->getModel()->update($_GET['order_id'],array('signed' => "false"));
                $select="false";
            }else if($_GET['signed'] == 'true'){
                $this->getModel()->update($_GET['order_id'],array('signed' => "true"));
                $select="true";
            }

            $paymentData=array('state'=>$select, 'total_amount' => $total_amount);

            $this->returnSuccess(200,$paymentData);
        }else{
            $this->returnError(400,"No se pudo actualizar");
        }
    }


    function getTotalAmountByOrderId($order_id){
        $order= $this->getModel()->findById($order_id);

        if($order>0){

            $items_order_list = $this->items_order->findAllItems(array('order_id = "' .$order_id.'"'));
            $total_amount=0;
            for ($i = 0; $i < count($items_order_list); ++$i) {

                if($items_order_list[$i]['loaded'] == "true"){
                    $total_amount=$total_amount+($items_order_list[$i]['price']*$items_order_list[$i]['quantity']);
                }

               // $total_amount=$total_amount+($items_order_list[$i]['price']*$items_order_list[$i]['quantity']);

            }

            return $total_amount;
        }else{

            return 0;
        }
    }

//esta se usaba para la pantalla completa de la orden
    function getReportByOrderId(){
        if(isset($_GET['order_id'])){

            $order= $this->getModel()->findById($_GET['order_id']);
            if($order>0){

                $client= $this->clients->findById($order['client_id']);

                $items_order_list = $this->items_order->findAllItems(array('order_id = "' .$order['id'].'"'));

                $array_item_product = array();
                $array_item_product_rem = array();
                $array_item_product_add = array();
                $total_amount=0;
                for ($i = 0; $i < count($items_order_list); ++$i) {

                    if($items_order_list[$i]['billing'] == "remito"){
                        $array_item_product_rem[] = array('item_order_id' => $items_order_list[$i]['id'],'product_descr' => $items_order_list[$i]['product_descr'], 'price' => $items_order_list[$i]['price'],
                            'preci1' => $items_order_list[$i]['preci1'],'preci2' => $items_order_list[$i]['preci2'],'preci3' => $items_order_list[$i]['preci3'],'preci4' => $items_order_list[$i]['preci4'],'preci5' => $items_order_list[$i]['preci5'],
                            'quantity' => $items_order_list[$i]['quantity'],'loaded' => $items_order_list[$i]['loaded'],'reasigned_quantity' => $items_order_list[$i]['reasigned_quantity'],
                            'pendient_stock' => $items_order_list[$i]['pendient_stock'],'billing' => $items_order_list[$i]['billing']);

                    }else if($items_order_list[$i]['billing'] == "factura"){
                        $array_item_product[] = array('item_order_id' => $items_order_list[$i]['id'],'product_descr' => $items_order_list[$i]['product_descr'], 'price' => $items_order_list[$i]['price'],
                            'quantity' => $items_order_list[$i]['quantity'],'loaded' => $items_order_list[$i]['loaded'],'reasigned_quantity' => $items_order_list[$i]['reasigned_quantity'],
                            'pendient_stock' => $items_order_list[$i]['pendient_stock'],'billing' => $items_order_list[$i]['billing']);
                    }else{
                        $array_item_product_add[] = array('item_order_id' => $items_order_list[$i]['id'],'product_descr' => $items_order_list[$i]['product_descr'], 'price' => $items_order_list[$i]['price'],
                            'quantity' => $items_order_list[$i]['quantity'],'loaded' => $items_order_list[$i]['loaded'],'reasigned_quantity' => $items_order_list[$i]['reasigned_quantity'],
                            'pendient_stock' => $items_order_list[$i]['pendient_stock'],'billing' => $items_order_list[$i]['billing']);
                    }

                    if($items_order_list[$i]['loaded'] == "true"){
                        $total_amount=$total_amount+($items_order_list[$i]['price']*$items_order_list[$i]['quantity']);
                    }

                   // $total_amount=$total_amount+($items_order_list[$i]['price']*$items_order_list[$i]['quantity']);
                }

                $listReport = array('order_created' =>$order['created'],
                    'order_obs' => $order['observation'],'order_id' => $order['id'],
                    'order_state' => $order['state'],
                    'order_state_check' =>$order['state_check'],
                    'order_state_prepare' =>$order['state_prepare'],
                    'order_state_billing' =>$order['state_billing'],
                    'order_state_delivery' => $order['state_delivery'],
                    'order_signed' => $order['signed'],
                    'order_paid_out' => $order['paid_out'],
                    'order_paid_amount' => $order['paid_amount'],
                    'client_id' => $order['client_id'],
                    'client_nomcli' => $client['nomcli'],
                    'client_dircli' => $client['dircli'],
                    'client_loccli' => $order['assigned_zone'],
                    'client_comcli' => $client['comcli'],
                    'delivery_date' => $order['delivery_date'], 'items' => $array_item_product,'items_rem' => $array_item_product_rem,'items_add' => $array_item_product_add,
                    'amount_order' => $total_amount,
                    'loaded_in' => $order['loaded_in'],
                    'loaded_by' => $order['loaded_by'],
                    'prepared_by' => $order['prepared_by'],
                    'delivery_by' => $order['delivery_by']);

                $this->returnSuccess(200, $listReport);
            }else{
                $this->returnError(404,"ENTITY NOT FOUND");
            }
        }
    }


}


/*
    public function getFiltersContainCom()
    {
        $filters = $this->asingFilters();
        if(isset($_GET['query']) && !empty($_GET['query'])){
            $filters[] = 'comcli like "%'.$_GET['query'].'%"';
        }
        return $filters;
    }

    public function getFiltersFirstLetterCom()
    {
        $filters =  $filters = $this->asingFilters();
        if(isset($_GET['query']) && !empty($_GET['query'])){
            $filters[] = 'comcli like "'.$_GET['query'].'%"';
        }
        return $filters;
    }

    public function getFiltersContainNomb()
    {
        $filters = $this->asingFilters();
        if(isset($_GET['query']) && !empty($_GET['query'])){
            $filters[] = 'nomcli like "%'.$_GET['query'].'%"';
        }
        return $filters;
    }

    public function getFiltersFirstLetterNom()
    {
        $filters =  $filters = $this->asingFilters();
        if(isset($_GET['query']) && !empty($_GET['query'])){
            $filters[] = 'nomcli like "'.$_GET['query'].'%"';
        }
        return $filters;
    }

    function getOrderList(){
        $orders_contain_com = $this->getModel()->getOrdersClient($this->getFiltersContainCom(),$this->getPaginator(),$this->state_order);
        $orders_firstletter_com = $this->getModel()->getOrdersClient($this->getFiltersFirstLetterCom(),$this->getPaginator(),$this->state_order);
        $orders_firstletter_nom = $this->getModel()->getOrdersClient($this->getFiltersFirstLetterNom(),$this->getPaginator(),$this->state_order);
        $orders_contain_nom = $this->getModel()->getOrdersClient($this->getFiltersContainNomb(),$this->getPaginator(),$this->state_order);

        return $orders_contain_com+$orders_firstletter_com+$orders_firstletter_nom+$orders_contain_nom;
    }

 function listOrdersByDeliveryDate(){
        if(isset($_GET['delivery_date'])) {
            $listReport = array();

            $list_orders_by_deliver_date = $this->getModel()->findAllOrder($this->asingFilters(),$this->getPaginator(),$this->state_order);

            for ($j = 0; $j < count($list_orders_by_deliver_date); ++$j) {

                $client= $this->clients->findById($list_orders_by_deliver_date[$j]['client_id']);

                $items_order_list = $this->items_order->findAllItems(array('order_id = "' .$list_orders_by_deliver_date[$j]['id'].'"'));

                $array_item_product = array();
                $total_amount=0;
                for ($i = 0; $i < count($items_order_list); ++$i) {

                    $array_item_product[] = array('item_order_id' => $items_order_list[$i]['id'],'product_descr' => $items_order_list[$i]['product_descr'], 'price' => $items_order_list[$i]['price'],
                        'quantity' => $items_order_list[$i]['quantity'],'loaded' => $items_order_list[$i]['loaded'],'reasigned_quantity' => $items_order_list[$i]['reasigned_quantity']);

                    $total_amount=$total_amount+($items_order_list[$i]['price']*$items_order_list[$i]['quantity']);

                }

                $listReport[] = array('order_created' => $list_orders_by_deliver_date[$j]['created'],
                    'order_obs' => $list_orders_by_deliver_date[$j]['observation'],'order_id' => $list_orders_by_deliver_date[$j]['id'],
                    'order_state' => $list_orders_by_deliver_date[$j]['state'],
                    'order_state_check' => $list_orders_by_deliver_date[$j]['state_check'],
                    'order_state_prepare' => $list_orders_by_deliver_date[$j]['state_prepare'],
                    'order_state_billing' => $list_orders_by_deliver_date[$j]['state_billing'],
                    'order_state_delivery' => $list_orders_by_deliver_date[$j]['state_delivery'],
                    'order_signed' => $list_orders_by_deliver_date[$j]['signed'],
                    'order_paid_out' => $list_orders_by_deliver_date[$j]['paid_out'],
                    'order_paid_amount' => $list_orders_by_deliver_date[$j]['paid_amount'],
                    'client_id' => $list_orders_by_deliver_date[$j]['client_id'],
                    'client_nomcli' => $client['nomcli'],
                    'client_dircli' => $client['dircli'],
                    'client_loccli' => $list_orders_by_deliver_date[$j]['assigned_zone'],
                    'client_comcli' => $client['comcli'],
                    'delivery_date' => $list_orders_by_deliver_date[$j]['delivery_date'], 'items' => $array_item_product,
                    'amount_order' => $total_amount,
                    'loaded_in' => $list_orders_by_deliver_date[$j]['loaded_in'],
                    'loaded_by' => $list_orders_by_deliver_date[$j]['loaded_by'],
                    'prepared_by' => $list_orders_by_deliver_date[$j]['prepared_by'],
                    'delivery_by' => $list_orders_by_deliver_date[$j]['delivery_by']
                   );
            }

            $this->returnSuccess(200, $listReport);
        }else{
            $this->returnError(404,"ENTITY NOT FOUND");
        }
    }
*/
//  $list_orders_by_deliver_date = $this->getModel()->getOrderList();

/*
for ($j = 0; $j < count($list_orders_by_deliver_date); ++$j) {

    $items_order_list = $this->items_order->findAllItems(array('order_id = "' .$list_orders_by_deliver_date[$j]['order_id'].'"'));

    $array_item_product = array();
    $total_amount=0;
    for ($i = 0; $i < count($items_order_list); ++$i) {

        $array_item_product[] = array('item_order_id' => $items_order_list[$i]['id'],'product_descr' => $items_order_list[$i]['product_descr'], 'price' => $items_order_list[$i]['price'],
            'preci1' => $items_order_list[$i]['preci1'],'preci2' => $items_order_list[$i]['preci2'],'preci3' => $items_order_list[$i]['preci3'],'preci4' => $items_order_list[$i]['preci4'],'preci5' => $items_order_list[$i]['preci5'],
            'quantity' => $items_order_list[$i]['quantity'],'loaded' => $items_order_list[$i]['loaded'],'reasigned_quantity' => $items_order_list[$i]['reasigned_quantity'],
 'pendient_stock' => $items_order_list[$i]['pendient_stock']);

        $total_amount=$total_amount+($items_order_list[$i]['price']*$items_order_list[$i]['quantity']);

    }

    $listReport[] = array('order_created' => $list_orders_by_deliver_date[$j]['created'],
        'order_obs' => $list_orders_by_deliver_date[$j]['observation'],'order_id' => $list_orders_by_deliver_date[$j]['order_id'],
        'order_state' => $list_orders_by_deliver_date[$j]['state'],
        'order_state_check' => $list_orders_by_deliver_date[$j]['state_check'],
        'order_state_prepare' => $list_orders_by_deliver_date[$j]['state_prepare'],
        'order_state_billing' => $list_orders_by_deliver_date[$j]['state_billing'],
        'order_state_delivery' => $list_orders_by_deliver_date[$j]['state_delivery'],
        'order_signed' => $list_orders_by_deliver_date[$j]['signed'],
        'order_paid_out' => $list_orders_by_deliver_date[$j]['paid_out'],
        'order_paid_amount' => $list_orders_by_deliver_date[$j]['paid_amount'],
        'client_id' => $list_orders_by_deliver_date[$j]['client_id'],
        'client_nomcli' => $list_orders_by_deliver_date[$j]['nomcli'],
        'client_dircli' =>$list_orders_by_deliver_date[$j]['dircli'],
        'client_loccli' => $list_orders_by_deliver_date[$j]['assigned_zone'],
        'client_comcli' => $list_orders_by_deliver_date[$j]['comcli'],
        'client_telcli' => $list_orders_by_deliver_date[$j]['telcli'],
        'delivery_date' => $list_orders_by_deliver_date[$j]['delivery_date'], 'items' => $array_item_product,
        'amount_order' => $total_amount,
        'loaded_in' => $list_orders_by_deliver_date[$j]['loaded_in'],
        'loaded_by' => $list_orders_by_deliver_date[$j]['loaded_by'],
        'prepared_by' => $list_orders_by_deliver_date[$j]['prepared_by'],
        'delivery_by' => $list_orders_by_deliver_date[$j]['delivery_by']
    );
}
*/

/*
            for ($j = 0; $j < count($list_orders_by_deliver_date); ++$j) {

                $items_order_list = $this->items_order->findAllItems(array('order_id = "' .$list_orders_by_deliver_date[$j]['order_id'].'"'));

                $array_item_product = array();
                $total_amount=0;
                for ($i = 0; $i < count($items_order_list); ++$i) {

                    $array_item_product[] = array('item_order_id' => $items_order_list[$i]['id'],'product_descr' => $items_order_list[$i]['product_descr'], 'price' => $items_order_list[$i]['price'],
                        'preci1' => $items_order_list[$i]['preci1'],'preci2' => $items_order_list[$i]['preci2'],'preci3' => $items_order_list[$i]['preci3'],'preci4' => $items_order_list[$i]['preci4'],'preci5' => $items_order_list[$i]['preci5'],
                        'quantity' => $items_order_list[$i]['quantity'],'loaded' => $items_order_list[$i]['loaded'],'reasigned_quantity' => $items_order_list[$i]['reasigned_quantity']);

                    $total_amount=$total_amount+($items_order_list[$i]['price']*$items_order_list[$i]['quantity']);

                }

                $listReport[] = array('order_created' => $list_orders_by_deliver_date[$j]['created'],
                    'order_obs' => $list_orders_by_deliver_date[$j]['observation'],'order_id' => $list_orders_by_deliver_date[$j]['order_id'],
                    'order_state' => $list_orders_by_deliver_date[$j]['state'],
                    'order_state_check' => $list_orders_by_deliver_date[$j]['state_check'],
                    'order_state_prepare' => $list_orders_by_deliver_date[$j]['state_prepare'],
                    'order_state_billing' => $list_orders_by_deliver_date[$j]['state_billing'],
                    'order_state_delivery' => $list_orders_by_deliver_date[$j]['state_delivery'],
                    'order_signed' => $list_orders_by_deliver_date[$j]['signed'],
                    'order_paid_out' => $list_orders_by_deliver_date[$j]['paid_out'],
                    'order_paid_amount' => $list_orders_by_deliver_date[$j]['paid_amount'],
                    'client_id' => $list_orders_by_deliver_date[$j]['client_id'],
                    'client_nomcli' => $list_orders_by_deliver_date[$j]['nomcli'],
                    'client_dircli' =>$list_orders_by_deliver_date[$j]['dircli'],
                    'client_loccli' => $list_orders_by_deliver_date[$j]['assigned_zone'],
                    'client_comcli' => $list_orders_by_deliver_date[$j]['comcli'],
                    'client_telcli' => $list_orders_by_deliver_date[$j]['telcli'],
                    'delivery_date' => $list_orders_by_deliver_date[$j]['delivery_date'], 'items' => $array_item_product,
                    'amount_order' => $total_amount,
                    'loaded_in' => $list_orders_by_deliver_date[$j]['loaded_in'],
                    'loaded_by' => $list_orders_by_deliver_date[$j]['loaded_by'],
                    'prepared_by' => $list_orders_by_deliver_date[$j]['prepared_by'],
                    'delivery_by' => $list_orders_by_deliver_date[$j]['delivery_by']
                );
            }
*/

