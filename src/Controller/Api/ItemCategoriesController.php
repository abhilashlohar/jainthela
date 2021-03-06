<?php
namespace App\Controller\Api;
use App\Controller\Api\AppController;
use Cake\Event\Event;
class ItemCategoriesController extends AppController
{
	public function home()
    {
		$jain_thela_admin_id=$this->request->query('jain_thela_admin_id');
		$customer_id=$this->request->query('customer_id');
	    $itemCategories = $this->ItemCategories->find('All')->where(['jain_thela_admin_id'=>$jain_thela_admin_id]);
		$itemCategories->select(['image_url' => $itemCategories->func()->concat(['http://app.jainthela.in'.$this->request->webroot.'itemcategories/','image' => 'identifier' ])])->limit(2)
                                ->autoFields(true);
		
	    $banners = $this->ItemCategories->Banners->find('All')->where(['link_name'=>'offer', 'Banners.status'=>'Active']);
		$banners->select(['image_url' => $banners->func()->concat(['http://app.jainthela.in'.$this->request->webroot.'banners/','image' => 'identifier' ])])->limit(2)->autoFields(true);
		
		$query=$this->ItemCategories->Items->ItemLedgers->find();
		$popular_items=$query
						->select(['total_rows' => $query->func()->count('ItemLedgers.id'),'item_id',])
						->where(['inventory_transfer'=>'no','status'=>'out'])
						->group(['ItemLedgers.item_id'])
						->order(['total_rows'=>'DESC'])
						->limit(2)
						->contain(['Items'=>function($q){
							return $q->select(['name', 'image', 'sales_rate','minimum_quantity_factor','ready_to_sale', 'out_of_stock', 'print_rate', 'print_quantity', 'discount_per'])
									->contain(['Units'=>function($q){
										return $q->select(['id','longname','shortname','is_deleted','jain_thela_admin_id']);
									}]);
						}]);
						$popular_items->select(['image_url' => $popular_items->func()->concat(['http://app.jainthela.in'.$this->request->webroot.'img/item_images/','image' => 'identifier' ])]);
						
							
				/* $querys=$this->ItemCategories->Items->ItemLedgers->find();
				$recently_bought=$querys
						->select(['total_rows' => $querys->func()->count('ItemLedgers.id'),'item_id',])
						->where(['inventory_transfer'=>'no','status'=>'out'])
						->group(['ItemLedgers.item_id'])
						->order(['total_rows'=>'DESC'])
						->limit(2)
						->contain(['Items'=>function($q){
							return $q->select(['name', 'image', 'sales_rate','minimum_quantity_factor','ready_to_sale', 'out_of_stock', 'print_rate', 'print_quantity', 'discount_per'])
							->contain(['Units'=>function($q){
								return $q->select(['id','longname','shortname','is_deleted','jain_thela_admin_id']);
							}]);
						}]);
						$recently_bought->select(['image_url' => $recently_bought->func()->concat(['http://app.jainthela.in'.$this->request->webroot.'img/item_images/','image' => 'identifier' ])]); */
		$recently_bought=$popular_items;
						$cart_count = $this->ItemCategories->Carts->find('All')->where(['Carts.customer_id'=>$customer_id])->count();						

		$status=true;
		$error="";
        $this->set(compact('status', 'error', 'itemCategories', 'banners','popular_items','recently_bought', 'cart_count'));
        $this->set('_serialize', ['status', 'error', 'itemCategories', 'banners', 'popular_items','recently_bought','cart_count']);
    }
}