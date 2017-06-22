<?php
namespace App\Controller\Api;
use App\Controller\Api\AppController;
class CustomerAddressesController extends AppController
{
    public function addAddress()
    {
		$jain_thela_admin_id=$this->request->data('jain_thela_admin_id');
		$customer_id=$this->request->data('customer_id');
		$name=$this->request->data('name');
		$mobile=$this->request->data('mobile');
		$house_no=$this->request->data('house_no');
		$address=$this->request->data('address');
		$locality=$this->request->data('mobile');
		$default_address=$this->request->data('default_address');
		$tag=$this->request->data('tag');
		$customer_address_id=$this->request->data('customer_address_id');
		
		if($tag=='add'){
		  $query = $this->CustomerAddresses->query();
				$result = $query->update()
                    ->set(['default_address' => 0])
                    ->where(['customer_id' => $customer_id])
                    ->execute();
					 
			$query = $this->CustomerAddresses->query();
					$query->insert(['customer_id', 'name', 'mobile', 'house_no', 'address', 'locality', 'default_address'])
							->values([
							'customer_id' => $customer_id,
							'name' => $name,
							'mobile' => $mobile,
							'house_no' => $house_no,
							'address' => $address,
							'locality' => $locality,
							'default_address' => 1
							])
					->execute();
		}
		if($tag=='edit'){
			$query = $this->CustomerAddresses->query();
				$result = $query->update()
                    ->set(['default_address' => 0])
                    ->where(['customer_id' => $customer_id])
                    ->execute();
					
			$query = $this->CustomerAddresses->query();
				$result = $query->update()
                    ->set(['customer_id' => $customer_id,
							'name' => $name,
							'mobile' => $mobile,
							'house_no' => $house_no,
							'address' => $address,
							'locality' => $locality,
							'default_address' => 1
							])
					->where(['id' => $customer_address_id])
					->execute();
		}
		if($tag=='delete'){
		
			$query = $this->CustomerAddresses->query();
				$result = $query->delete()
					->where(['id' => $customer_address_id])
					->execute();
		}
		if($tag=='default'){
			$query = $this->CustomerAddresses->query();
				$result = $query->update()
                    ->set(['default_address' => 0])
                    ->where(['customer_id' => $customer_id])
                    ->execute();
					
			$query = $this->CustomerAddresses->query();
				$result = $query->update()
                    ->set(['default_address' => 1])
					->where(['id' => $customer_address_id])
					->execute();
		}
		
		$customer_addresses=$this->CustomerAddresses->find()->where(['customer_id' => $customer_id])->contain(['Customers']);
		$status=true;
		$error="";
        $this->set(compact('status', 'error','customer_addresses'));
        $this->set('_serialize', ['status', 'error', 'customer_addresses']);
    }
}