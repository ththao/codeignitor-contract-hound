<div class="row">
	<div class="col-sm-12">
		<div class="ibox float-e-margins">
			<div class="ibox-title">
				<h5>Subscriptions (<?php echo number_format($oSubs->count); ?>)</h5>
			</div>
			<div class="ibox-content">
				<ul>
					<li><a href="<?php echo site_url('subscriptions/admin_list_subs'); ?>">All</a></li>
					<li><a href="<?php echo site_url('subscriptions/admin_list_subs?v=p'); ?>">Paid</a></li>
					<li><a href="<?php echo site_url('subscriptions/admin_list_subs?v=pa'); ?>">Paid/Active</a></li>
					<li><a href="<?php echo site_url('subscriptions/admin_list_billings'); ?>">Billing Logs</a></li>
				</ul>
				<table class="table table-hover">
					<thead>
					<tr>
						<th>sub_id</th>
						<th>member_id<?php if (!empty($aMembers)): ?> / email<?php endif; ?></th>
						<th>create_date</th>
						<th>type</th>
						<th>status</th>
						<th>price</th>
						<th>start_date</th>
						<th>last_checked</th>
						<th>last_changed</th>
						<th>cancel_date</th>
						<th>expire_date</th>
						<th>next_billing_date</th>
					</tr>
					</thead>
					<tbody>
					<?php $iActiveSubTotal = 0; if ($oSubs->count):
						foreach ($oSubs as $oSub):
							if ($oSub->status == SubscriptionModel::StatusActive) { $iActiveSubTotal += $oSub->price;} ?>
							<tr>
								<td><?php echo $oSub->subscription_id; ?></td>
								<td><?php echo $oSub->member_id; if (!empty($aMembers[$oSub->member_id])) { echo ' / '.$aMembers[$oSub->member_id]; } ?></td>
								<td><?php //echo $oSub->create_date; ?><?php echo convertto_local_datetime($oLog->create_date,$time_zone,'%x %X'); ?></td>
								<td><?php echo $oSub->readable_type; ?></td>
								<td><?php echo lang($oSub->readable_status); ?></td>
								<td><?php echo ($oSub->price > 0)?$oSub->price:''; ?></td>
								<td><?php //echo $oSub->start_date; ?><?php echo convertto_local_datetime($oSub->start_date,$time_zone,'%x'); ?></td>
								<td><?php //echo $oSub->last_checked; ?><?php echo convertto_local_datetime($oSub->last_checked,$time_zone,'%x %X'); ?></td>
								<td><?php //echo $oSub->last_changed; ?><?php echo convertto_local_datetime($oSub->last_changed,$time_zone,'%x %X'); ?></td>
								<td><?php //echo $oSub->cancel_date; ?><?php echo convertto_local_datetime($oSub->cancel_date,$time_zone,'%x'); ?></td>
								<td><?php //echo $oSub->expire_date; ?><?php echo convertto_local_datetime($oSub->expire_date,$time_zone,'%x'); ?></td>
								<td><?php // echo $oSub->next_billing_date; ?><?php echo convertto_local_datetime($oSub->next_billing_date,$time_zone,'%x'); ?></td>
							</tr>
						<?php endforeach;
					endif; ?>
					</tbody>
					<tfoot>
						<tr>
							<td colspan="5">&nbsp;</td>
							<td colspan="7">$<?php echo number_format($iActiveSubTotal,2); ?> monthly active re-occurring</td>
						</tr>
					</tfoot>
				</table>
			</div>
		</div>
	</div>
</div>
