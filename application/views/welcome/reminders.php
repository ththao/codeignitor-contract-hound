<?php
$i5Days = strtotime('+5 days'); 
$i21Days = strtotime('+21 days'); 
foreach ($oReminders as $oReminder):
	$iAlertDate = strtotime($oReminder->alert_date); ?>
	<div class="reminder <?php echo !$oReminder->status ? 'expired-reminder reminder-default hide' : (($iAlertDate < $i5Days) ? 'active-reminder reminder-danger' : (($iAlertDate < $i21Days) ? 'active-reminder reminder-warning' : 'active-reminder reminder-default')); ?>">
		<div class="reminder-content">
			<a href="/contracts/view/<?php echo $oReminder->contract_id; ?>" class="reminder-time"><?php //echo date('n/j/Y',$iAlertDate); ?><?php echo convertto_local_datetime($iAlertDate,$time_zone,'%x',true); ?></a>
			<a href="/contracts/view/<?php echo $oReminder->contract_id; ?>" class="reminder-body">
				<h6><?php echud($oReminder->name); ?></h6>
				<p><?php echud($oReminder->message); ?></p>
			</a>
			<a href="/reminders/dismiss/<?php echo $oReminder->reminder_id; ?>" class="reminder-actions"><span data-icon="close-small">Dismiss</span></a>
		</div>
	</div>
<?php endforeach; ?>