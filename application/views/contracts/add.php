<?php echo form_open_multipart('contracts/add','class="contact-form"'); ?>
<p>
	<?php echo form_label('Name','name'); ?>
	<?php echo form_input('name','','id="name" placeholder="Contract Name"'); ?>
</p>
<p>
	<?php echo form_label('Company','company'); ?>
	<?php echo form_input('company','','id="company" placeholder="Company"'); ?>
</p>
<p>
	<?php echo form_label('Value','valued'); ?>
	<?php echo form_input('valued','','id="valued" placeholder="Value (exp: $10,000)"'); ?>
</p>
<p>
	<?php echo form_label('Type','type'); ?>
	<select id="type" name="type">
		<option selected value="0">Buy Side</option>
		<option value="1">Sell Side</option>
	</select>
</p>
<p>
	<?php echo form_label('Start Date','start_date'); ?>
	<?php echo form_input('start_date','','id="start_date" placeholder="Start Date"'); ?>
</p>
<p>
	<?php echo form_label('End Date','end_date'); ?>
	<?php echo form_input('end_date','','id="end_date" placeholder="End Date"'); ?>
</p>
<?php echo my_form_upload('contract_file','','','Links CSV'); ?>
<?php echo form_submit('submit','Upload','class="button-like"'); ?>
<?php echo form_close(); ?>
