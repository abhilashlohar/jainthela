<?php

namespace App\Controller\Api;
use App\Controller\Api\AppController;
class CustomersController extends AppController
{
    public function registration()
    {
		$error="";
	    $mobile_no=$this->request->query('mobile_no');
	    $otp=$this->request->query('otp');
		$signup=$this->request->query('status');
		
		
		if(!empty($mobile_no) && empty($otp) && empty($signup))
		{
			
		        $customerDetails = $this->Customers->find()->where(['mobile'=>$mobile_no,'status'=>'completed'])->first();
				if($customerDetails){
				    $id=$customerDetails->id;
					$new_signup='no';
					$random=(string)mt_rand(1000,9999);
					$sms=str_replace(' ', '+', 'Your one time OTP for Jainthela App is: '.$random.'');
					$working_key='A7a76ea72525fc05bbe9963267b48dd96';
					$sms_sender='JAINTE';
					$sms=str_replace(' ', '+', $sms);
					file_get_contents('http://alerts.sinfini.com/api/web2sms.php?workingkey='.$working_key.'&sender='.$sms_sender.'&to='.$mobile_no.'&message='.$sms.'');
					
					$customerDetails = $this->Customers->get($customerDetails->id);
					$customerDetails->otp=$random;
					$this->Customers->save($customerDetails);
					$status=true;
				}else{
					
					$customerDetails = $this->Customers->find()->where(['mobile'=>$mobile_no,'status'=>'incompleted'])->first();
					if($customerDetails)
					{
						$id=$customerDetails->id;
						$new_signup='no';
						$random=(string)mt_rand(1000,9999);
						$sms=str_replace(' ', '+', 'Your one time OTP for Jainthela App is: '.$random.'');
						$working_key='A7a76ea72525fc05bbe9963267b48dd96';
						$sms_sender='JAINTE';
						$sms=str_replace(' ', '+', $sms);
						file_get_contents('http://alerts.sinfini.com/api/web2sms.php?workingkey='.$working_key.'&sender='.$sms_sender.'&to='.$mobile_no.'&message='.$sms.'');
						
						$customerDetails = $this->Customers->get($customerDetails->id);
						$customerDetails->otp=$random;
						$this->Customers->save($customerDetails);
						$status=true;	
					}
					else{
							$customer = $this->Customers->newEntity();
							$customer->mobile=$mobile_no;
							$customer->status='incompleted';
							$random=(string)mt_rand(1000,9999);
							$customer->otp=$random;
							$sms=str_replace(' ', '+', 'Your one time OTP for Jainthela App is: '.$random.'');
							$working_key='A7a76ea72525fc05bbe9963267b48dd96';
							$sms_sender='JAINTE';
							$sms=str_replace(' ', '+', $sms);
							file_get_contents('http://alerts.sinfini.com/api/web2sms.php?workingkey='.$working_key.'&sender='.$sms_sender.'&to='.$mobile_no.'&message='.$sms.'');
							if($this->Customers->save($customer)){
								$new_signup='yes';
								$status=true;
								$customerDetails=$this->Customers->get($customer->id);
							}else{
								$status=false;
								$error="Customer registration failed.";
							}
					  }
					
				}
				$this->set(compact('status', 'error', 'new_signup', 'customerDetails'));
		        $this->set('_serialize', ['status', 'error', 'new_signup', 'customerDetails']);
		}
		else if(!empty($mobile_no) && !empty($otp) && !empty($signup))
		{
			
			if($signup=='completed')
			{
			$customerDetails = $this->Customers->find()->where(['mobile'=>$mobile_no, 'otp'=>$otp, 'status'=>'completed'])->first();
				if($customerDetails)
				{
					$customerDetails->already_login=true;
					$status=true;
					$new_signup='';
					$error='Successfully Login';
					$this->set(compact('status', 'error', 'new_signup', 'customerDetails'));
					$this->set('_serialize', ['status', 'error', 'new_signup', 'customerDetails']);
				}
				else
				{
					$status=false;
					$error="Sorry, you entered Wrong OTP, try again";
					$this->set(compact('status', 'error'));
					$this->set('_serialize', ['status', 'error']);
				}
			}
			else if($signup=='incompleted'){
				
			$customerDetails = $this->Customers->find()->where(['mobile'=>$mobile_no, 'otp'=>$otp, 'status'=>'incompleted'])->first();
				if($customerDetails)
				{
					$customerDetails = $this->Customers->get($customerDetails->id);
				    $customerDetails->status='completed';
					$customerDetails->notification_key='AAAAXmNqxY4:APA91bG0X6RHVhwJKXUQGNSSCas44hruFdR6_CFd6WHPwx9abUr-WsrfEzsFInJawElgrp24QzaE4ksfmXu6kmIL6JG3yP487fierMys5byv-I1agRtMPIoSqdgCZf8R0iqsnds-u4CU';
					$customerDetails->referral_code=$customerDetails->id;
					$this->Customers->save($customerDetails);
					$customerDetails->already_login=false;
					$status=true;
					$new_signup='';
					$error='Successfully Login';
					$this->set(compact('status', 'error', 'new_signup', 'customerDetails'));
					$this->set('_serialize', ['status', 'error', 'new_signup', 'customerDetails']);
				}
				else
				{
					$status=false;
					$error="Sorry, you entered Wrong OTP, try again";
					$this->set(compact('status', 'error'));
					$this->set('_serialize', ['status', 'error']);
				}
			}
			
			
		}
		else
		{
			$status=false;
			$error="Enter Mobile No.";
			$this->set(compact('status', 'error'));
		    $this->set('_serialize', ['status', 'error']);
			
		}
    }
	
	public function profileEdit()
    {
		$customer_id=$this->request->data('customer_id');
		$name=$this->request->data('name');
		$mobile=$this->request->data('mobile');
		$email=$this->request->data('email');
		$fetchs=$this->Customers->find()->where(['Customers.id !=' => $customer_id, 'Customers.mobile' =>$mobile])->count();
		if(empty($fetchs)){
			$query = $this->Customers->query();
				$result = $query->update()
                    ->set([ 'name' => $name,
							'mobile' => $mobile,
							'email' => $email
							])
					->where(['id' => $customer_id])
					->execute();
		$profiles=$this->Customers->find()->where(['id' => $customer_id])->first();
		
		
		$query = $this->Customers->JainCashPoints->find();
		$totalInCase = $query->newExpr()
			->addCase(
				$query->newExpr()->add(['order_id' => '0']),
				$query->newExpr()->add(['point']),
				'integer'
			);
		$totalOutCase = $query->newExpr()
			->addCase(
				$query->newExpr()->add(['order_id' => '0']),
				$query->newExpr()->add(['used_point']),
				'integer'
			);
			$query->select([
			'total_in' => $query->func()->sum($totalInCase),
			'total_out' => $query->func()->sum($totalOutCase),'id','customer_id'
		])
		->where(['JainCashPoints.customer_id' => $customer_id])
		->group('customer_id')
		->autoFields(true);
		foreach($query as $fetch_query)
		{
			$points=$fetch_query->total_in;
			$used_points=$fetch_query->total_out;
			$jain_cash_points=$points-$used_points;
		}
		
		
		$queryw = $this->Customers->Wallets->find();
		$totalInCasew = $queryw->newExpr()
			->addCase(
				$queryw->newExpr()->add(['order_id' => '0']),
				$queryw->newExpr()->add(['advance']),
				'integer'
			);
		$totalOutCasew = $queryw->newExpr()
			->addCase(
				$queryw->newExpr()->add(['plan_id' => '0']),
				$queryw->newExpr()->add(['consumed']),
				'integer'
			);
			$queryw->select([
			'total_in' => $queryw->func()->sum($totalInCasew),
			'total_out' => $queryw->func()->sum($totalOutCasew),'id','customer_id'
		])
		->where(['Wallets.customer_id' => $customer_id])
		->group('customer_id')
		->autoFields(true);
		foreach($queryw as $fetch_query)
		{
			$advance=$fetch_query->total_in;
			$consumed=$fetch_query->total_out;
			$wallet_balance=$advance-$consumed;
		}
		
		$status=true;
		$error="";
        $this->set(compact('status', 'error','jain_cash_points','wallet_balance','profiles'));
        $this->set('_serialize', ['status', 'error','jain_cash_points','wallet_balance','profiles']);
		}
		else if(!empty($fetchs)){
			$status=false;
			$error="Please Try Another Number";
			$this->set(compact('status', 'error'));
			$this->set('_serialize', ['status', 'error']);
		}
    }
	
	public function MyAccount()
    {
		$customer_id=$this->request->query('customer_id');
		$profiles=$this->Customers->find()->where(['id' => $customer_id])->first();
	    
		$query = $this->Customers->JainCashPoints->find();
		$totalInCase = $query->newExpr()
			->addCase(
				$query->newExpr()->add(['order_id' => '0']),
				$query->newExpr()->add(['point']),
				'integer'
			);
		$totalOutCase = $query->newExpr()
			->addCase(
				$query->newExpr()->add(['order_id' => '0']),
				$query->newExpr()->add(['used_point']),
				'integer'
			);
			$query->select([
			'total_in' => $query->func()->sum($totalInCase),
			'total_out' => $query->func()->sum($totalOutCase),'id','customer_id'
		])
		->where(['JainCashPoints.customer_id' => $customer_id])
		->group('customer_id')
		->autoFields(true);
		foreach($query as $fetch_query)
		{
			$points=$fetch_query->total_in;
			$used_points=$fetch_query->total_out;
			$jain_cash_points=$points-$used_points;
		}
		
		
		$queryw = $this->Customers->Wallets->find();
		$totalInCasew = $queryw->newExpr()
			->addCase(
				$queryw->newExpr()->add(['order_id' => '0']),
				$queryw->newExpr()->add(['advance']),
				'integer'
			);
		$totalOutCasew = $queryw->newExpr()
			->addCase(
				$queryw->newExpr()->add(['plan_id' => '0']),
				$queryw->newExpr()->add(['consumed']),
				'integer'
			);
			$queryw->select([
			'total_in' => $queryw->func()->sum($totalInCasew),
			'total_out' => $queryw->func()->sum($totalOutCasew),'id','customer_id'
		])
		->where(['Wallets.customer_id' => $customer_id])
		->group('customer_id')
		->autoFields(true);
		foreach($queryw as $fetch_query)
		{
			$advance=$fetch_query->total_in;
			$consumed=$fetch_query->total_out;
			$wallet_balance=$advance-$consumed;
		}
		
		$status=true;
		$error="";
        $this->set(compact('status', 'error','jain_cash_points','wallet_balance','profiles'));
        $this->set('_serialize', ['status', 'error','jain_cash_points','wallet_balance','profiles']);
    }
	public function PushTokenUpdate()
    {
		$customer_id=$this->request->data('customer_id');
		$device_token=$this->request->data('device_token');
		
		$query = $this->Customers->query();
				$result = $query->update()
                    ->set([ 'device_token' => $device_token
							])
					->where(['id' => $customer_id])
					->execute();
		
		$status=true;
		$error="Token Updated Successfully";
        $this->set(compact('status', 'error'));
        $this->set('_serialize', ['status', 'error']);
    }
	
}
