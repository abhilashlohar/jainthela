<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * ItemLedgers Controller
 *
 * @property \App\Model\Table\ItemLedgersTable $ItemLedgers
 *
 * @method \App\Model\Entity\ItemLedger[] paginate($object = null, array $settings = [])
 */
class ItemLedgersController extends AppController
{

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Items', 'Franchises', 'PurchaseInwardVouchers']
        ];
        $itemLedgers = $this->paginate($this->ItemLedgers);

        $this->set(compact('itemLedgers'));
        $this->set('_serialize', ['itemLedgers']);
    }

    /**
     * View method
     *
     * @param string|null $id Item Ledger id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $itemLedger = $this->ItemLedgers->get($id, [
            'contain' => ['Items', 'Franchises', 'PurchaseInwardVouchers']
        ]);

        $this->set('itemLedger', $itemLedger);
        $this->set('_serialize', ['itemLedger']);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
		$this->viewBuilder()->layout('index_layout'); 
        $itemLedger = $this->ItemLedgers->newEntity();
		$jain_thela_admin_id=$this->Auth->User('jain_thela_admin_id');

        if ($this->request->is('post')) {
			$item_ledgers=$this->request->getData('item_ledgers');

			$driver_id=$this->request->data['driver_id'];
			$warehouse_id=$this->request->data['warehouse_id'];
			$transaction_date=date('Y-m-d', strtotime($this->request->data['transaction_date'])); 
			$i=0;

			foreach($item_ledgers as $item_ledger){
				$item_ledger=(object)$item_ledger;
				$quantity=$item_ledger->quantity;
				$item_id=$item_ledger->item_id;

				$query = $this->ItemLedgers->query();
				$query->insert(['driver_id', 'warehouse_id', 'transaction_date', 'item_id', 'quantity','status','jain_thela_admin_id', 'inventory_transfer'])
						->values([
						'driver_id' => 0,
						'warehouse_id' => $warehouse_id,
						'transaction_date' => $transaction_date,
						'item_id' => $item_id,
						'quantity' => $quantity,
						'status' => 'out',
						'jain_thela_admin_id' => $jain_thela_admin_id,
						'inventory_transfer' => 'yes'
						])
				->execute();

				$query = $this->ItemLedgers->query();
				$query->insert(['driver_id', 'warehouse_id', 'transaction_date', 'item_id', 'quantity','status', 'jain_thela_admin_id', 'inventory_transfer'])
						->values([
						'driver_id' => $driver_id,
						'warehouse_id' => 0,
						'transaction_date' => $transaction_date,
						'item_id' => $item_id,
						'quantity' => $quantity,
						'status' => 'In',
						'jain_thela_admin_id' => $jain_thela_admin_id,
						'inventory_transfer' => 'yes'
						])
				->execute();
			}
			$this->Flash->success(__('The item ledger has been saved.'));
			return $this->redirect(['action' => 'add']);
            $this->Flash->error(__('The item ledger could not be saved. Please, try again.'));
        }
		$item_fetchs = $this->ItemLedgers->Items->find()->where(['Items.jain_thela_admin_id' => $jain_thela_admin_id, 'Items.is_combo'=>'no', 'Items.is_virtual'=>'no', 'Items.freeze'=>0])->contain(['Units']);
			foreach($item_fetchs as $item_fetch){
			$item_name=$item_fetch->name;
			$alias_name=$item_fetch->alias_name;
			$print_quantity=$item_fetch->print_quantity;
			$unit_name=$item_fetch->unit->unit_name;
			$minimum_quantity_factor=$item_fetch->minimum_quantity_factor;
			$items[]= ['value'=>$item_fetch->id,'text'=>$item_name." (".$alias_name.")", 'print_quantity'=>$print_quantity, 'minimum_quantity_factor'=>$minimum_quantity_factor, 'unit_name'=>$unit_name];
		}
        $drivers = $this->ItemLedgers->Drivers->find('list')->where(['jain_thela_admin_id' => $jain_thela_admin_id]);
		$warehouses = $this->ItemLedgers->Warehouses->find('list')->where(['jain_thela_admin_id' => $jain_thela_admin_id]);
        $this->set(compact('itemLedger', 'items', 'drivers', 'warehouses'));
        $this->set('_serialize', ['itemLedger']);
    }

	public function stockReturn()
    {
		$this->viewBuilder()->layout('index_layout'); 
        $itemLedger = $this->ItemLedgers->newEntity();
		$jain_thela_admin_id=$this->Auth->User('jain_thela_admin_id');
        if ($this->request->is('post')) {
			$item_ledgers=$this->request->getData('item_ledgers');
			 
			$driver_id=$this->request->data['driver_id'];
			$warehouse_id=$this->request->data['warehouse_id'];			
			$transaction_date=date('Y-m-d', strtotime($this->request->data['transaction_date'])); 
			$i=0;
			foreach($item_ledgers as $item_ledger){
				$item_ledger=(object)$item_ledger;
				$item_ledger_quantity=$item_ledger->quantity;
				$total_quantity=$item_ledger->quantity+$item_ledger->waste;
				$item_id=$item_ledger->item_id;
				$waste=$item_ledger->waste;
				
				$query = $this->ItemLedgers->query();
				$query->insert(['driver_id', 'warehouse_id', 'transaction_date', 'item_id', 'quantity','status', 'jain_thela_admin_id', 'inventory_transfer'])
						->values([
						'driver_id' => 0,
						'warehouse_id' => $warehouse_id,
						'transaction_date' => $transaction_date,
						'item_id' => $item_id,
						'quantity' => $item_ledger_quantity,
						'status' => 'In',
						'jain_thela_admin_id' => $jain_thela_admin_id,
						'inventory_transfer' => 'yes'
						])
				->execute();	
				
				$query = $this->ItemLedgers->query();
				$query->insert(['driver_id', 'warehouse_id', 'transaction_date', 'item_id', 'quantity','status', 'jain_thela_admin_id', 'inventory_transfer'])
						->values([
						'driver_id' => $driver_id,
						'warehouse_id' => 0,
						'transaction_date' => $transaction_date,
						'item_id' => $item_id,
						'quantity' => $total_quantity,
						'status' => 'out',
						'jain_thela_admin_id' => $jain_thela_admin_id,
						'inventory_transfer' => 'yes'
						])
				->execute();
				
				$query = $this->ItemLedgers->query();
				$query->insert(['driver_id', 'warehouse_id', 'transaction_date', 'item_id', 'quantity','status', 'jain_thela_admin_id','different_driver_id', 'weight_variation', 'inventory_transfer'])
						->values([
						'driver_id' => 0,
						'warehouse_id' => 0,
						'transaction_date' => $transaction_date,
						'item_id' => $item_id,
						'quantity' => $waste,
						'status' => 'In',
						'jain_thela_admin_id' => $jain_thela_admin_id,
						'different_driver_id' => $driver_id,
						'weight_variation' => 1,
						'inventory_transfer' => 'yes'
						])
				->execute();
			}
			
			$this->Flash->success(__('The item ledger has been saved.'));
			return $this->redirect(['action' => 'stock_return']);         
            $this->Flash->error(__('The item ledger could not be saved. Please, try again.'));
        }
        $items = $this->ItemLedgers->Items->find('list')->where(['jain_thela_admin_id' => $jain_thela_admin_id]);
        $drivers = $this->ItemLedgers->Drivers->find('list')->where(['jain_thela_admin_id' => $jain_thela_admin_id]);
		$warehouses = $this->ItemLedgers->Warehouses->find('list')->where(['jain_thela_admin_id' => $jain_thela_admin_id]);
        $this->set(compact('itemLedger', 'items', 'drivers', 'warehouses'));
        $this->set('_serialize', ['itemLedger']);
    }

	public function ajaxStockReturn()
    {
		  $driver_id=$this->request->data['driver'];
		  $jain_thela_admin_id=$this->Auth->User('jain_thela_admin_id');
		  
 			$query = $this->ItemLedgers->find();
		$totalInCase = $query->newExpr()
			->addCase(
				$query->newExpr()->add(['status' => 'In']),
				$query->newExpr()->add(['quantity']),
				'integer'
			);
		$totalOutCase = $query->newExpr()
			->addCase(
				$query->newExpr()->add(['status' => 'out']),
				$query->newExpr()->add(['quantity']),
				'integer'
			);
		$query->select([
			'total_in' => $query->func()->sum($totalInCase),
			'total_out' => $query->func()->sum($totalOutCase),'id','item_id'
		])
		->where(['ItemLedgers.driver_id' => $driver_id,'ItemLedgers.jain_thela_admin_id' => $jain_thela_admin_id])
		->group('item_id')
		->autoFields(true)
		->contain(['Items'=>['Units']]);
        $itemLedgers = ($query);
		//
		$count=$itemLedgers->count();
        $this->set(compact('itemLedgers','count'));
     }

	public function DriverReport()
    {
		$this->viewBuilder()->layout('index_layout'); 
        $itemLedger = $this->ItemLedgers->newEntity();
		$jain_thela_admin_id=$this->Auth->User('jain_thela_admin_id');

        $items = $this->ItemLedgers->Items->find('list')->where(['jain_thela_admin_id' => $jain_thela_admin_id]);
        $drivers = $this->ItemLedgers->Drivers->find('list')->where(['jain_thela_admin_id' => $jain_thela_admin_id]);
		$warehouses = $this->ItemLedgers->Warehouses->find('list')->where(['jain_thela_admin_id' => $jain_thela_admin_id]);
        $this->set(compact('itemLedger', 'items', 'drivers', 'warehouses'));
        $this->set('_serialize', ['itemLedger']);
    }

	public function ajaxDriverReport()
    {
		  $driver_id=$this->request->data['driver'];
		  $jain_thela_admin_id=$this->Auth->User('jain_thela_admin_id');
		  
 			$query = $this->ItemLedgers->find();
		$totalInCase = $query->newExpr()
			->addCase(
				$query->newExpr()->add(['status' => 'In']),
				$query->newExpr()->add(['quantity']),
				'integer'
			);
		$totalOutCase = $query->newExpr()
			->addCase(
				$query->newExpr()->add(['status' => 'out']),
				$query->newExpr()->add(['quantity']),
				'integer'
			);
		$query->select([
			'total_in' => $query->func()->sum($totalInCase),
			'total_out' => $query->func()->sum($totalOutCase),'id','item_id'
		])
		->where(['ItemLedgers.driver_id' => $driver_id, 'ItemLedgers.jain_thela_admin_id' => $jain_thela_admin_id])
		->group('item_id')
		->autoFields(true)
		->contain(['Items']);
        $itemLedgers = ($query);
		$count=$itemLedgers->count();

        $this->set(compact('itemLedgers','count'));
     }

	 public function productReport()
    {
		$this->viewBuilder()->layout('index_layout'); 
        $itemLedger = $this->ItemLedgers->newEntity();
		$jain_thela_admin_id=$this->Auth->User('jain_thela_admin_id');

        $items = $this->ItemLedgers->Items->find('list')->where(['jain_thela_admin_id' => $jain_thela_admin_id]);
        $drivers = $this->ItemLedgers->Drivers->find('list')->where(['jain_thela_admin_id' => $jain_thela_admin_id]);
		$warehouses = $this->ItemLedgers->Warehouses->find('list')->where(['jain_thela_admin_id' => $jain_thela_admin_id]);
        $this->set(compact('itemLedger', 'items', 'drivers', 'warehouses'));
        $this->set('_serialize', ['itemLedger']);
    }

	 public function ajaxStockIssue()
    {
		  $warehouse_id=$this->request->data['warehouse_id'];
		  $jain_thela_admin_id=$this->Auth->User('jain_thela_admin_id');
		  
 			$query = $this->ItemLedgers->find();
		$totalInCase = $query->newExpr()
			->addCase(
				$query->newExpr()->add(['status' => 'In']),
				$query->newExpr()->add(['quantity']),
				'integer'
			);
		$totalOutCase = $query->newExpr()
			->addCase(
				$query->newExpr()->add(['status' => 'out']),
				$query->newExpr()->add(['quantity']),
				'integer'
			);
		$query->select([
			'total_in' => $query->func()->sum($totalInCase),
			'total_out' => $query->func()->sum($totalOutCase),'id','item_id'
		])
		->where(['ItemLedgers.warehouse_id' => $warehouse_id, 'ItemLedgers.jain_thela_admin_id' => $jain_thela_admin_id])
		->group('item_id')
		->autoFields(true)
		->contain(['Items']);
        $itemLedgers = ($query);
		$count=$itemLedgers->count();
        $this->set(compact('itemLedgers','count'));
     }


	public function ajaxStockAvailable()
    {
		$item_id=$this->request->data['itm_val'];
		$warehouse_id=$this->request->data['ware_house'];
		$jain_thela_admin_id=$this->Auth->User('jain_thela_admin_id');
		 
 			$query = $this->ItemLedgers->find();
		$totalInCase = $query->newExpr()
			->addCase(
				$query->newExpr()->add(['status' => 'In']),
				$query->newExpr()->add(['quantity']),
				'integer'
			);
		$totalOutCase = $query->newExpr()
			->addCase(
				$query->newExpr()->add(['status' => 'out']),
				$query->newExpr()->add(['quantity']),
				'integer'
			);
		$query->select([
			'total_in' => $query->func()->sum($totalInCase),
			'total_out' => $query->func()->sum($totalOutCase),'id','item_id'
		])
		->where(['ItemLedgers.warehouse_id' => $warehouse_id, 'ItemLedgers.jain_thela_admin_id' => $jain_thela_admin_id, 'ItemLedgers.item_id' => $item_id])
		->group('item_id')
		->autoFields(true)
		->contain(['Items']);
        $itemLedgers = ($query);
		  foreach($itemLedgers as $itemLedger){
			   $available_stock=$itemLedger->total_in;
			   $stock_issue=$itemLedger->total_out;
			 echo @$remaining=$available_stock-$stock_issue;
		  }
		  exit;
     }
	 
	 
	 
	public function ajaxWarehouseStockAvailable()
    {
		$item_id=$this->request->data['itm_val'];
		$warehouse_id=$this->request->data['ware_house'];
		$jain_thela_admin_id=$this->Auth->User('jain_thela_admin_id');
		 
 			$query = $this->ItemLedgers->find();
		$totalInCase = $query->newExpr()
			->addCase(
				$query->newExpr()->add(['status' => 'In']),
				$query->newExpr()->add(['quantity']),
				'integer'
			);
		$totalOutCase = $query->newExpr()
			->addCase(
				$query->newExpr()->add(['status' => 'out']),
				$query->newExpr()->add(['quantity']),
				'integer'
			);
		$query->select([
			'total_in' => $query->func()->sum($totalInCase),
			'total_out' => $query->func()->sum($totalOutCase),'id','item_id'
		])
		->where(['ItemLedgers.warehouse_id' => $warehouse_id, 'ItemLedgers.jain_thela_admin_id' => $jain_thela_admin_id, 'ItemLedgers.item_id' => $item_id])
		->group('item_id')
		->autoFields(true)
		->contain(['Items']);
        $itemLedgers = ($query);
		  foreach($itemLedgers as $itemLedger){
			   $available_stock=$itemLedger->total_in;
			   $stock_issue=$itemLedger->total_out;
			 echo @$remaining=$available_stock-$stock_issue;
		  }
		  exit;
     }


	 public function itemIssueReport()
    {
		$url=$this->request->here();
		$url=parse_url($url,PHP_URL_QUERY);
		
		$this->viewBuilder()->layout('index_layout'); 
		$jain_thela_admin_id=$this->Auth->User('jain_thela_admin_id'); 
		
		$from=$this->request->query('from');
		$to=$this->request->query('to');
		$item_id=$this->request->query('item_id');
		$driver_id=$this->request->query('driver_id');
		if(!empty($from)){
			$where['transaction_date >=']=date('Y-m-d',strtotime($from));
		}
		if(!empty($to)){
			$where['transaction_date <=']=date('Y-m-d',strtotime($to));
		}
		if(!empty($item_id)){
			$where['item_id']=$item_id;
		}
		if(!empty($driver_id)){
			$where['driver_id']=$driver_id;
		}
		$where['driver_id !=']=0;
		$where['order_id =']=0;
		$where['inventory_transfer']='yes';
		//pr($where); exit;
 				 
		$item_ledgers=$this->paginate(
			$this->ItemLedgers->find()
			->where($where)
			->order(['transaction_date'=> 'DESC'])
			->contain(['Drivers', 'Items'=>['Units','itemCategories']])
		);
		$drivers=$this->ItemLedgers->Drivers->find('list');
		
		$item_fetchs = $this->ItemLedgers->Items->find()->where(['Items.jain_thela_admin_id' => $jain_thela_admin_id, 'Items.freeze !='=>1, 'Items.is_virtual'=>'no']);

		foreach($item_fetchs as $item_fetch){
			$item_name=$item_fetch->name;
			$alias_name=$item_fetch->alias_name;
			$items[]= ['value'=>$item_fetch->id,'text'=>$item_name." (".$alias_name.")"];
		}
		$this->set(compact('item_ledgers','from','to', 'drivers', 'items','driver_id','item_id','url'));
    }
	
	public function exportExcelItem(){
		$this->viewBuilder()->layout(''); 
		$jain_thela_admin_id=$this->Auth->User('jain_thela_admin_id'); 
		
		$from=$this->request->query('from');
		$to=$this->request->query('to');
		$item_id=$this->request->query('item_id');
		$driver_id=$this->request->query('driver_id');
		if(!empty($from)){
			$where['transaction_date >=']=date('Y-m-d',strtotime($from));
		}
		if(!empty($to)){
			$where['transaction_date <=']=date('Y-m-d',strtotime($to));
		}
		if(!empty($item_id)){
			$where['item_id']=$item_id;
		}
		if(!empty($driver_id)){
			$where['driver_id']=$driver_id;
		}
		$where['driver_id !=']=0;
		$where['order_id =']=0;
		$where['inventory_transfer']='yes';
		//pr($where); exit;
 				 
		$item_ledgers=$this->paginate(
			$this->ItemLedgers->find()
			->where($where)
			->order(['transaction_date'=> 'DESC'])
			->contain(['Drivers', 'Items'=>['Units','itemCategories']])
		);
		$drivers=$this->ItemLedgers->Drivers->find('list');
		
		$item_fetchs = $this->ItemLedgers->Items->find()->where(['Items.jain_thela_admin_id' => $jain_thela_admin_id, 'Items.freeze !='=>1]);

		foreach($item_fetchs as $item_fetch){
			$item_name=$item_fetch->name;
			$alias_name=$item_fetch->alias_name;
			$items[]= ['value'=>$item_fetch->id,'text'=>$item_name." (".$alias_name.")"];
		}
		$this->set(compact('item_ledgers','from','to', 'drivers', 'items','driver_id','item_id'));
	}
	
	public function reportShow()
    {
		$url=$this->request->here();
		$url=parse_url($url,PHP_URL_QUERY);
		
		$jain_thela_admin_id=$this->Auth->User('jain_thela_admin_id');
		$this->viewBuilder()->layout('index_layout');

		$query = $this->ItemLedgers->find();

		$totalInWarehouseCase = $query->newExpr()
			->addCase(
				$query->newExpr()->add(['status' => 'In', 'warehouse_id']),
				$query->newExpr()->add(['quantity']),
				'integer'
			);
		$totalOutWarehouseCase = $query->newExpr()
			->addCase(
				$query->newExpr()->add(['status' => 'out', 'warehouse_id']),
				$query->newExpr()->add(['quantity']),
				'integer'
			);
		$totalInDriverCase = $query->newExpr()
			->addCase(
				$query->newExpr()->add(['status' => 'In', 'driver_id']),
				$query->newExpr()->add(['quantity']),
				'integer'
			);
		$totalOutDriverCase = $query->newExpr()
			->addCase(
				$query->newExpr()->add(['status' => 'out', 'driver_id']),
				$query->newExpr()->add(['quantity']),
				'integer'
			);
		$query->select([
			'totalInWarehouse' => $query->func()->sum($totalInWarehouseCase),
			'totalOutWarehouse' => $query->func()->sum($totalOutWarehouseCase),
			'totalInDriver' => $query->func()->sum($totalInDriverCase),
			'totalOutDriver' => $query->func()->sum($totalOutDriverCase),'id','item_id'
		])
		->where(['ItemLedgers.jain_thela_admin_id'=>$jain_thela_admin_id])
		->group('item_id')
		->autoFields(true)
		->contain(['Items'=>['Units','itemCategories']]);

		$itemLedgers=$query;

		$this->set(compact('itemLedgers','url'));
    }

	public function exportExcelStock(){
		$this->viewBuilder()->layout(''); 
		$jain_thela_admin_id=$this->Auth->User('jain_thela_admin_id'); 
		
		$query = $this->ItemLedgers->find();

		$totalInWarehouseCase = $query->newExpr()
			->addCase(
				$query->newExpr()->add(['status' => 'In', 'warehouse_id']),
				$query->newExpr()->add(['quantity']),
				'integer'
			);
		$totalOutWarehouseCase = $query->newExpr()
			->addCase(
				$query->newExpr()->add(['status' => 'out', 'warehouse_id']),
				$query->newExpr()->add(['quantity']),
				'integer'
			);
		$totalInDriverCase = $query->newExpr()
			->addCase(
				$query->newExpr()->add(['status' => 'In', 'driver_id']),
				$query->newExpr()->add(['quantity']),
				'integer'
			);
		$totalOutDriverCase = $query->newExpr()
			->addCase(
				$query->newExpr()->add(['status' => 'out', 'driver_id']),
				$query->newExpr()->add(['quantity']),
				'integer'
			);
		$query->select([
			'totalInWarehouse' => $query->func()->sum($totalInWarehouseCase),
			'totalOutWarehouse' => $query->func()->sum($totalOutWarehouseCase),
			'totalInDriver' => $query->func()->sum($totalInDriverCase),
			'totalOutDriver' => $query->func()->sum($totalOutDriverCase),'id','item_id'
		])
		->where(['ItemLedgers.jain_thela_admin_id'=>$jain_thela_admin_id])
		->group('item_id')
		->autoFields(true)
		->contain(['Items'=>['Units','itemCategories']]);

		$itemLedgers=$query;

		$this->set(compact('itemLedgers','url'));
	}
	
	public function itemStockUpdate()
    {
		$this->viewBuilder()->layout('index_layout'); 
		$jain_thela_admin_id=$this->Auth->User('jain_thela_admin_id'); 
 				$query = $this->ItemLedgers->find();
		$totalInCase = $query->newExpr()
			->addCase(
				$query->newExpr()->add(['status' => 'In']),
				$query->newExpr()->add(['quantity']),
				'integer'
			);
		$totalOutCase = $query->newExpr()
			->addCase(
				$query->newExpr()->add(['status' => 'out']),
				$query->newExpr()->add(['quantity']),
				'integer'
			);
		$query->select([
			'total_in' => $query->func()->sum($totalInCase),
			'total_out' => $query->func()->sum($totalOutCase),'id','item_id'
		])
		->where(['ItemLedgers.jain_thela_admin_id'=>$jain_thela_admin_id])
		->group('item_id')
		->autoFields(true)
		->contain(['Items'=>['Units','itemCategories']]);
        $itemLedgers = ($query);	
		foreach($itemLedgers as $itemLedger){
			$item_id=$itemLedger->item_id;
			$total_in=$itemLedger->total_in;
			$total_out=$itemLedger->total_out;
			$remaining_stock=$total_in-$total_out;
			$item_data = $this->ItemLedgers->Items->find()->where(['id'=>$item_id]);
				foreach($item_data as $item_data_fetch){
					$minimum_stock=$item_data_fetch->minimum_stock;
					if($remaining_stock<$minimum_stock){
						$query=$this->ItemLedgers->Items->query();
						$result = $query->update()
							->set(['out_of_stock' => 1])
							->where(['id' => $item_id])
							->execute();
					}
				}
			}
         $this->set(compact('itemLedgers'));
    }

	
	
	public function itemSaleReports(){
		$url=$this->request->here();
		$url=parse_url($url,PHP_URL_QUERY);
		$this->viewBuilder()->layout('index_layout'); 
		$jain_thela_admin_id=$this->Auth->User('jain_thela_admin_id');
		
		$from_date = $this->request->query('From');
		$to_date = $this->request->query('To');
		
		$where =[];
		if(!empty($from_date)){
			$from_date=date("Y-m-d",strtotime($this->request->query('From')));
			$where['ItemLedgers.transaction_date >=']=$from_date;
		}
		if(!empty($to_date)){
			$to_date=date("Y-m-d",strtotime($this->request->query('To')));
			$where['ItemLedgers.transaction_date <=']=$to_date;
		}
		
		$where1 =[];
		if(empty($from_date)){
			$from_date=date("Y-m-d");
			$where1['ItemLedgers.transaction_date >=']=$from_date;
		}
		if(empty($to_date)){
			$to_date=date("Y-m-d");
			$where1['ItemLedgers.transaction_date <=']=$to_date;
		}
		if(!empty($where)){
			$itemLedgers = $this->ItemLedgers->find()->contain(['Items'=> function ($q) {
				return $q->where(['is_combo'=>'no','is_virtual'=>'no','freeze'=>0])->contain(['Units'])->order(['Items.name'=>'ASC']);
			}])->where(['ItemLedgers.jain_thela_admin_id'=>$jain_thela_admin_id])->where($where);
		}else{
			$itemLedgers = $this->ItemLedgers->find()->contain(['Items'=> function ($q) {
				return $q->where(['is_combo'=>'no','is_virtual'=>'no','freeze'=>0])->contain(['Units'])->order(['Items.name'=>'ASC']);
			}])->where(['ItemLedgers.jain_thela_admin_id'=>$jain_thela_admin_id])->where($where1);
		}	
		$order_online = []; $order_online_name=[]; $order_bulk = []; $walkins_sales = []; $order_online_rate = [];
		$order_bulk_rate = []; $walkins_sales_rate = []; $Itemsexists=[]; $qty=0; $units=[];
		foreach($itemLedgers as $itemLedger){ 
			$Orders = $this->ItemLedgers->Orders->find()->where(['id'=>$itemLedger->order_id])->toArray();
			if(sizeof($Orders)>0){ 
				foreach($Orders as $order){
					if($order->order_type == 'Online' || $order->order_type == 'Wallet' || $order->order_type == 'Cod' || $order->order_type == 'cod'|| $order->order_type =='Offline'){
						@$order_online[$itemLedger->item_id] += $itemLedger->quantity; 
						@$order_online_rate[$itemLedger->item_id] += $itemLedger->amount; 
						@$Itemsexists[$itemLedger->item_id] = $itemLedger->item_id;
						@$units[$itemLedger->item_id] = $itemLedger->item->unit->unit_name;
						
						//pr($order_online);
					}
					if($order->order_type == 'Bulkorder'){
						@$order_bulk[$itemLedger->item_id] += $itemLedger->quantity;
						@$order_bulk_rate[$itemLedger->item_id] += $itemLedger->amount; 
						@$Itemsexists[$itemLedger->item_id] = $itemLedger->item_id;
						@$units[$itemLedger->item_id] = $itemLedger->item->unit->unit_name;
					}
				}
			}
		$WalkinSales = $this->ItemLedgers->WalkinSales->find()->where(['id'=>$itemLedger->walkin_sales_id]);	
		  foreach($WalkinSales as $WalkinSale){
				@$walkins_sales[$itemLedger->item_id] += $itemLedger->quantity; 
				@$walkins_sales_rate[$itemLedger->item_id] += @$itemLedger->amount; 
				@$Itemsexists[$itemLedger->item_id] = $itemLedger->item_id;
				@$units[$itemLedger->item_id] = $itemLedger->item->unit->unit_name;
		  }
		}
		//pr($Itemsexists);exit;
		
		$ItemList =  $this->ItemLedgers->Items->find()->order(['Items.name'=>'ASC']);
		
		$this->set(compact('itemLedgers','ItemList','from_date','to_date','order_online','order_bulk','order_offline'
		 ,'bulkitemrate','bulkitemqty','Offlineitemrate','Offlineitemqty','Onlineitemrate','Onlineitemqty','list_items','order_online_rate','order_bulk_rate','order_offline_rate','order_online_name','Itemsexists','walkins_sales','walkins_sales_rate','units','url'));
		 $this->set('_serialize', ['itemLedgers']);
	}
	
	public function exportExcel()
	{
		$this->viewBuilder()->layout('');
		$jain_thela_admin_id=$this->Auth->User('jain_thela_admin_id');
		$from_date = $this->request->query('From');
		$to_date = $this->request->query('To');
		
		$where =[];
		if(!empty($from_date)){
			$from_date=date("Y-m-d",strtotime($this->request->query('From')));
			$where['ItemLedgers.transaction_date >=']=$from_date;
		}
		if(!empty($to_date)){
			$to_date=date("Y-m-d",strtotime($this->request->query('To')));
			$where['ItemLedgers.transaction_date <=']=$to_date;
		}
		
		$where1 =[];
		if(empty($from_date)){
			$from_date=date("Y-m-d");
			$where1['ItemLedgers.transaction_date >=']=$from_date;
		}
		if(empty($to_date)){
			$to_date=date("Y-m-d");
			$where1['ItemLedgers.transaction_date <=']=$to_date;
		}
		if(!empty($where)){
			$itemLedgers = $this->ItemLedgers->find()->contain(['Items'=> function ($q) {
				return $q->where(['is_combo'=>'no','is_virtual'=>'no','freeze'=>0])->contain(['Units'])->order(['Items.name'=>'ASC']);
			}])->where(['ItemLedgers.jain_thela_admin_id'=>$jain_thela_admin_id])->where($where);
		}else{
			$itemLedgers = $this->ItemLedgers->find()->contain(['Items'=> function ($q) {
				return $q->where(['is_combo'=>'no','is_virtual'=>'no','freeze'=>0])->contain(['Units'])->order(['Items.name'=>'ASC']);
			}])->where(['ItemLedgers.jain_thela_admin_id'=>$jain_thela_admin_id])->where($where1);
		}	
		$order_online = []; $order_online_name=[]; $order_bulk = []; $walkins_sales = []; $order_online_rate = [];
		$order_bulk_rate = []; $walkins_sales_rate = []; $Itemsexists=[]; $qty=0; $units=[];
		foreach($itemLedgers as $itemLedger){ 
			$Orders = $this->ItemLedgers->Orders->find()->where(['id'=>$itemLedger->order_id])->toArray();
			if(sizeof($Orders)>0){ 
				foreach($Orders as $order){
					if($order->order_type == 'Online' || $order->order_type == 'Wallet' || $order->order_type == 'Cod' || $order->order_type == 'cod'|| $order->order_type =='Offline'){
						@$order_online[$itemLedger->item_id] += $itemLedger->quantity; 
						@$order_online_rate[$itemLedger->item_id] += $itemLedger->amount; 
						@$Itemsexists[$itemLedger->item_id] = $itemLedger->item_id;
						@$units[$itemLedger->item_id] = $itemLedger->item->unit->unit_name;
						
						//pr($order_online);
					}
					if($order->order_type == 'Bulkorder'){
						@$order_bulk[$itemLedger->item_id] += $itemLedger->quantity;
						@$order_bulk_rate[$itemLedger->item_id] += $itemLedger->amount; 
						@$Itemsexists[$itemLedger->item_id] = $itemLedger->item_id;
						@$units[$itemLedger->item_id] = $itemLedger->item->unit->unit_name;
					}
				}
			}
		$WalkinSales = $this->ItemLedgers->WalkinSales->find()->where(['id'=>$itemLedger->walkin_sales_id]);	
		  foreach($WalkinSales as $WalkinSale){
				@$walkins_sales[$itemLedger->item_id] += $itemLedger->quantity; 
				@$walkins_sales_rate[$itemLedger->item_id] += @$itemLedger->amount; 
				@$Itemsexists[$itemLedger->item_id] = $itemLedger->item_id;
				@$units[$itemLedger->item_id] = $itemLedger->item->unit->unit_name;
		  }
		}
		//pr($Itemsexists);exit;
		
		$ItemList =  $this->ItemLedgers->Items->find()->order(['Items.name'=>'ASC']);
		
		$this->set(compact('itemLedgers','ItemList','from_date','to_date','order_online','order_bulk','order_offline'
		 ,'bulkitemrate','bulkitemqty','Offlineitemrate','Offlineitemqty','Onlineitemrate','Onlineitemqty','list_items','order_online_rate','order_bulk_rate','order_offline_rate','order_online_name','Itemsexists','walkins_sales','walkins_sales_rate','units','url'));
		 $this->set('_serialize', ['itemLedgers']);
	}	
	
	
	public function ajaxItemDetails($id = null)
    {
		$jain_thela_admin_id=$this->Auth->User('jain_thela_admin_id'); 
		   $query =$this->ItemLedgers->find();
		   
		$totalInWarehouseCase = $query->newExpr()
			->addCase(
				$query->newExpr()->add(['status' => 'In', 'warehouse_id']),
				$query->newExpr()->add(['quantity']),
				'integer'
			);
		$totalOutWarehouseCase = $query->newExpr()
			->addCase(
				$query->newExpr()->add(['status' => 'out', 'warehouse_id']),
				$query->newExpr()->add(['quantity']),
				'integer'
			);
		$query->select([
			'totalInWarehouse' => $query->func()->sum($totalInWarehouseCase),
			'totalOutWarehouse' => $query->func()->sum($totalOutWarehouseCase),'id','item_id'
		])
		->where(['ItemLedgers.jain_thela_admin_id'=>$jain_thela_admin_id, 'item_id'=>$id, 'warehouse_id !='=>0])
		->group(['warehouse_id'])
		->autoFields(true)
		->contain(['Items'=>['Units'], 'Drivers', 'Warehouses']);
        $warehpouse_itemLedgers = ($query);
		
/////////////////////////////////
		$query1 =$this->ItemLedgers->find();
		$totalInDriverCase = $query1->newExpr()
			->addCase(
				$query1->newExpr()->add(['status' => 'In', 'driver_id']),
				$query1->newExpr()->add(['quantity']),
				'integer'
			);
		$totalOutDriverCase = $query1->newExpr()
			->addCase(
				$query1->newExpr()->add(['status' => 'out', 'driver_id']),
				$query1->newExpr()->add(['quantity']),
				'integer'
			);
		$query1->select([
			'totalInDriver' => $query->func()->sum($totalInDriverCase),
			'totalOutDriver' => $query->func()->sum($totalOutDriverCase),'id','item_id'
		])
		->where(['ItemLedgers.jain_thela_admin_id'=>$jain_thela_admin_id, 'item_id'=>$id, 'driver_id !='=>0])
		->group(['driver_id'])
		->autoFields(true)
		->contain(['Items'=>['Units'], 'Drivers', 'Warehouses']);
        $driver_itemLedgers = ($query1);
		
        $this->set(compact('warehpouse_itemLedgers', 'driver_itemLedgers'));
    }


    /**
     * Edit method
     *
     * @param string|null $id Item Ledger id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
	public function edit($id = null)
    {
        $itemLedger = $this->ItemLedgers->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $itemLedger = $this->ItemLedgers->patchEntity($itemLedger, $this->request->getData());
            if ($this->ItemLedgers->save($itemLedger)) {
                $this->Flash->success(__('The item ledger has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The item ledger could not be saved. Please, try again.'));
        }
        $items = $this->ItemLedgers->Items->find('list', ['limit' => 200]);
        $franchises = $this->ItemLedgers->Franchises->find('list', ['limit' => 200]);
        $purchaseInwardVouchers = $this->ItemLedgers->PurchaseInwardVouchers->find('list', ['limit' => 200]);
        $this->set(compact('itemLedger', 'items', 'franchises', 'purchaseInwardVouchers'));
        $this->set('_serialize', ['itemLedger']);
    }

	public function itemstockAvailable(){
		$item_id=$this->request->data['itm_val'];
		$jain_thela_admin_id=$this->Auth->User('jain_thela_admin_id');
		 
 		$query = $this->ItemLedgers->find();
		$totalInCase = $query->newExpr()
			->addCase(
				$query->newExpr()->add(['status' => 'In']),
				$query->newExpr()->add(['quantity']),
				'integer'
			);
		$totalOutCase = $query->newExpr()
			->addCase(
				$query->newExpr()->add(['status' => 'out']),
				$query->newExpr()->add(['quantity']),
				'integer'
			);
		$query->select([
			'total_in' => $query->func()->sum($totalInCase),
			'total_out' => $query->func()->sum($totalOutCase),'id','item_id'
		])
		->where(['ItemLedgers.jain_thela_admin_id' => $jain_thela_admin_id, 'ItemLedgers.item_id' => $item_id])
		->group('item_id')
		->autoFields(true)
		->contain(['Items']);
        $itemLedgers = ($query);
		  foreach($itemLedgers as $itemLedger){
			   $available_stock=$itemLedger->total_in;
			   $stock_issue=$itemLedger->total_out;
			 echo @$remaining=$available_stock-$stock_issue;
		  }
		  exit;
	}
	public function wastageVouchers(){
		$this->viewBuilder()->layout('index_layout'); 
        $itemLedger = $this->ItemLedgers->newEntity();
		$jain_thela_admin_id=$this->Auth->User('jain_thela_admin_id');
		
		 if ($this->request->is('post')) {
			 $itemLedger = $this->ItemLedgers->patchEntity($itemLedger, $this->request->getData());
			 
			$transaction_date=date('Y-m-d', strtotime($itemLedger->transaction_date)); 
			
			$query = $this->ItemLedgers->query();
				$query->insert(['transaction_date', 'item_id', 'quantity','status','jain_thela_admin_id', 'wastage','usable_wastage'])
						->values([
						'transaction_date' => $transaction_date,
						'item_id' => $itemLedger->item_id,
						'quantity' => $itemLedger->quantity,
						'status' => ' ',
						'jain_thela_admin_id' => $jain_thela_admin_id,
						'wastage' => 1,
						'usable_wastage' => 0,
						])
				->execute();
			$this->Flash->success(__('The Wastage Vouchers has been saved.'));	
			return $this->redirect(['action' => 'wastageVouchers']);
		 }
		
		$Item_datas = $this->ItemLedgers->Items->find()->where(['Items.jain_thela_admin_id' => $jain_thela_admin_id, 'Items.is_combo'=>'no', 'Items.is_virtual'=>'no', 'Items.freeze'=>0])->contain(['Units']);
		$Items=[];
			foreach($Item_datas as $Item){
				$item_name=$Item->name;
				$alias_name=$Item->alias_name;
				$Items[]= ['value'=>$Item->id,'text'=>$item_name." (".$alias_name.")"];
			}
		
		
		
		$this->set(compact('itemLedger', 'Items'));
        $this->set('_serialize', ['itemLedger']);
	}
	
	public function wastageReport(){
		$url=$this->request->here();
		$url=parse_url($url,PHP_URL_QUERY);
		
		$this->viewBuilder()->layout('index_layout'); 
		$jain_thela_admin_id=$this->Auth->User('jain_thela_admin_id');
		
		$query =$this->ItemLedgers->find();
		   
		$totalOutWarehouseCase = $query->newExpr()
			->addCase(
				$query->newExpr()->add(['wastage' => '1','item_id']),
				$query->newExpr()->add(['quantity']),
				'integer'
			);
	
		$query->select([
			'totalOutWarehouse' => $query->func()->sum($totalOutWarehouseCase),'id','item_id'
		])
		->where(['ItemLedgers.jain_thela_admin_id'=>$jain_thela_admin_id,'wastage'=>'1'])
		->group('item_id')
		->autoFields(true)
		->contain(['Items'=>['Units']]);
        $wastageItems = ($query);
		
		$this->set(compact('wastageItems','url'));
        $this->set('_serialize', ['wastageItems']);
		
	}
    /**
     * Delete method
     *
     * @param string|null $id Item Ledger id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $itemLedger = $this->ItemLedgers->get($id);
        if ($this->ItemLedgers->delete($itemLedger)) {
            $this->Flash->success(__('The item ledger has been deleted.'));
        } else {
            $this->Flash->error(__('The item ledger could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
	
	
}