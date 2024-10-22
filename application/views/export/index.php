	<div class="layout-section">
		<h4>Contract Details Export</h4>
		<p class="help-block">
			You can export your contracts and custom fields for contracts that do not have
			an end date.</p>
		<p class="help-block">
			<a class="btn btn-success" href="<?php echo site_url('export/export'); ?>">Export Now</a>
			<?php if (!empty($iLastExported)) { echo "Last Exported: ".convertto_local_datetime($iLastExported,$time_zone,'%x');/*date('m-d-Y',$iLastExported);*/
			} ?>
		</p>
	</div>