<div class="row">
	<div class="col-md-5 col-sm-5">
		<div class="portlet light bordered">
			<div class="portlet-title">
				<div class="caption">
					<i class="font-purple-intense"></i>
					<span class="caption-subject font-purple-intense ">
						<?php 
						if(!empty($itemCategory->id)){ ?>
							<i class="fa fa-pencil-square-o"></i> Edit Item Category
						<?php }else{ ?>
							<i class="fa fa-plus"></i> Add Item Category
						<?php } ?>
					</span>
				</div>
				<div class="actions">
					<?php if(!empty($updt_id)){ ?>
						<?php echo $this->Html->link('<i class="fa fa-plus"></i> Add New',['action' => 'index'],array('escape'=>false,'class'=>'btn btn-default')); ?>
					<?php } ?>
				</div>
			</div>
			<div class="portlet-body">
				<?= $this->Form->create($itemCategory,['id'=>'form_sample_3']) ?>
				<div class="row">
					<div class="col-md-8">
						<label class=" control-label">Item Category <span class="required" aria-required="true">*</span></label>
						<?php echo $this->Form->control('name',['placeholder'=>'Category name','class'=>'form-control input-sm','label'=>false]); ?>
					</div>
				</div>
				<br/>
				<?= $this->Form->button($this->html->tag('i', '', ['class'=>'fa fa-plus']) . __(' Submit'),['class'=>'btn btn-success']); ?>
				<?= $this->Form->end() ?>
			</div>
		</div>
	</div>
	<div class="col-md-7 col-sm-7">
		<div class="portlet light bordered">
			<div class="portlet-title">
				<div class="caption">
					<i class=" fa fa-gift"></i>
					<span class="caption-subject">Item Categories</span>
				</div>
				<div class="actions">
					<input type="text" class="form-control input-sm pull-right" placeholder="Search..." id="search3"  style="width: 200px;">
				</div>
			</div>
			<div class="portlet-body">
				<div style="overflow-y: scroll;height: 400px;">
					<table class="table table-bordered table-condensed pagin-table" id="main_tble">
						<thead>
							<tr>
								<th><?=  h('Sr.no') ?></th>
								<th><?=  h('Category Name') ?></th>
								<th class="actions"><?= __('Actions') ?></th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ($itemCategories as $itemCategory):
								@$k++;
							?>
							<tr>
								<td><?= h($k) ?></td>
								<td><?= h($itemCategory->name) ?></td>
								<td class="actions">
								<?php echo $this->Html->link('<i class="fa fa-pencil-square-o"></i>',['action' => 'index', $itemCategory->id],['escape'=>false,'class'=>'btn btn-xs blue']); ?>
								</td>
							</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
<?php echo $this->Html->script('/assets/global/plugins/jquery.min.js'); ?>
<script>
$(document).ready(function() {
	
  //--------- FORM VALIDATION
	var form3 = $('#form_sample_3');
	var error3 = $('.alert-danger', form3);
	var success3 = $('.alert-success', form3);
	form3.validate({
		
		errorElement: 'span', //default input error message container
		errorClass: 'help-block help-block-error', // default input error message class
		focusInvalid: true, // do not focus the last invalid input
		rules: {
				name:{
					required: true,					 
				},
				unit_id:{
					required: true,
				}
			},

				}

		invalidHandler: function (event, validator) { //display error alert on form submit   
			success3.hide();
			error3.show();
		}

			//--	 END OF VALIDATION
	

	});
});
</script>
