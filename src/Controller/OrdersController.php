<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * Orders Controller
 *
 * @property \App\Model\Table\OrdersTable $Orders
 *
 * @method \App\Model\Entity\Order[] paginate($object = null, array $settings = [])
 */
class OrdersController extends AppController
{

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
	 
	public function dashboard()
    {
		$this->viewBuilder()->layout('index_layout');
		$curent_date=date('Y-m-d');
		$query = $this->Orders->find();
		
		$totalOrder=$query
		->select([
		'count' => $query->func()->count('id'),
		'total_amount' => $query->func()->sum('Orders.grand_total')])
		->where(['Orders.delivery_date' => $curent_date])->first();
		
		$this->set(compact('totalOrder'));
		
		$query = $this->Orders->find();
		$inProcessOrder=$query->select([
		'count' => $query->func()->count('id'),
		'total_amount' => $query->func()->sum('Orders.grand_total')])
		->where(['Orders.delivery_date' => $curent_date, 'Orders.status' => 'In Process'])->first();
		$this->set(compact('inProcessOrder'));
		
		$this->loadModel('WalkinSales');
		$query = $this->WalkinSales->find();
		$walkinsales=$query->select([
		'count' => $query->func()->count('id'),
		'total_amount' => $query->func()->sum('total_amount')]) 
		->where(['WalkinSales.transaction_date' => $curent_date])->first();
		$this->set(compact('walkinsales'));
		
		$query = $this->Orders->find();
		$deliveredOrder=$query->select([
		'count' => $query->func()->count('id'),
		'total_amount' => $query->func()->sum('Orders.grand_total')])
		->where(['Orders.delivery_date' => $curent_date, 'Orders.status' => 'Delivered'])->first();
		$this->set(compact('deliveredOrder'));
		
		
		$query = $this->Orders->find();
		$cancelOrder=$query->select([
		'count' => $query->func()->count('id'),
		'total_amount' => $query->func()->sum('Orders.grand_total')])
		->where(['Orders.delivery_date' => $curent_date, 'Orders.status' => 'Cancel'])->first();
		$this->set(compact('cancelOrder'));
		
		$query = $this->Orders->find();
		$bulkOrder=$query->select([
		'count' => $query->func()->count('id'),
		'total_amount' => $query->func()->sum('Orders.grand_total')])
		->where(['Orders.delivery_date' => $curent_date, 'Orders.order_type' => 'Bulkorder', 'Orders.status' => 'In Process'])->first();
		$this->set(compact('bulkOrder'));
		$curent_date=date('Y-m-d');
		$orders = $this->Orders->find('all')->order(['Orders.id'=>'DESC'])->where(['curent_date'=>$curent_date, 'Orders.status'=>'In process'])->contain(['Customers']);
		$this->set(compact('orders'));
        $this->set('_serialize', ['orders']);
    }
	
    public function index()
    {
		$this->viewBuilder()->layout('index_layout');
		$jain_thela_admin_id=$this->Auth->User('jain_thela_admin_id');
		$curent_date=date('Y-m-d');
		
		$this->paginate = [
            'contain' => ['Customers']
        ];
        $orders = $this->paginate($this->Orders->find('all')
		->order(['Orders.id'=>'DESC'])
		->where(['jain_thela_admin_id'=>$jain_thela_admin_id])
		->contain(['CustomerAddresses']));
		
        $this->set(compact('orders'));
        $this->set('_serialize', ['orders']);
    }

	public function manageOrder()
    {
		$this->viewBuilder()->layout('index_layout');
		$jain_thela_admin_id=$this->Auth->User('jain_thela_admin_id');
		$curent_date=date('Y-m-d');
		$orders = $this->Orders->find('all')->order(['Orders.id'=>'DESC'])->where(['jain_thela_admin_id'=>$jain_thela_admin_id, 'curent_date'=>$curent_date, 'Orders.status'=>'In process'])->contain(['Customers']);
		
        $this->set(compact('orders'));
        $this->set('_serialize', ['orders']);
    }
	
    /**
     * View method
     *
     * @param string|null $id Order id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
		$this->viewBuilder()->layout('');
        $order = $this->Orders->get($id, [
            'contain' => ['Customers', 'PromoCodes', 'OrderDetails'=>['Items'=>['Units']], 'CustomerAddresses']
        ]);
        $this->set('order', $order);
        $this->set('_serialize', ['order']);
    }
	
	public function cancelBox($id = null)
    {
		$this->viewBuilder()->layout('');
        $order = $this->Orders->get($id);
		$order_date=$order->order_date;
		$delivery_date=$order->delivery_date;
		$curent_date=$order->curent_date;
		$CancelReasons=$this->Orders->CancelReasons->find('list');
		if ($this->request->is(['patch', 'post', 'put'])) {
			$cancel_id=$this->request->data['cancel_id'];
			$Orders=$this->Orders->get($id);
			$Orders->order_date=$order_date;
			$Orders->delivery_date=$delivery_date;
			$Orders->curent_date=$curent_date;
			$Orders->status='Cancel';
			$Orders->cancel_id=$cancel_id;
			$this->Orders->save($Orders);

			return $this->redirect(['action' => 'index']);
		}
        $this->set('order', $order);
        $this->set('CancelReasons', $CancelReasons);
        $this->set('_serialize', ['order', 'CancelReasons']);
    }

	public function ajaxDeliver($id = null)
    {
		$this->viewBuilder()->layout('');
         $order = $this->Orders->get($id, [
            'contain' => ['Customers']
        ]);
        $this->set('order', $order);
        $this->set('_serialize', ['order']);
    }
	
	public function undoBox($id = null)
    {
		$Orders = $this->Orders->get($id);
		$order_date=$Orders->order_date;
		$Orders->status='In Process';
		$Orders->order_date=$order_date;
		$Orders->cancel_id=0;
		 if ($this->Orders->save($Orders)) {
            $this->Flash->success(__('The Order has been reopened.'));
        } else {
            $this->Flash->error(__('The Order could not be Reopened. Please, try again.'));
        }
		return $this->redirect(['action' => 'index']);
    }
	public function ajaxOrderView()
    {
		$order_id=$this->request->data['odr_id'];
		$jain_thela_admin_id=$this->Auth->User('jain_thela_admin_id'); 
		$order_details=$this->Orders->OrderDetails->find()->where(['order_id'=>$order_id])->contain(['Items'=>['Units']]);

		pr($order_details->toArray());  
 		$this->set('order_details', $order_details);
 		$this->set('order_id', $order_id);
        $this->set('_serialize', ['order_details', 'order_id']);
		 
	}

	public function ajaxDeliverApi()
    {
		$order_id=$this->request->data['order_id'];
		$jain_thela_admin_id=$this->Auth->User('jain_thela_admin_id');
		$this->set(compact('jain_thela_admin_id', 'order_id'));
        $this->set('_serialize', ['jain_thela_admin_id', 'order_id']);
	}
    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add($order_type = Null,$bulkorder_id = Null)
    {
		
		@$bulkorder_id;
		$this->viewBuilder()->layout('index_layout');
		$jain_thela_admin_id=$this->Auth->User('jain_thela_admin_id');
        $order = $this->Orders->newEntity();
        if ($this->request->is('post')) {
            $order = $this->Orders->patchEntity($order, $this->request->getData());
			$curent_date=date('Y-m-d');

			$last_order_no = $this->Orders->find()->select(['order_no', 'get_auto_no'])->order(['order_no'=>'DESC'])->where(['jain_thela_admin_id'=>$jain_thela_admin_id, 'curent_date'=>$curent_date])->first();

			if(!empty($last_order_no)){
			$get_auto_no = h(str_pad(number_format($last_order_no->get_auto_no+1),6, '0', STR_PAD_LEFT));
			$next_get_auto_no=$last_order_no->get_auto_no+1;
			}else{
		    $get_auto_no=h(str_pad(number_format(1),6, '0', STR_PAD_LEFT));
			echo $next_get_auto_no=1;
			}
			$get_date=str_replace('-','',$curent_date);
			$exact_order_no=h('W'.$get_date.$get_auto_no);//orderno///
			
			$order->order_no=$exact_order_no;
 			$order->curent_date=$curent_date;
			$order->get_auto_no=$next_get_auto_no;
			$order->order_type=$order_type;
			$order->jain_thela_admin_id=$jain_thela_admin_id;
			$order->grand_total=$this->request->data['total_amount'];
			$order->delivery_date=date('Y-m-d', strtotime($this->request->data['delivery_date']));
			
            if ($this->Orders->save($order)) {
				$customer = $this->Orders->Customers->get($order->customer_id);
				$ledgerAccount = $this->Orders->LedgerAccounts->newEntity();
				$ledgerAccount->name = $customer->name.$customer->mobile;
				$ledgerAccount->customer_id = $order->customer_id;
				$ledgerAccount->account_group_id = '5';
				$ledgerAccount->jain_thela_admin_id = $jain_thela_admin_id;
				$this->Orders->LedgerAccounts->save($ledgerAccount);
				
			
					$ledgers = $this->Orders->Ledgers->newEntity();
					$ledgers->ledger_account_id	 = $ledgerAccount->id;
					$ledgers->debit = $order->grand_total;
					$ledgers->credit = '0';
					$this->Orders->Ledgers->save($ledgers);

					$ledgers = $this->Orders->Ledgers->newEntity();
					$ledgers->ledger_account_id	 = 9;
					$ledgers->debit = $order->amount_from_wallet;
					$ledgers->credit = '0';
					if($order->amount_from_wallet > 0){
					$this->Orders->Ledgers->save($ledgers);
					}
					
					$ledgers = $this->Orders->Ledgers->newEntity();
					$ledgers->ledger_account_id	 = 8;
					$ledgers->debit = '0';
					$ledgers->credit = ($order->grand_total+$order->amount_from_wallet);
					$this->Orders->Ledgers->save($ledgers);
			


				
                $this->Flash->success(__('The order has been saved.'));

                return $this->redirect(['action' => 'index']);
            }

            $this->Flash->error(__('The order could not be saved. Please, try again.'));
        }
        $customer_fetchs = $this->Orders->Customers->find('all');
		foreach($customer_fetchs as $customer_fetch){
			$customer_name=$customer_fetch->name;
			$customer_mobile=$customer_fetch->mobile;
			$customers[]= ['value'=>$customer_fetch->id,'text'=>$customer_name." (".$customer_mobile.")"];
		}
		$deliverytime_fetchs = $this->Orders->DeliveryTimes->find('all');
		foreach($deliverytime_fetchs as $deliverytime_fetch){
			$time_id=$deliverytime_fetch->id;
			$time_from=$deliverytime_fetch->time_from;
			$time_to=$deliverytime_fetch->time_to;
			$delivery_time[]= ['value'=>$time_id,'text'=>$time_from." - ".$time_to];
		}
       // $promoCodes = $this->Orders->PromoCodes->find('list');
		$item_fetchs = $this->Orders->Items->find()->where(['Items.jain_thela_admin_id' => $jain_thela_admin_id, 'Items.freeze !='=>1])->contain(['Units']);

		foreach($item_fetchs as $item_fetch){
			$item_name=$item_fetch->name;
			$alias_name=$item_fetch->alias_name;
			@$unit_name=$item_fetch->unit->unit_name;
			$print_quantity=$item_fetch->print_quantity;
			$rates=$item_fetch->offline_sales_rate;
			$minimum_quantity_factor=$item_fetch->minimum_quantity_factor;
			$minimum_quantity_purchase=$item_fetch->minimum_quantity_purchase;
			$items[]= ['value'=>$item_fetch->id,'text'=>$item_name." (".$alias_name.")", 'print_quantity'=>$print_quantity, 'rates'=>$rates, 'minimum_quantity_factor'=>$minimum_quantity_factor, 'unit_name'=>$unit_name, 'minimum_quantity_purchase'=>$minimum_quantity_purchase];
		}
		$this->loadModel('BulkBookingLeads');
        $bulk_Details = $this->BulkBookingLeads->find()->where(['id' => $bulkorder_id])->toArray();

        $this->set(compact('order', 'customers', 'items', 'order_type', 'bulk_Details', 'bulkorder_id','delivery_time'));
        $this->set('_serialize', ['order']);
    }
	/**
     * Ajax method
     **/
	public function ajaxCustomerDiscount()
    {
		$this->viewBuilder()->layout('ajax');
		$jain_thela_admin_id=$this->Auth->User('jain_thela_admin_id');
		$customer = $this->Orders->Customers->get($this->request->data['customer_id']);
		$this->set(compact('customer'));
	}
    /**
     * Edit method
     *
     * @param string|null $id Order id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
		$this->viewBuilder()->layout('index_layout');
		$jain_thela_admin_id=$this->Auth->User('jain_thela_admin_id');
		$curent_date=date('Y-m-d');
		
        $order = $this->Orders->get($id, [
            'contain' => []
        ]);
		
		$amount_from_wallet=$order->amount_from_wallet;
		$amount_from_jain_cash=$order->amount_from_jain_cash;
		$amount_from_promo_code=$order->amount_from_promo_code;
		$customer_id=$order->customer_id;
		$order_date=$order->order_date;
		
		$paid_amount=$amount_from_wallet+$amount_from_jain_cash+$amount_from_promo_code;
        if ($this->request->is(['patch', 'post', 'put'])) {
            $order = $this->Orders->patchEntity($order, $this->request->getData());
			$total_amount=$this->request->data['total_amount'];
			$delivery_charge=$this->request->data['delivery_charge'];
			$grand_total=$total_amount+$delivery_charge;
			$remaining_amount=$grand_total-$paid_amount;
			$remaining_paid_amount=$paid_amount-$grand_total;
			if($remaining_amount>0){
				$order->pay_amount=$remaining_amount;
			}
			else if($remaining_paid_amount>0){
				$order->pay_amount=0;
				$query = $this->Orders->Wallets->query();
					$query->insert(['customer_id', 'advance', 'narration', 'return_order_id'])
							->values([
							'customer_id' => $customer_id,
							'advance' => $remaining_paid_amount,
							'narration' => 'Amount Return form Order',
							'return_order_id' => $id
							])
					->execute();
			}
			$order->grand_total=$grand_total;
			$order->order_date=$order_date;
			$order->delivery_date=date('Y-m-d', strtotime($this->request->data['delivery_date']));

            if ($this->Orders->save($order)) {
                $this->Flash->success(__('The order has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The order could not be saved. Please, try again.'));
        }
		
		$item_fetchs = $this->Orders->Items->find()->where(['Items.jain_thela_admin_id' => $jain_thela_admin_id, 'Items.freeze !='=>1])->contain(['Units']);

		foreach($item_fetchs as $item_fetch){
			$item_name=$item_fetch->name;
			$alias_name=$item_fetch->alias_name;
			@$unit_name=$item_fetch->unit->unit_name;
			$print_quantity=$item_fetch->print_quantity;
			$rates=$item_fetch->offline_sales_rate;
			$minimum_quantity_factor=$item_fetch->minimum_quantity_factor;
			$minimum_quantity_purchase=$item_fetch->minimum_quantity_purchase;
			$items[]= ['value'=>$item_fetch->id,'text'=>$item_name." (".$alias_name.")", 'print_quantity'=>$print_quantity, 'rates'=>$rates, 'minimum_quantity_factor'=>$minimum_quantity_factor, 'unit_name'=>$unit_name, 'minimum_quantity_purchase'=>$minimum_quantity_purchase];
		}
        $customer_fetchs = $this->Orders->Customers->find('all');
		foreach($customer_fetchs as $customer_fetch){
			$customer_name=$customer_fetch->name;
			$customer_mobile=$customer_fetch->mobile;
			$customers[]= ['value'=>$customer_fetch->id,'text'=>$customer_name." (".$customer_mobile.")"];
		}
		$deliverytime_fetchs = $this->Orders->DeliveryTimes->find('all');
		foreach($deliverytime_fetchs as $deliverytime_fetch){
			$time_id=$deliverytime_fetch->id;
			$time_from=$deliverytime_fetch->time_from;
			$time_to=$deliverytime_fetch->time_to;
			$delivery_time[]= ['value'=>$time_id,'text'=>$time_from." - ".$time_to];
		}
        $promoCodes = $this->Orders->PromoCodes->find('list', ['limit' => 200]);
        $OrderDetails = $this->Orders->OrderDetails->find()->where(['order_id'=>$id]);
        $this->set(compact('order', 'customers', 'promoCodes', 'OrderDetails', 'items','delivery_time'));
        $this->set('_serialize', ['order']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Order id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $order = $this->Orders->get($id);
        if ($this->Orders->delete($order)) {
            $this->Flash->success(__('The order has been deleted.'));
        } else {
            $this->Flash->error(__('The order could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
	
	public function onlineSaleDetails($item_id=null,$from_date=null,$to_date=null){
				$this->viewBuilder()->layout('index_layout');
		$jain_thela_admin_id=$this->Auth->User('jain_thela_admin_id');
		
		
		
		
		$ItemLedgers=$this->Orders->ItemLedgers->find()
					->where(['item_id'=>$item_id,'order_id !='=>0,'transaction_date >='=>$from_date,'transaction_date <='=>$to_date])
					->contain(['Orders','Items'=>['Units']])
					->order(['Orders.id'=>'DESC']);
					
					//pr($ItemLedgers->toArray());exit;
		/* $SumQty=0;
		foreach($ItemLedgers as $ItemLedger){
			if($ItemLedger->order->order_type!='Bulkorder '){
				$SumQty+=$ItemLedger->quantity;
			}
		} 
		*/
		$this->set(compact('ItemLedgers','from_date','to_date'));
		
	}
	
	public function bulkSaleDetails($item_id=null,$from_date=null,$to_date=null){
		
		$this->viewBuilder()->layout('index_layout');
		$jain_thela_admin_id=$this->Auth->User('jain_thela_admin_id');
		
		
		$ItemLedgers=$this->Orders->ItemLedgers->find()
					->where(['item_id'=>$item_id,'order_id !='=>0,'transaction_date >='=>$from_date,'transaction_date <='=>$to_date])
					->contain(['Orders','Items'=>['Units']])
					->order(['Orders.id'=>'DESC'])
					->where(['order_type IN'=>['Bulkorder']]);
		
			/* $bulkSales = $this->Orders->OrderDetails->find()->contain(['Orders'=>function ($q)use($where) {
				return $q->where(['order_type IN'=>['Bulkorder']])->where($where);
			},'Items'=>['Units']])->where(['OrderDetails.item_id'=>$item_id])->order(['Orders.id'=>'Desc']); */
		
		//pr($bulkSales->toArray());exit;
		$this->set(compact('ItemLedgers','from_date','to_date'));
        $this->set('_serialize', ['bulkSales']);
	}
}
