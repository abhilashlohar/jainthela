<div style="">
	<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
		<table class=" table-striped table-condensed table-hover  scroll" width="100%" border="0">
			<thead>
				<tr style="background-color:#fff; color:#000;">
					<td align="left" colspan="5">
						<b>
						<?php foreach($order as $order_detail){
								$order_id=$order_detail->id;
								$order_no=$order_detail->order_no;
							
						} ?>
							Customer Order No.: <?= h(@$order_no) ?>
						</b>
					</td>
				</tr>
				</thead>
				<tbody>
					<tr>
						<td>
							<div class="portlet-body">
						<?= $this->Form->create($order,['id'=>'form_sample_3']) ?>
						 
							<div class="col-md-4">
								<label class=" control-label">Reason <span class="required" aria-required="true">*</span></label>
								<?php echo $this->Form->control('reason_id',['options' => $CancelReasons, 'class'=>'form-control input-sm','label'=>false,'type'=>'radio']); ?>
							</div>
							 
						<br/>
						<?= $this->Form->button($this->Html->tag('i', '', ['class'=>'fa fa-plus']) . __(' Submit'),['class'=>'btn btn-success']); ?>
						<?= $this->Form->end() ?>
					</div>
				</td>
			</tr>
				</tbody>
		</table>
</div>