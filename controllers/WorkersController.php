<?php
/**
 * Created by PhpStorm.
 * User: leila
 * Date: 26/07/2019
 * Time: 13:54
 */


require_once 'BaseController.php';
require_once __DIR__.'/../models/WorkerModel.php';
require_once __DIR__.'/../models/OrderModel.php';
require_once __DIR__.'/../models/ItemOrderModel.php';
require_once __DIR__.'/../models/ClientModel.php';
class WorkersController extends BaseController
{

    private $orders;
    private $items_order;
    private $clients;

    function __construct(){
        parent::__construct();
        $this->model = new WorkerModel();
        $this->orders=new OrderModel();
        $this->items_order=new ItemOrderModel();
        $this->clients=new ClientModel();
    }

    function getWorkersBy(){

        $res=array();

        if($_GET['type'] == "load"){
            $res=$this->model->findAllWorkers(array('load_worker = "true"'));
        }else if($_GET['type'] == "prepare"){
            $res=$this->model->findAllWorkers(array('prepare_worker = "true"'));
        }else if($_GET['type'] == "delivery"){
            $res=$this->model->findAllWorkers(array('delivery_worker = "true"'));
        }


        $this->returnSuccess(200,$res);

    }


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
                        $array_item_product_rem[] = array('item_order_id' => $items_order_list[$i]['id'],'product_descr' => $items_order_list[$i]['product_descr'], 'price' => $items_order_list[$i]['price'],
                            'preci1' => $items_order_list[$i]['preci1'],'preci2' => $items_order_list[$i]['preci2'],'preci3' => $items_order_list[$i]['preci3'],'preci4' => $items_order_list[$i]['preci4'],'preci5' => $items_order_list[$i]['preci5'],
                            'quantity' => $items_order_list[$i]['quantity'],'loaded' => $items_order_list[$i]['loaded'],'reasigned_quantity' => $items_order_list[$i]['reasigned_quantity'],
                            'pendient_stock' => $items_order_list[$i]['pendient_stock'],'billing' => $items_order_list[$i]['billing']);
                    }else if($items_order_list[$i]['billing'] == "factura" ){
                        $array_item_product[] = array('item_order_id' => $items_order_list[$i]['id'],'product_descr' => $items_order_list[$i]['product_descr'], 'price' => $items_order_list[$i]['price'],
                            'quantity' => $items_order_list[$i]['quantity'],'loaded' => $items_order_list[$i]['loaded'],'reasigned_quantity' => $items_order_list[$i]['reasigned_quantity'],
                            'pendient_stock' => $items_order_list[$i]['pendient_stock'],'billing' => $items_order_list[$i]['billing']);
                    }else{
                        $array_item_product_add[] = array('item_order_id' => $items_order_list[$i]['id'],'product_descr' => $items_order_list[$i]['product_descr'], 'price' => $items_order_list[$i]['price'],
                            'preci1' => $items_order_list[$i]['preci1'],'preci2' => $items_order_list[$i]['preci2'],'preci3' => $items_order_list[$i]['preci3'],'preci4' => $items_order_list[$i]['preci4'],'preci5' => $items_order_list[$i]['preci5'],
                            'quantity' => $items_order_list[$i]['quantity'],'loaded' => $items_order_list[$i]['loaded'],'reasigned_quantity' => $items_order_list[$i]['reasigned_quantity'],
                            'pendient_stock' => $items_order_list[$i]['pendient_stock'],'billing' => $items_order_list[$i]['billing']);
                    }
                    if($items_order_list[$i]['loaded'] == "true") {
                        $total_amount = $total_amount + ($items_order_list[$i]['price'] * $items_order_list[$i]['quantity']);
                    }

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
                    'delivery_date' => $list_orders_by_deliver_date[$j]['delivery_date'], 'items' => $array_item_product,'items_rem' => $array_item_product_rem,'items_add' => $array_item_product_add,
                    'amount_order' => $total_amount,
                    'loaded_in' => $list_orders_by_deliver_date[$j]['loaded_in'],
                    'loaded_by' => $list_orders_by_deliver_date[$j]['loaded_by'],
                    'prepared_by' => $list_orders_by_deliver_date[$j]['prepared_by'],
                    'delivery_by' => $list_orders_by_deliver_date[$j]['delivery_by']
                );
            }

            $this->returnSuccess(200, $listReport);
        }else{
            $this->returnError(404,"");
        }


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
                            'preci1' => $items_order_list[$i]['preci1'],'preci2' => $items_order_list[$i]['preci2'],'preci3' => $items_order_list[$i]['preci3'],'preci4' => $items_order_list[$i]['preci4'],'preci5' => $items_order_list[$i]['preci5'],
                            'quantity' => $items_order_list[$i]['quantity'],'loaded' => $items_order_list[$i]['loaded'],'reasigned_quantity' => $items_order_list[$i]['reasigned_quantity'],
                            'pendient_stock' => $items_order_list[$i]['pendient_stock'],'billing' => $items_order_list[$i]['billing']);
                    }


                    if($items_order_list[$i]['loaded'] == "true") {
                        $total_amount = $total_amount + ($items_order_list[$i]['price'] * $items_order_list[$i]['quantity']);
                    }

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
                    'delivery_date' => $list_orders_by_deliver_date[$j]['delivery_date'], 'items' => $array_item_product,'items_rem' => $array_item_product_rem,'items_add' => $array_item_product_add,
                    'amount_order' => $total_amount,
                    'loaded_in' => $list_orders_by_deliver_date[$j]['loaded_in'],
                    'loaded_by' => $list_orders_by_deliver_date[$j]['loaded_by'],
                    'prepared_by' => $list_orders_by_deliver_date[$j]['prepared_by'],
                    'delivery_by' => $list_orders_by_deliver_date[$j]['delivery_by']
                );
            }

            $this->returnSuccess(200, $listReport);
        }else{
            $this->returnError(404,"");
        }


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