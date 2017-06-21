<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * PushNotifications Controller
 *
 * @property \App\Model\Table\PushNotificationsTable $PushNotifications
 *
 * @method \App\Model\Entity\PushNotification[] paginate($object = null, array $settings = [])
 */
class PushNotificationsController extends AppController
{

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
		$this->viewBuilder()->layout('index_layout');
       
    }
	

	public function home()
    {
		$this->viewBuilder()->layout('index_layout');
        $pushNotification = $this->PushNotifications->newEntity();
		$customers = $this->PushNotifications->Customers->find();
		$page = $this->request->getQuery('page');
		if($page=="home")
		{
		$deepLinks = $this->PushNotifications->DeepLinks->find()->where(['id'=>1])->first();}
		if($page=="bulkbooking")
		{
		$deepLinks = $this->PushNotifications->DeepLinks->find()->where(['id'=>2])->first();}
		if($page=="referfriend")
		{
		$deepLinks = $this->PushNotifications->DeepLinks->find()->where(['id'=>3])->first();}
		if($page=="addmoney")
		{
		$deepLinks = $this->PushNotifications->DeepLinks->find()->where(['id'=>4])->first();}
		if($page=="viewcart")
		{
		$deepLinks = $this->PushNotifications->DeepLinks->find()->where(['id'=>5])->first();}
		if($page=="specialoffers")
		{
		$deepLinks = $this->PushNotifications->DeepLinks->find()->where(['id'=>6])->first();}
		
        if ($this->request->is('post'))
			{
			$pushNotification = $this->PushNotifications->patchEntity($pushNotification, $this->request->data);
            $file = $this->request->data['image'];
			$ext = substr(strtolower(strrchr($file['name'], '.')), 1); //get the extension
            $arr_ext = array('jpg', 'jpeg', 'png'); //set allowed extensions
            $setNewFileName = uniqid();
            $pushNotification->image = $setNewFileName . '.' . $ext;
			if (in_array($ext, $arr_ext))
				{
					move_uploaded_file($file['tmp_name'], WWW_ROOT . '/Notify_images/' . $setNewFileName . '.' . $ext);
				}
			$pushNotification->link_url = $deepLinks->link_url;
			if ($this->PushNotifications->save($pushNotification))
			  {
			   foreach($customers as $customer)
				{
					$pushNotificationCustomer = $this->PushNotifications->PushNotificationCustomers->newEntity(); 
					$pushNotificationCustomer->customer_id =$customer->id;
					$pushNotificationCustomer->push_notification_id =$pushNotification->id;
					$this->PushNotifications->PushNotificationCustomers->save($pushNotificationCustomer);
				}
				$id=$pushNotification->id;
				$this->Flash->success(__('The push notification saved.'));
			 $this->redirect(['action' => 'sendProgress/' . $id]);
			} 
			else {
				$this->Flash->error(__('The push notification could not be saved. Please, try again.'));
				}
			}
			$this->set('page', $page);
		$this->set('pushNotification', $pushNotification);
        $this->set('_serialize', ['pushNotification']);
		
	}
	
	public function sendProgress($id = null)
    {
		$this->viewBuilder()->layout('index_layout');
		$this->set('id', $id);   
    }
	public function checkNotify($id)
    {
		
		$this->viewBuilder()->layout(null);
		$pushNotifications = $this->PushNotifications->PushNotificationCustomers->find()->where(['sent'=>0,'push_notification_id'=>$id])->contain(['Customers'])->limit(1);
		$pushNotifications_data = $this->PushNotifications->find()->where(['id'=>$id])->first();;
		foreach($pushNotifications as $pushNotification)
		{
			    $API_ACCESS_KEY=$pushNotification->customer->notification_key;
				$device_token=$pushNotification->customer->device_token;
				  $device_token1=rtrim($device_token);
                if(!empty($device_token))
					$msg = array
							(
							'message'     =>$pushNotifications_data->message,
							'link'    => $pushNotifications_data->link_url,
							'notification_id'    => 1,
							);
							
						$url = 'https://fcm.googleapis.com/fcm/send';
						$fields = array
						(
							'registration_ids'     => array($device_token),
							'data'            => @$msg,
						);
						$headers = array
						(
							'Authorization: key=' .$API_ACCESS_KEY,
							'Content-Type: application/json'
						);

						  json_encode($fields);
						  $ch = curl_init();
						curl_setopt($ch, CURLOPT_URL, $url);
						curl_setopt($ch, CURLOPT_POST, true);
						curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
						curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
						curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
						curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
						curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
						$result = curl_exec($ch);
						if ($result === FALSE) {
							//die('FCM Send Error: ' . curl_error($ch));
						}
						curl_close($ch);
						$l[]=$result;
						//return $l;  
						
						$query = $this->PushNotifications->PushNotificationCustomers->query();
						$query->update()
							->set(['sent'=>1])
							->where(['id' => $pushNotification->id])
							->execute();
		}
		$total_records = $this->PushNotifications->PushNotificationCustomers->find()->where(['push_notification_id'=>$id])->count();
		$total_converted_records = $this->PushNotifications->PushNotificationCustomers->find()->where(['sent'=>1,'push_notification_id'=>$id])->count();
		$converted_per=($total_converted_records*100)/$total_records;
		    if($converted_per==100){ $again_call_ajax="NO";
                                   }
									else{$again_call_ajax="YES";}
			die(json_encode(array("again_call_ajax"=>$again_call_ajax,"converted_per"=>$converted_per)));
 
   } 

    /**
     * View method
     *
     * @param string|null $id Push Notification id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $this->viewBuilder()->layout('index_layout');
		$pushNotification = $this->PushNotifications->get($id, [
            'contain' => ['PushNotificationCustomer']
        ]);

        $this->set('pushNotification', $pushNotification);
        $this->set('_serialize', ['pushNotification']);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $pushNotification = $this->PushNotifications->newEntity();
        if ($this->request->is('post')) {
            $pushNotification = $this->PushNotifications->patchEntity($pushNotification, $this->request->getData());
            if ($this->PushNotifications->save($pushNotification)) {
                $this->Flash->success(__('The push notification has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The push notification could not be saved. Please, try again.'));
        }
        $this->set(compact('pushNotification'));
        $this->set('_serialize', ['pushNotification']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Push Notification id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $pushNotification = $this->PushNotifications->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $pushNotification = $this->PushNotifications->patchEntity($pushNotification, $this->request->getData());
            if ($this->PushNotifications->save($pushNotification)) {
                $this->Flash->success(__('The push notification has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The push notification could not be saved. Please, try again.'));
        }
        $this->set(compact('pushNotification'));
        $this->set('_serialize', ['pushNotification']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Push Notification id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $pushNotification = $this->PushNotifications->get($id);
        if ($this->PushNotifications->delete($pushNotification)) {
            $this->Flash->success(__('The push notification has been deleted.'));
        } else {
            $this->Flash->error(__('The push notification could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}