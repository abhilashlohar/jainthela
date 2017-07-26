<div class="portlet light bordered">
	<div class="portlet-title">
		<div class="caption">
			<i class="icon-globe font-blue-steel"></i>
			<span class="caption-subject font-blue-steel ">Bulk Sale For "<?php foreach ($bulkSales as $bulkSale){ echo $bulkSale->item->name.'('.$bulkSale->item->alias_name.')'; break; } ?>"</span>
		</div>
		<div class="portlet-body">
		
			<div class="row">
				<div class="col-md-12">
				<table class="table table-bordered table-striped table-hover">
					<thead>
						<tr>
							<th>Sr. No.</th>
							<th>Order No</th>
							<th>Quantity</th>
						</tr>
					</thead>
					<tbody>
						<?php $unit; $total=0; $i=1; foreach($bulkSales as $bulkSale){ ?> 
						<tr>
							<td><?= h($i++) ?></td>
							<td><?= h(@$bulkSale->order->order_no) ?></td>
							<td><?= h(@$bulkSale->quantity).$bulkSale->item->unit->unit_name;
							@$total+=@$bulkSale->quantity; 
							@$unit = @$bulkSale->item->unit->unit_name;?></td>
						</tr>
						<?php } ?>
						<tr>
							<td colspan="2" align="right"><b>Total</b></td>
							<td><b><?php  echo $this->Number->format(@$total).@$unit ?></b></td>
						</tr>
					</tbody>
				</table>
				
				</div>
			</div>
		</div>
	</div>
</div>

