<div class="row">
	<div class="col-sm-12">
		<div class="ibox float-e-margins">
			<div class="ibox-title">
				<h5>Billing Logs (<?php echo number_format($oBillingLogs->count); ?>)</h5>
			</div>
			<div class="ibox-content">
				<p>s=<a href="<?php echo site_url('subscriptions/admin_list_billings?s=c');
					?>">c</a>,<a href="<?php echo site_url('subscriptions/admin_list_billings?s=ca');
					?>">ca</a>,<a href="<?php echo site_url('subscriptions/admin_list_billings?s=cd');
					?>">cd</a>,<a href="<?php echo site_url('subscriptions/admin_list_billings?s=li');
					?>">li</a>,<a href="<?php echo site_url('subscriptions/admin_list_billings?s=lia');
					?>">lia</a>,<a href="<?php echo site_url('subscriptions/admin_list_billings?s=lid');
					?>">lid</a>,<a href="<?php echo site_url('subscriptions/admin_list_billings?s=m');
					?>">m</a>,<a href="<?php echo site_url('subscriptions/admin_list_billings?s=ma');
					?>">ma</a>,<a href="<?php echo site_url('subscriptions/admin_list_billings?s=md');
					?>">md</a></p>
				<table class="table table-hover">
					<thead>
					<tr>
						<th>billing_log_id</th>
						<th>member_id<?php if (!empty($aMembers)): ?> / email<?php endif; ?></th>
						<th>subscription_id</th>
						<th>create_date</th>
						<th>amount</th>
						<th>status</th>
					</tr>
					</thead>
					<tbody>
					<?php if ($oBillingLogs->count):
						foreach ($oBillingLogs as $oBillingLog): ?>
							<tr>
								<td><?php echo $oBillingLog->billing_log_id; ?></td>
								<td><?php echo $oBillingLog->member_id; if (!empty($aMembers[$oBillingLog->member_id])) { echo ' / '.$aMembers[$oBillingLog->member_id]; } ?></td>
								<td><?php echo $oBillingLog->subscription_id; ?></td>
								<td><?php //echo $oBillingLog->create_date; ?><?php echo convertto_local_datetime($oBillingLog->create_date,$time_zone,'%x %X'); ?></td>
								<td><?php echo $oBillingLog->amount; ?></td>
								<td><?php echo $oBillingLog->readable_status; ?></td>
							</tr>
						<?php endforeach;
					endif; ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
