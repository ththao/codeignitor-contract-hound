<div class="row">
	<div class="col-md-12">
		<p style="display:none;">
			Site Count: <?php echo number_format($iTotalSites); ?><br />
			Email Count: <?php echo number_format($iTotalEmails); ?><br />
			Type: <?php echo $oSub->readable_type; ?>
		</p>

		<?php foreach ($aPlansDetails as $iPlanId=>$aPlan): ?>
		<div class="col-md-4">
			<div class="panel">
				<div class="panel-heading">
					<h4 class="text-center"><?php echo $aPlan['name']; ?></h4>
				</div>
				<div class="panel-body text-center">
					<p class="lead">
						<strong>$<?php echo $aPlan['price']; ?> / month</strong></p>
				</div>
				<ul class="list-group list-group-flush text-center">
					<?php foreach ($aPlan['basics'] as $sBasic): ?>
						<li class="list-group-item"><i class="icon-ok text-danger"></i><?php echo $sBasic; ?></li>
					<?php endforeach; ?>
					<?php foreach ($aPlan['extras'] as $sExtra): ?>
						<li class="list-group-item"><i class="icon-ok text-danger"></i><?php echo $sExtra; ?></li>
					<?php endforeach; ?>
				</ul>
				<div class="panel-footer">
					<?php if ($iPlanId == $oSub->type): ?>
						<a class="btn btn-lg btn-block">Current Plan</a>
					<?php else: ?>
						<a class="btn btn-lg btn-block btn-success" href="<?php echo site_url('subscriptions/billing/'.$iPlanId); ?>">Continue</a>
					<?php endif; ?>
				</div>
			</div>
		</div>
		<?php endforeach; ?>
	</div>
</div>

<?php if ($oSub->type == SubscriptionModel::TypeFree): ?>
<div class="row">
	<div class="col-md-2 col-md-offset-10">
		<a href="<?php echo site_url('sites'); ?>">No thanks, continue to free version >></a>
	</div>
</div>
<?php endif; ?>

<?php /*
<div class="row">
	<div class="col-md-6">
		<?php if (!empty($sNextBillingDate)):
			if ($oSub->isActive()):
			?>Your next bill is <?php
			else: ?>Your subscription expires in <?php
			endif;
		echo strtolower(time_ago($sNextBillingDate,2)); ?>. <?php endif; ?>You can update
	your <?php echo anchor('subscriptions/billing','billing info here','class="btn btn-primary"'); ?>
	</div>
</div>
*/ ?>
