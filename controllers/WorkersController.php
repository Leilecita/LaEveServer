<?php
/**
 * Created by PhpStorm.
 * User: leila
 * Date: 26/07/2019
 * Time: 13:54
 */


require_once 'BaseController.php';
require_once __DIR__.'/SecureBaseController.php';
require_once __DIR__.'/../models/WorkerModel.php';
require_once __DIR__.'/../models/OrderModel.php';
require_once __DIR__.'/../models/ItemOrderModel.php';
require_once __DIR__.'/../models/ClientModel.php';
require_once __DIR__.'/../models/AssignedZoneModel.php';
class WorkersController extends SecureBaseController
{

    private $orders;
    private $items_order;
    private $clients;
    private $assigned_zones;

    function __construct(){
        parent::__construct();
        $this->model = new WorkerModel();
        $this->orders=new OrderModel();
        $this->items_order=new ItemOrderModel();
        $this->clients=new ClientModel();
        $this->assigned_zones=new AssignedZoneModel();
    }

    function getWorkersOld(){


        $workers = $this->model->getUsersJoinWorkers($this->getFilters(), $this->getPaginator());
        $reportWorker = array();
        for ($j = 0; $j < count($workers); ++$j) {

            $reportWorker[] = array('name' => $workers[$j]['worker_name'],'load_worker' => $workers[$j]['load_worker'],
                'prepare_worker' => $workers[$j]['prepare_worker'],
                'delivery_worker' => $workers[$j]['delivery_worker'],
                'bill_worker' => $workers[$j]['bill_worker'],
                'category' => $workers[$j]['category'],
                'zone' => $workers[$j]['zone'],
                'worker_id' => $workers[$j]['worker_id'],
                'user_id' => $workers[$j]['user_id'],
                );
        }

        $this->returnSuccess(200, $reportWorker);
    }

    function getWorkers(){

        $workers = $this->model->getUsersJoinWorkers($this->getFilters(), $this->getPaginator());
        $reportWorker = array();
        for ($j = 0; $j < count($workers); ++$j) {

            $assigned_zones = $this->assigned_zones->findAssignedZones(array('a.user_id = "'.$workers[$j]['user_id'].'"'));

            $reportWorker[] = array('name' => $workers[$j]['worker_name'],'load_worker' => $workers[$j]['load_worker'],
                'prepare_worker' => $workers[$j]['prepare_worker'],
                'delivery_worker' => $workers[$j]['delivery_worker'],
                'bill_worker' => $workers[$j]['bill_worker'],
                'category' => $workers[$j]['category'],
                'zone' => $workers[$j]['zone'],
                'worker_id' => $workers[$j]['worker_id'],
                'user_id' => $workers[$j]['user_id'],
                'assigned_zones' => $assigned_zones,
            );
        }

        $this->returnSuccess(200, $reportWorker);
    }

    function getWorkersBy(){

        $res=array();

        if($_GET['type'] == "load"){
            $res=$this->model->findAllWorkers(array('load_worker = "true"'));
        }else if($_GET['type'] == "prepare"){
            $res=$this->model->findAllWorkers(array('prepare_worker = "true"'));
        }else if($_GET['type'] == "delivery"){
            $res=$this->model->findAllWorkers(array('delivery_worker = "true"'));
        }else if($_GET['type'] == "bill"){
            $res=$this->model->findAllWorkers(array('bill_worker = "true"'));
        }

        $this->returnSuccess(200,$res);
    }




    function getWorkerLiquidation(){

        if(isset($_GET['worker_name']) && isset($_GET['delivery_date'])){

            $total_amount=$this->orders->sumTotalAmount($_GET['delivery_date'],$_GET['worker_name'],"delivery");

            $total_paid_amount=$this->orders->sumPaidAmountByWorker($_GET['delivery_date'],$_GET['worker_name'],"delivery");

            $delivery_orders_quantity= $this->orders->countDeliveryOrders($_GET['delivery_date'],$_GET['worker_name'],"delivery");

            $pendients_orders_quantity= $this->orders->countDeliveryOrders($_GET['delivery_date'],$_GET['worker_name'],"todelivery");

            $reportWorkerLiquidation=array('total_amount' => $total_amount,'total_paid_amount' => $total_paid_amount,
                'quantity_delivery_orders' => $delivery_orders_quantity,'quantity_delivery_pendients' => $pendients_orders_quantity, 'load_orders_quantity' => 0
                );

            $this->returnSuccess(200, $reportWorkerLiquidation);

        }else{
            $this->returnError(404,"ENTITY NOT FOUND");
        }
    }

    function getLoadWorkerLiquidation(){

        if(isset($_GET['worker_name']) && isset($_GET['delivery_date'])){

           // $loaded_orders= $this->orders->countLoadOrders($_GET['delivery_date'],$_GET['worker_name'],"delivery",$_GET['loaded_in']);

            $loaded_orders_m= $this->orders->countLoadOrders($_GET['delivery_date'],$_GET['worker_name'],"delivery","Mostrador");
            $loaded_orders_v= $this->orders->countLoadOrders($_GET['delivery_date'],$_GET['worker_name'],"delivery","Vendedor");
            $loaded_orders_w= $this->orders->countLoadOrders($_GET['delivery_date'],$_GET['worker_name'],"delivery","Whatsapp");
            $loaded_orders_t= $this->orders->countLoadOrders($_GET['delivery_date'],$_GET['worker_name'],"delivery","Telefono");

            $total_load_orders= $this->orders->countTotalLoadOrders($_GET['delivery_date'],$_GET['worker_name'],"delivery");

            $reportWorkerLiquidation=array('order_w' => $loaded_orders_w,'order_m' => $loaded_orders_m,
                'order_t' => $loaded_orders_t,'order_v' => $loaded_orders_v, 'total_orders' => $total_load_orders
            );

            $this->returnSuccess(200, $reportWorkerLiquidation);

        }else{
            $this->returnError(404,"ENTITY NOT FOUND");
        }
    }
}

/*
 *
    public function filterDeliveryWorkers($worker_name,$delivery_date)
    {
        $filters = array();

        $filters[] = 'delivery_by = "' . $worker_name . '"';
        $filters[] = 'state_delivery = "delivery"';
        $filters[] = 'delivery_date = "' .$delivery_date.'"';

        return $filters;
    }

    public function filterLoadWorkers($worker_name,$delivery_date,$loaded_in)
    {
        $filters = array();

        $filters[] = 'loaded_by = "' . $worker_name . '"';
        if($loaded_in != ""){
            $filters[] = 'loaded_in = "' . $loaded_in . '"';
        }
        $filters[] = 'state_delivery = "delivery"';
        $filters[] = 'delivery_date = "' .$delivery_date.'"';

        return $filters;
    }

 function getWorkerLoadLiquidationList(){

        if(isset($_GET['worker_name']) && isset($_GET['delivery_date'])){

            $listReport=array();
            $list_orders_by_deliver_date=$this->orders->findAll($this->filterLoadWorkers($_GET['worker_name'],$_GET['delivery_date'],$_GET['loaded_in']),$this->getPaginator());
            for ($j = 0; $j < count($list_orders_by_deliver_date); ++$j) {

                $client= $this->clients->findById($list_orders_by_deliver_date[$j]['client_id']);
                $items_order_list = $this->items_order->findAllItems(array('order_id = "' .$list_orders_by_deliver_date[$j]['id'].'"'));

                $array_item_product = array();

                $array_item_product_rem = array();

                $array_item_product_add = array();

                $total_amount=0;
                for ($i = 0; $i < count($items_order_list); ++$i) {


                    if($items_order_list[$i]['billing'] == "remito"){
                        $array_item_product_rem[] = $this->createReportItem($items_order_list[$i],$array_item_product_rem);
                    }else if($items_order_list[$i]['billing'] == "factura"){
                        $array_item_product[] =  $array_item_product[] = $this->createReportItem($items_order_list[$i],$array_item_product);
                    }else{
                        $array_item_product_add[] = $this->createReportItem($items_order_list[$i],$array_item_product_add);
                    }

                    if($items_order_list[$i]['loaded'] == "true") {
                        $total_amount = $total_amount + ($items_order_list[$i]['price'] * $items_order_list[$i]['quantity']);
                    }
                }

                $items_cant = $this->items_order->countItemsByOrder($list_orders_by_deliver_date[$j]['order_id']);
                $pendient_items = $this->items_order->countPendientItems("false" ,$list_orders_by_deliver_date[$j]['order_id']);



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
                    'client_nomcli' => $client['nomcli'],
                    'client_dircli' =>$client['dircli'],
                    'client_loccli' => $client['loccli'],
                    'assigned_zone' => $list_orders_by_deliver_date[$j]['assigned_zone'],
                    'client_comcli' => $client['comcli'],
                    'client_telcli' => $client['telcli'],
                    'delivery_date' => $list_orders_by_deliver_date[$j]['delivery_date'], 'items' => $array_item_product,'items_rem' => $array_item_product_rem,'items_add' => $array_item_product_add,
                    'amount_order' => $total_amount,
                    'loaded_in' => $list_orders_by_deliver_date[$j]['loaded_in'],
                    'loaded_by' => $list_orders_by_deliver_date[$j]['loaded_by'],
                    'prepared_by' => $list_orders_by_deliver_date[$j]['prepared_by'],
                    'billed_by' => $list_orders_by_deliver_date[$j]['billed_by'],
                    'delivery_by' => $list_orders_by_deliver_date[$j]['delivery_by'],
                    'products_cant' => $items_cant,
                    'pendients_cant' => $pendient_items
                );
            }

            $this->returnSuccess(200, $listReport);
        }else{
            $this->returnError(404,"");
        }


    }

    function createReportItem($items_order_list,$array_item){

        $array_item = array('item_order_id' => $items_order_list['id'],'product_descr' => $items_order_list['product_descr'], 'price' => $items_order_list['price'],
            'preci1' => $items_order_list['preci1'],'preci2' => $items_order_list['preci2'],'preci3' => $items_order_list['preci3'],'preci4' => $items_order_list['preci4'],'preci5' => $items_order_list['preci5'],
            'quantity' => $items_order_list['quantity'],'loaded' => $items_order_list['loaded'],'reasigned_quantity' => $items_order_list['reasigned_quantity'],
            'pendient_stock' => $items_order_list['pendient_stock'],'billing' => $items_order_list['billing'],
            'observation' =>  $items_order_list['observation'],
            'kg' => $items_order_list['kg'],
            'able_kg' => $items_order_list['able_kg'],
            'product_code' => $items_order_list['product_code'],
            'able_text' => $items_order_list['able_text']
        );

        return $array_item;

    }
    function getWorkerLiquidationList(){

        if(isset($_GET['worker_name']) && isset($_GET['delivery_date'])){

            $listReport=array();
            $list_orders_by_deliver_date=$this->orders->findAll($this->filterDeliveryWorkers($_GET['worker_name'],$_GET['delivery_date']),$this->getPaginator());
            for ($j = 0; $j < count($list_orders_by_deliver_date); ++$j) {

                $client= $this->clients->findById($list_orders_by_deliver_date[$j]['client_id']);
                $items_order_list = $this->items_order->findAllItems(array('order_id = "' .$list_orders_by_deliver_date[$j]['id'].'"'));

                $array_item_product = array();

                $array_item_product_rem = array();
                $array_item_product_add = array();

                $total_amount=0;
                for ($i = 0; $i < count($items_order_list); ++$i) {

                    if($items_order_list[$i]['billing'] == "remito"){
                        $array_item_product_rem[] = $this->createReportItem($items_order_list[$i],$array_item_product_rem);
                    }else if($items_order_list[$i]['billing'] == "factura"){
                        $array_item_product[] =  $array_item_product[] = $this->createReportItem($items_order_list[$i],$array_item_product);
                    }else{
                        $array_item_product_add[] = $this->createReportItem($items_order_list[$i],$array_item_product_add);
                    }


                    if($items_order_list[$i]['loaded'] == "true") {
                        $total_amount = $total_amount + ($items_order_list[$i]['price'] * $items_order_list[$i]['quantity']);
                    }
                }

                $items_cant = $this->items_order->countItemsByOrder($list_orders_by_deliver_date[$j]['order_id']);
                $pendient_items = $this->items_order->countPendientItems("false" ,$list_orders_by_deliver_date[$j]['order_id']);


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
                    'client_nomcli' => $client['nomcli'],
                    'client_dircli' =>$client['dircli'],
                    'client_loccli' => $client['loccli'],
                    'assigned_zone' => $list_orders_by_deliver_date[$j]['assigned_zone'],
                    'client_comcli' => $client['comcli'],
                    'client_telcli' => $client['telcli'],
                    'delivery_date' => $list_orders_by_deliver_date[$j]['delivery_date'], 'items' => $array_item_product,'items_rem' => $array_item_product_rem,'items_add' => $array_item_product_add,
                    'amount_order' => $total_amount,
                    'loaded_in' => $list_orders_by_deliver_date[$j]['loaded_in'],
                    'loaded_by' => $list_orders_by_deliver_date[$j]['loaded_by'],
                    'prepared_by' => $list_orders_by_deliver_date[$j]['prepared_by'],
                    'billed_by' => $list_orders_by_deliver_date[$j]['billed_by'],
                    'delivery_by' => $list_orders_by_deliver_date[$j]['delivery_by'],
                    'products_cant' => $items_cant,
                    'pendients_cant' => $pendient_items
                );
            }

            $this->returnSuccess(200, $listReport);
        }else{
            $this->returnError(404,"");
        }


    }
 */