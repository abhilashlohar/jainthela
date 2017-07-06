<style>
.table>thead>tr>th{
	font-size:12px !important;
}
</style>
<div class="row">
	<div class="col-md-12">
		<div class="portlet light bordered">
			<div class="portlet-title">
				<div class="caption">
					<i class="font-purple-intense"></i>
					<span class="caption-subject font-purple-intense ">
						<i class="fa fa-book"></i> Order</span>
				</div>
				<div class="actions">
					<?php echo $this->Html->link('<i class="fa fa-plus"></i> Add new','/Orders/Add/Offline',['escape'=>false,'class'=>'btn btn-default']) ?>
					<input type="text" class="form-control input-sm pull-right" placeholder="Search..." id="search3" style="width: 200px;">
				</div>
			</div>
			<div class="portlet-body">
				<table class="table table-condensed table-hover table-bordered" id="main_tble">
						<thead>
						<tr>
							<th scope="col">Sr. No.</th>
							<th scope="col">Order No.</th>
							<th scope="col">Customer Name</th>
							<th scope="col">wallet Amount</th>
							<th scope="col">Grand Total</th>
							<th scope="col">Order Type</th>
							<th scope="col">Order Date</th>
							<th scope="col">Status</th>
							<th scope="col" class="actions"><?= __('Actions') ?></th>
						</tr>
					</thead>
					<tbody>
            <?php $sr_no=0; foreach ($orders as $order): ?>
            <tr>
                <td><?= ++$sr_no ?></td>
				<td><?= h('#'.str_pad($this->Number->format($order->order_no), 4, '0', STR_PAD_LEFT)) ?></td>
                <td>
					<?php 
						$customer_name=$order->customer->name;
						$customer_mobile=$order->customer->mobile;
					?>
					<?= h($customer_name.' ('.$customer_mobile.')') ?>
				</td>
                <td><?= $this->Number->format($order->amount_from_wallet) ?></td>
                <td><?= $this->Number->format($order->total_amount) ?></td>
                <td><?= h($order->order_type) ?></td>
                <td><?= h($order->order_date) ?></td>
                <td><?= h($order->status) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['action' => 'view', $order->id]) ?>
                   <!-- <?= $this->Html->link(__('Edit'), ['action' => 'edit', $order->id]) ?>-->
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
				</table>
			</div>
			
		</div>
	</div>
</div>
<?php echo $this->Html->script('/assets/global/plugins/jquery.min.js'); ?>
<script>
var $rows = $('#main_tble tbody tr');
	$('#search3').on('keyup',function() {
		var val = $.trim($(this).val()).replace(/ +/g, ' ').toLowerCase();
		var v = $(this).val();
		if(v){ 
			$rows.show().filter(function() {
				var text = $(this).text().replace(/\s+/g, ' ').toLowerCase();
	
				return !~text.indexOf(val);
			}).hide();
		}else{
			$rows.show();
		}
	});
</script>