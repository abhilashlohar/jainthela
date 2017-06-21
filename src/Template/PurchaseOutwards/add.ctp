<div class="row">
		<div class="col-md-12">
			<div class="portlet">
		<div class="portlet-body"> 
			<?= $this->Form->create($purchaseOutward,['id'=>'form_sample_3']) ?>
				<div class="portlet light bordered">
					<div class="portlet-title">
						<div class="caption">
							<span>
							<B>Purchase Order Outward</B>
							</span>
						</div>
					</div>
					<div class="portlet-body form">
					<!-- BEGIN FORM-->
							<div class="row">
								<div class="col-md-12">
									<div class="col-md-4">
										<label class="col-md-6 control-label">Vendors <span class="required" 	aria-required="true">*</span></label>
										<?= $this->Form->input('vendor_id',array('options' => $vendors,'class'=>'form-control input-sm select2me','empty' => 'Select','label'=>false)) ?>
									</div>
									
									<div class="col-md-2">
										<label class="control-label">Date <span class="required" aria-require>*</span></label>
										
										<?php echo $this->Form->control('transaction_date',['placeholder'=>'dd-mm-yyyy','class'=>'form-control input-sm date-picker','data-date-format'=>'dd-mm-yyyy','label'=>false,'type'=>'text','value'=>date('d-m-Y')]); ?>
										
									</div>
								 </div>
								 <div class="col-md-12"><br></div>
							</div>
						<!-- END FORM-->
						<table id="main_table" class="table table-condensed table-bordered">
							<thead>
								<tr align="center">
									<td width="7%">
										<label>Sr<label>
									</td>
									<td width="27%">
										<label>Item<label>
									</td>
									<td width="10%">
										<label>Available Stock<label>
									</td>
									<td width="20%">
										<label>Quantity<label>
									</td>
									<td width="20%">
										<label>Rate<label>
									</td>
									<td width="20%">
										<label>Amount<label>
									</td>
									<td></td>
								</tr>
							</thead>
							<tbody id='main_tbody' class="tab">
								
							</tbody>
							<tfoot>
								<tr>
									<td>
										<button type="button" class="add btn btn-default input-sm"><i class="fa fa-plus"></i> Add row</button>
									</td>
									<td colspan="4" style="text-align:right !important;">
										<label class="control-label" >Grand Total</label>
									</td>
									<td>
										<div class="form-group">
											<?= $this->Form->input('total_amount',['class'=>'form-control input-sm grnd_ttl','label'=>false,'placeholder'=>'Grand Total','value'=>0]) ?>
										</div>
									</td>
									<td></td>
								</tr>
							</tfoot>
						</table>
						<div class="row" style="padding-top:5px;">
							<div class="col-md-4"></div>
							<div class="col-md-4"></div>
							<div class="col-md-4"> </div>
						</div>
						<div align="center">
							<?= $this->Form->button($this->Html->tag('i', '', ['class'=>'fa fa-minus']) . __(' Purchase Outward'),['class'=>'btn btn-success','id'=>'submitbtn']); ?>
						</div>
					</div>
				</div>
			<?= $this->Form->end() ?>
		</div>
	</div>
</div>
    <div class="col-md-1">
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
				warehouses_id:{
					required: false
				},
				driver_id:{
					required: true
				},
				transaction_date:{
					required: false
				}
			},

		errorPlacement: function (error, element) { // render error placement for each input type
			if (element.parent(".input-group").size() > 0) {
				error.insertAfter(element.parent(".input-group"));
			} else if (element.attr("data-error-container")) {
				error.appendTo(element.attr("data-error-container"));
			} else if (element.parents('.radio-list').size() > 0) {
				error.appendTo(element.parents('.radio-list').attr("data-error-container"));
			} else if (element.parents('.radio-inline').size() > 0) {
				error.appendTo(element.parents('.radio-inline').attr("data-error-container"));
			} else if (element.parents('.checkbox-list').size() > 0) {
				error.appendTo(element.parents('.checkbox-list').attr("data-error-container"));
			} else if (element.parents('.checkbox-inline').size() > 0) {
				error.appendTo(element.parents('.checkbox-inline').attr("data-error-container"));
			} else {
				error.insertAfter(element); // for other inputs, just perform default behavior
			}
		},

		invalidHandler: function (event, validator) { //display error alert on form submit   
			success3.hide();
			error3.show();
		},

		highlight: function (element) { // hightlight error inputs
		   $(element)
				.closest('.form-group').addClass('has-error'); // set error class to the control group
		},

		unhighlight: function (element) { // revert the change done by hightlight
			$(element)
				.closest('.form-group').removeClass('has-error'); // set error class to the control group
		},

		success: function (label) {
			label
				.closest('.form-group').removeClass('has-error'); // set success class to the control group
		},

		submitHandler: function (form) {
			$('#submitbtn').prop('disabled', true);
			$('#submitbtn').text('Submitting.....');
			success3.show();
			error3.hide();
			form[0].submit(); // submit the form
		}

	});
	//--	 END OF VALIDATION

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

	$('.delete-tr').live('click',function() 
	{
		var rowCount = $('#main_table tbody#main_tbody tr').length; 
		if(rowCount>1)
		{
			$(this).closest('tr').remove();
			 
		}
		
    });

	$('.add').click(function(){
			add_row();
	});
		
	add_row();
	
	function add_row(){
			var tr=$("#sample_table tbody tr.main_tr").clone();
			$("#main_table tbody#main_tbody").append(tr); 
			rename_rows();
		}

	function rename_rows(){
		var i=0;
		$("#main_table tbody#main_tbody tr.main_tr").each(function(){ 
			$(this).find('td:nth-child(1)').html(i+1);
			$(this).find("td:nth-child(2) select").select2().attr({name:"purchase_outward_details["+i+"][item_id]", id:"purchase_outward_details-"+i+"-item_id"}).rules('add', {
						required: true
					});
			$(this).find("td:nth-child(4) input").attr({name:"purchase_outward_details["+i+"][quantity]", id:"purchase_outward_details-"+i+"-quantity"}).rules('add', {
						required: true
					}); 
			$(this).find("td:nth-child(5) input").attr({name:"purchase_outward_details["+i+"][rate]", id:"purchase_outward_details-"+i+"-rate"}).rules('add', {
						required: true
					}); 
			$(this).find("td:nth-child(6) input").attr({name:"purchase_outward_details["+i+"][amount]", id:"purchase_outward_details-"+i+"-amount"}).rules('add', {
						required: true
					}); 			
			i++;
		});
	}
	
	$(".calculation_amount").die().live('keyup',function(){
		calculation();				
	});	
	
	function calculation(){
		var grand_total = 0;		
		$("#main_table tbody#main_tbody tr.main_tr").each(function(){
			var amount =0;
			var quantity = parseFloat($(this).find("td:nth-child(4) input").val());
			if(!quantity){ quantity=0; }
			var price = parseFloat($(this).find("td:nth-child(5) input").val());
			if(!price){ price=0; }
			amount = quantity*price;
			grand_total=grand_total+amount;
			$(this).find("td:nth-child(6) input").val(amount.toFixed(2));
			var total_amount = $(this).find("td:nth-child(6) input").val();
		}); 
		$(".grnd_ttl").val(grand_total.toFixed(2));
	}

	$(document).on('keyup', '.number', function(e)
    { 
		var mdl=$(this).val();
		var numbers =  /^[0-9]*\.?[0-9]*$/;
		if(mdl.match(numbers))
		{
		}
		else
		{
			$(this).val('');
			return false;
		}
    });
	
	$('.itm_chng').die().live('change',function() 
	{ 
		$('#data').html('<i style= "margin-top: 20px;" class="fa fa-refresh fa-spin fa-3x fa-fw"></i><b> Loading... </b>');
		var update = $(this);
		var itm_val = $(this).val();
		var ware_house = $(".ware_house").val();
 		var m_data = new FormData();
		m_data.append('itm_val',itm_val);
		m_data.append('ware_house',ware_house);
			
		$.ajax({
			url: "<?php echo $this->Url->build(["controller" => "ItemLedgers", "action" => "ajax_stock_available"]); ?>",
			data: m_data,
			processData: false,
			contentType: false,
			type: 'POST',
			dataType:'text',
			success: function(data)   // A function to be called if request succeeds
			{
				$(update).closest('tr').find('.stock_available').html(data);
				$(update).closest('tr').find('.valid').attr('max',data);
			}
		});	
	});
});

</script>
		<table id="sample_table" style="display:none;" cellpadding="5" cellspacing="5">
			<tbody>
				<tr class="main_tr" class="tab">
					<td align="center" width="1px"></td>
				    <td>
						<?= $this->Form->input('item_id[]',array('options' => $items,'class'=>'form-control input-sm itm_chng','empty' => 'Select','label'=>false)) ?>
					</td>
					<td class="stock_available" align="center">
						
					</td>
					<td>
						<?php echo $this->Form->input('quantity[]', ['label' => false,'class' => 'form-control input-sm number valid','placeholder'=>'Quantity','value'=>0]); ?>	
					</td>
					<td>
						<?php echo $this->Form->input('rate[]', ['label' => false,'class' => 'calculation_amount form-control input-sm number valid','placeholder'=>'Rate','value'=>0]); ?>	
					</td>
					<td>
						<?php echo $this->Form->input('amount[]', ['label' => false,'class' => 'form-control input-sm number valid','placeholder'=>'Amount','value'=>0]); ?>	
					</td>
                    <td>
						<a class="btn btn-default delete-tr input-sm" href="#" role="button" style="margin-bottom: 1px;"><i class="fa fa-times"></i></a>
					</td>
				</tr>
			</tbody>
		</table>