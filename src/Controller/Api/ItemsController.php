<?php
namespace App\Controller\Api;
use App\Controller\Api\AppController;
class ItemsController extends AppController
{
    public function item()
    {
		$jain_thela_admin_id=$this->request->query('jain_thela_admin_id');
		$item_category_id=$this->request->query('item_category_id');
		$item_sub_category_id=$this->request->query('item_sub_category_id');
		$customer_id=$this->request->query('customer_id');

		if($item_sub_category_id=='0')
		{
			$where=['Items.jain_thela_admin_id'=>$jain_thela_admin_id, 'Items.item_category_id'=>$item_category_id, 'Items.is_combo'=>'no', 'Items.freeze'=>0, 'Items.ready_to_sale'=>'Yes'];
		}
		else
		{
			$where=['Items.jain_thela_admin_id'=>$jain_thela_admin_id, 'Items.item_category_id'=>$item_category_id, 'Items.item_sub_category_id'=>$item_sub_category_id, 'Items.is_combo'=>'no', 'Items.freeze'=>0, 'Items.ready_to_sale'=>'Yes'];
		}
		$items = $this->Items->find()
					->where($where)
					->order(['name'=>'ASC'])
					->contain(['Units','Carts'=>function($q) use($customer_id){
						return $q->where(['customer_id'=>$customer_id]);
					}]);
					$items->select(['image_url' => $items->func()->concat(['http://app.jainthela.in'.$this->request->webroot.'img/item_images/','image' => 'identifier' ])])
                    ->autoFields(true);
					
				//pr($items->toArray());	exit;
					
			
		foreach($items as $item){
			if(!$item->cart){
				$item->cart=(object)[];
			}
		} 
		
        
		$cart_count = $this->Items->Carts->find('All')->where(['Carts.customer_id'=>$customer_id])->count();
		$status=true;
		$error="";
        $this->set(compact('status', 'error', 'items','cart_count'));
        $this->set('_serialize', ['status', 'error', 'items','cart_count']);
    }

	 public function itemdescription()
    {
		$jain_thela_admin_id=$this->request->query('jain_thela_admin_id');
		$item_id=$this->request->query('item_id');
		$customer_id=$this->request->query('customer_id');
		$item_description = $this->Items->find()
							->select(['image_url' => $this->Items->find()->func()->concat(['http://app.jainthela.in'.$this->request->webroot.'img/item_images/','image' => 'identifier' ])])
							->where(['Items.jain_thela_admin_id'=>$jain_thela_admin_id, 'Items.id'=>$item_id])
							->contain(['Units', 'Carts'=>function($q) use($customer_id){
						return $q->where(['customer_id'=>$customer_id]);
					}])
							->autoFields(true)->first();
             
		
			if(!$item_description->cart){
				$item_description->cart=(object)[];
			}
		
$querys=$this->Items->ItemLedgers->find();
				$customer_also_bought=$querys
						->select(['total_rows' => $querys->func()->count('ItemLedgers.id'),'item_id',])
						->where(['inventory_transfer'=>'no','status'=>'out'])
						->group(['ItemLedgers.item_id'])
						->order(['total_rows'=>'DESC'])
						->limit(5)
						->contain(['Items'=>function($q){
						return $q->select(['name', 'image', 'sales_rate','minimum_quantity_factor','ready_to_sale', 'out_of_stock', 'print_rate', 'print_quantity', 'discount_per','minimum_quantity_purchase'])
						->contain(['Units'=>function($q){
						return $q->select(['id','longname','shortname','is_deleted','jain_thela_admin_id']);
						}]);
						}]);
						$customer_also_bought->select(['image_url' => $customer_also_bought->func()->concat(['http://app.jainthela.in'.$this->request->webroot.'img/item_images/','image' => 'identifier' ])]);
		
						$cart_count = $this->Items->Carts->find('All')->where(['Carts.customer_id'=>$customer_id])->count();
			 
		$status=true;
		$error="";
        $this->set(compact('status', 'error', 'item_description', 'customer_also_bought','cart_count'));
        $this->set('_serialize', ['status', 'error', 'item_description', 'customer_also_bought','cart_count']);
    }
	
	 public function viewAll()
    {
		$jain_thela_admin_id=$this->request->query('jain_thela_admin_id');
		$type=$this->request->query('type');
		$customer_id=$this->request->query('customer_id');

		if($type=='popular')
		{
			$query=$this->Items->ItemLedgers->find();
		$view_items=$query
						->select(['total_rows' => $query->func()->count('ItemLedgers.id'),'item_id',])
						->where(['inventory_transfer'=>'no','status'=>'out'])
						->group(['ItemLedgers.item_id'])
						->order(['total_rows'=>'DESC'])
						->limit(5)
						->contain(['Items'=>function($q) use($customer_id){
							return $q->select(['name', 'image', 'sales_rate','minimum_quantity_factor','ready_to_sale', 'out_of_stock', 'print_rate', 'print_quantity', 'discount_per','minimum_quantity_purchase'])
									->contain(['Units'=>function($q) use($customer_id){
										return $q->select(['id','longname','shortname','is_deleted','jain_thela_admin_id']);
									},'Carts'=>function($q) use($customer_id){
										return $q->select(['cart_count'])
										->where(['customer_id'=>$customer_id]);
						}]);
						}]);
						$view_items->select(['image_url' => $view_items->func()->concat(['http://app.jainthela.in'.$this->request->webroot.'img/item_images/','image' => 'identifier' ])]);
						
		foreach($view_items as $item){
			if(!$item->item->cart){
				$item->item->cart=(object)[];
			}
		}
		}
		else if($type=='recently')
		{
				$querys=$this->Items->ItemLedgers->find();
				$view_items=$querys
						->select(['total_rows' => $querys->func()->count('ItemLedgers.id'),'item_id',])
						->where(['inventory_transfer'=>'no','status'=>'out'])
						->group(['ItemLedgers.item_id'])
						->order(['total_rows'=>'DESC'])
						->limit(5)
						->contain(['Items'=>function($q) use($customer_id){
							return $q->select(['name', 'image', 'sales_rate','minimum_quantity_factor','ready_to_sale', 'out_of_stock', 'print_rate', 'print_quantity', 'discount_per','minimum_quantity_purchase'])
							->contain(['Units'=>function($q) use($customer_id){
								return $q->select(['id','longname','shortname','is_deleted','jain_thela_admin_id']);
							},'Carts'=>function($q) use($customer_id){
										return $q->select(['cart_count'])
										->where(['customer_id'=>$customer_id]);
						}]);
						}]);
						$view_items->select(['image_url' => $view_items->func()->concat(['http://app.jainthela.in'.$this->request->webroot.'img/item_images/','image' => 'identifier' ])]);
		
		foreach($view_items as $item){
			if(!$item->item->cart){
				$item->item->cart=(object)[];
			}
		}
		}
		else if($type='bought')
		{
        $querys=$this->Items->ItemLedgers->find();
				$view_items=$querys
						->select(['total_rows' => $querys->func()->count('ItemLedgers.id'),'item_id',])
						->where(['inventory_transfer'=>'no','status'=>'out'])
						->group(['ItemLedgers.item_id'])
						->order(['total_rows'=>'DESC'])
						->limit(5)
						->contain(['Items'=>function($q) use($customer_id){
						return $q->select(['name', 'image', 'sales_rate','minimum_quantity_factor','ready_to_sale', 'out_of_stock', 'print_rate', 'print_quantity', 'discount_per','minimum_quantity_purchase'])
						->contain(['Units'=>function($q) use($customer_id){
						                return $q->select(['id','longname','shortname','is_deleted','jain_thela_admin_id']);
						},'Carts'=>function($q) use($customer_id){
										return $q->select(['cart_count'])
										->where(['Carts.customer_id'=>$customer_id]);
						}]);
						}]);
						$view_items->select(['image_url' => $view_items->func()->concat(['http://app.jainthela.in'.$this->request->webroot.'img/item_images/','image' => 'identifier' ])]);
		foreach($view_items as $item){
			if(!$item->item->cart){
				$item->item->cart=(object)[];
			}
		}
		}
        
		$cart_count = $this->Items->Carts->find('All')->where(['Carts.customer_id'=>$customer_id])->count();
		
		$status=true;
		$error="";
        $this->set(compact('status', 'error','cart_count', 'view_items'));
        $this->set('_serialize', ['status', 'error','cart_count', 'view_items']);
    }
	
	public function searchItem()
    {
		$jain_thela_admin_id=$this->request->query('jain_thela_admin_id');
		$item_query=$this->request->query('item_query');
		$customer_id=$this->request->query('customer_id');

        $search_items = $this->Items->find()
		->where(['Items.is_combo'=>'no', 'Items.jain_thela_admin_id'=>$jain_thela_admin_id, 'Items.name LIKE' => '%'.$item_query.'%', 'Items.freeze'=>0, 'Items.ready_to_sale'=>'Yes'])
		->contain(['Units','Carts'=>function($q) use($customer_id){
						return $q->where(['customer_id'=>$customer_id]);
					}]);
		$search_items->select(['image_url' => $search_items->func()->concat(['http://app.jainthela.in'.$this->request->webroot.'img/item_images/','image' => 'identifier' ])])
                                ->autoFields(true);
		foreach($search_items as $item){
			if(!$item->cart){
				$item->cart=(object)[];
			}
		}
		
		$cart_count = $this->Items->Carts->find('All')->where(['Carts.customer_id'=>$customer_id])->count();
		$status=true;
		$error="";
        $this->set(compact('status', 'error', 'cart_count', 'search_items'));
        $this->set('_serialize', ['status', 'error', 'cart_count', 'search_items']);
     }
	 public function fetchItem()
    {
		$jain_thela_admin_id=$this->request->query('jain_thela_admin_id');
			$where=['Items.jain_thela_admin_id'=>$jain_thela_admin_id, 'Items.is_combo'=>'no', 'Items.freeze'=>0,'Items.is_virtual'=>'no'];
		$fetch_items = $this->Items->find()
					->where($where)
					->order(['name'=>'ASC'])
					->contain(['Units']);
					$fetch_items->select(['image_url' => $fetch_items->func()->concat(['http://app.jainthela.in'.$this->request->webroot.'img/item_images/','image' => 'identifier' ])])
                    ->autoFields(true);
		
		
		$status=true;
		$error="";
        $this->set(compact('status', 'error', 'fetch_items'));
        $this->set('_serialize', ['status', 'error', 'fetch_items']);
    }


	
}