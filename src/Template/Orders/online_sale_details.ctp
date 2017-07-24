<div class="portlet light bordered">
	<div class="portlet-title">
		<div class="caption">
			<i class="icon-globe font-blue-steel"></i>
			<span class="caption-subject font-blue-steel ">Online Sale For "<?php foreach ($onlineSales as $onlineSale){ echo $onlineSale->item->name; break; } ?>"</span>
		</div>
		<div class="portlet-body">
		
		<form method="GET" >
				<table width="50%" class="table table-condensed">
					<tbody>
						<tr>
							<td width="2%">
							<?php if(!empty($from_date)){ ?>
								<input type="text" name="From" class="form-control input-sm date-picker" placeholder="Transaction From" value="<?php echo @date('d-m-Y', strtotime($from_date));  ?>"  data-date-format="dd-mm-yyyy">
							<?php }else{ ?>
								<input type="text" name="From" class="form-control input-sm date-picker" placeholder="Transaction From" value="<?php echo date('01-m-Y');  ?>"  data-date-format="dd-mm-yyyy">
							<?php } ?>	
							</td>	
							<td width="2%">
							<?php if(!empty($to_date)){ ?>
								<input type="text" name="To" class="form-control input-sm date-picker" placeholder="Transaction To" value="<?php echo @date('d-m-Y', strtotime($to_date));  ?>"  data-date-format="dd-mm-yyyy" >
							<?php }else{ ?>
								<input type="text" name="To" class="form-control input-sm date-picker" placeholder="Transaction To" value="<?php echo date('d-m-Y');  ?>"  data-date-format="dd-mm-yyyy" >
							<?php } ?>	
							</td>
							<td width="10%">
								<button type="submit" class="btn btn-primary btn-sm"><i class="fa fa-filter"></i> Filter</button>
							</td>
						</tr>
					</tbody>
				</table>
			</form>
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
						<?php $i=1; foreach($onlineSales as $onlineSale){ ?> 
						<tr>
							<td><?= h($i++) ?></td>
							<td><?= h(@$onlineSale->order->order_no) ?></td>
							<td><?= h(@$onlineSale->quantity).$onlineSale->item->unit->unit_name ?></td>
						</tr>
						<?php } ?>
					</tbody>
				</table>
				
				</div>
			</div>
		</div>
	</div>
</div>

