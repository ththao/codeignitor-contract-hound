<div class="layout-section">
	<div class="tabs">
		<div class="tabs-content">
			<div class="tabs-header">
				<h2>Billing</h2>
			</div>
			<div class="tabs-body">
				<ul class="nav nav-tabs">
					<li ng-class="{active:(settings_mode=='plans')}"><a href="#" ng-click="settings_mode='plans'">Plans</a></li>
					<li ng-class="{active:(settings_mode=='history')}"><a href="#" ng-click="settings_mode='history'">History</a></li>
					<li ng-class="{active:(settings_mode=='payment')}"><a href="#" ng-click="settings_mode='payment'">Payment Info</a></li>
				</ul>
			</div>
		</div>
	</div>

	<div ng-show="settings_mode=='plans'">
		<h4>Plans & Pricing</h4>

		<div class="plans">
			<div class="plan">
				<div class="plan-content">
					<div class="plan-header">
						<h2><?php echo $oSub->contract_limit; ?></h2>
						<p class="text-larger">
							<?php if ($oSub->status == SubscriptionModel::StatusTrial): ?>
							<strong>Trial expires on <?php //echo date('n/d/Y',strtotime($oSub->expire_date)); ?><?php echo convertto_local_datetime($oSub->expire_date,$time_zone,'%x'); ?></strong>
							<?php else: ?>
							<strong>$<?php echo number_format($oSub->price); ?></strong>
							<em>paid monthly</em>
							<?php endif; ?>
						</p>
					</div>
					<div class="plan-body">
						<ul class="text-large text-italic text-light">
							<li><strong>Contracts:</strong> Your account subscription lets you manage sharing and reminders on <?php echo number_format($oSub->contract_limit); ?> contracts with ease.</li>
						</ul>
					</div>
					<div class="plan-footer">
						<div class="notice notice-large">
							<div class="notice-content">
								<div class="notice-header">
									<div class="notice-header-text">
										<h4>Usage</h4>
										<p class="help-block"><?php echo $iPlanCount; ?> of <?php echo $oSub->contract_limit; ?> Contracts</p>
									</div>
								</div>
								<div class="notice-body">
									<div class="progress">
										<div class="progress-bar progress-bar-danger" style="width: <?php echo round(($iPlanCount/$oSub->contract_limit)*100); ?>%"></div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="plan plan-promoted">
				<div class="plan-content">
					<div class="plan-header">
						<h2>Upgrade</h2>
						<?php if ($oSub->next_billing_date): ?>
						<p class="text-larger">
							<strong>Next Cycle:</strong>
							<em><?php //echo date('m/d/Y',strtotime($oSub->next_billing_date)); ?><?php echo convertto_local_datetime($oSub->next_billing_date,$time_zone,'%x'); ?></em>
						</p>
						<?php endif; ?>
					</div>
					<div class="plan-body">
						<p class="text-large text-italic text-light">Add blocks of 50 contracts for $95 per month. Change your subscription at any time.</p>
					</div>
					<div class="plan-footer">
						<a href="/billing/upgrade" class="btn btn-primary btn-lg">Upgrade your Account</a>
					</div>
				</div>
			</div>
		</div>
									
		<h4>Questions about your account?</h4>
		<p class="help-block">If you have questions about upgrading and/or billing, contact us at <a href="mailto:support@contracthound.com">support@contracthound.com</a> or via chat by clicking the icon in the lower-right
.</p>

	</div>
	<div ng-show="settings_mode=='history'">
		<div class="divider divider-gap">
			<div class="divider-content">
				<div class="divider-title">
					<h6>Payment History</h6>
				</div>
				<div class="divider-separator">
					<hr/>
				</div>
				<div class="divider-actions">
					<div class="dropdown">
						<a href="#" class="text-light text-italic" data-toggle="dropdown">
							<span>View: </span><strong>Last 12 Months</strong>
							<span class="caret"></span>
						</a>
						<ul class="dropdown-menu dropdown-menu-right">
							<li><a href="#">All Time</a></li>
						</ul>
					</div>
				</div>
			</div>
		</div>
		<div class="table-responsive">
			<table class="table table-justified">
				<thead>
					<tr>
						<th>Date</th>
						<th>Plan</th>
						<th>Cost</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($oBillingLogs as $oBillingLog): ?>
					<tr>
						<td><?php //echo date('n/d/Y',strtotime($oBillingLog->create_date)); ?><?php echo convertto_local_datetime($oBillingLog->create_date,$time_zone,'%x'); ?></td>
						<th><?php echo number_format($oBillingLog->contract_limit); ?> Contracts</th>
						<td>$<?php echo number_format($oBillingLog->amount,2); ?></td>
					</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
	</div>
	<div ng-show="settings_mode=='payment'">
		<div class="divider divider-gap">
			<div class="divider-content">
				<div class="divider-title">
					<h6>Current Payment Methods</h6>
				</div>
				<div class="divider-separator">
					<hr/>
				</div>
			</div>
		</div>
		<div class="table-responsive table-alignment">
			<table class="table table-hover table-borderless table-justified">
				<thead>
					<tr>
						<th>Card</th>
						<th>Expiration</th>
						<th>Name</th>
						<th>Status</th>
						<th class="cell-small"></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($oBillingInfos as $oBillingInfo): ?>
					<tr>
						<th>#### #### #### <?php echo $oBillingInfo->cc_last_4; ?></th>
						<td><?php echo $oBillingInfo->cc_expire; ?></td>
						<td><?php echud($oBillingInfo->first_name.' '.$oBillingInfo->last_name); ?></td>
						<td><span class="label label-default label-compact"><?php 
							switch ($oBillingInfo->status) {
								case BillingInfoModel::STATUS_ACTIVE:
									echo '<span class="label label-success label-compact">Active</span>';
									break;
								case BillingInfoModel::STATUS_INVALID:
									echo '<span class="label label-danger label-compact">Failed</span>';
									break;
								case BillingInfoModel::STATUS_INACTIVE:
									echo '<span class="label label-default label-compact">Inactive</span>';
									break;
							} ?></span></td>
						<td class="cell-small dropdown" data-card-id="<?php echo $oBillingInfo->billing_info_id; ?>">
							<?php if ($oBillingInfo->status == BillingInfoModel::STATUS_INACTIVE): ?>
							<a href="#" class="cell-link text-light" data-toggle="dropdown"><span class="caret"></span></a>
							<ul class="dropdown-menu dropdown-menu-right">
								<li><a href="/billing/make_card_default/<?php echo $oBillingInfo->billing_info_id; ?>" class="make-active-card">Make active payment method</a></li>
								<li class="divider"></li>
								<li><a href="/billing/remove_payment_method/<?php echo $oBillingInfo->billing_info_id; ?>" class="remove-card">Remove payment method</a></li>
							</ul>
							<?php else: ?>
							&nbsp;
							<?php endif; ?>
						</td>
					</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
		<div class="divider divider-gap">
			<div class="divider-content">
				<div class="divider-title">
					<h6>Add New Payment Method</h6>
				</div>
				<div class="divider-separator">
					<hr/>
				</div>
			</div>
		</div>
		<div class="form-grid form-grid-large form-grid-fixed" ng-click="card_editing=true">
			<form method="post" action="/billing/add_method" id="payment-form">
				<table>
					<tr>
						<td colspan="6">
							<label>Card Number</label>
							<input id="cc_num" type="number" class="form-control" name="cc_num" />
						</td>
						<td colspan="2">
							<label>Exp</label>
							<select id="exp_month" class="form-control" name="exp_month">
								<option value="01" selected>01 - Jan</option>
								<option value="02">02 - Feb</option>
								<option value="03">03 - Mar</option>
								<option value="04">04 - Apr</option>
								<option value="05">05 - May</option>
								<option value="06">06 - Jun</option>
								<option value="07">07 - Jul</option>
								<option value="08">08 - Aug</option>
								<option value="09">09 - Sep</option>
								<option value="10">10 - Oct</option>
								<option value="11">11 - Nov</option>
								<option value="12">12 - Dec</option>
							</select>
						</td>
						<td colspan="2">
							<label class="invisible">Exp. Year</label>
							<select id="exp_year" class="form-control" name="exp_year">
								<?php $y = date('Y'); for ($i = 0; $i <= 10; $i++): ?>
								<option value="<?php echo ($y+$i); ?>"><?php echo ($y+$i); ?></option>
								<?php endfor; ?>
							</select>
						</td>
						<td colspan="2">
							<label>CVV</label>
							<input id="cvv" type="number" class="form-control" name="cvv" />
						</td>
					</tr>
					<tr>
						<td colspan="6">
							<label>Name on Card</label>
							<input id="first_name" name="first_name" type="text" class="form-control" />
						</td>
						<td colspan="6">
							<label class="invisible">Last Name on Card</label>
							<input id="last_name" name="last_name" type="text" class="form-control" />
						</td>
					</tr>
					<tr>
						<td colspan="12">
							<label>Billing Address</label>
							<input id="address1" type="text" class="form-control" name="address1" />
							<input id="address2" type="text" class="form-control" name="address2" />
						</td>
					</tr>
					<tr>
						<td colspan="3">
							<label>City</label>
							<input id="city" type="text" class="form-control" name="city" />
						</td>
						<td colspan="3">
							<label>State</label>
							<input type="text" id="state" maxlength="2" class="form-control" name="state" >
						</td>
						<td colspan="3">
							<label>ZIP</label>
							<input id="zip" type="text" class="form-control" name="zip" />
						</td>
						<td colspan="3">
							<label>Country</label>
							<?php echo country_dropdown('country','country','form-control m-b','US',array('US','UM','GB'),false,'US'); ?>
						</td>
					</tr>
				</table>
			</form>
		</div>
		<div class="form-footer">
			<a href="#" class="btn btn-primary btn-lg" id="add-card">Add Card</a>
		</div>
	</div>
</div>

<script type="text/javascript" src="https://js.stripe.com/v1/"></script>
<script type="text/javascript">
    // this identifies your website in the createToken call below
    
    $(document).ready(function() {
	    $('#add-card').click(function(e) {
    	    e.preventDefault();
    	    
		    $('#payment-form').submit();
	    });

		var scope = angular.element($('body')[0]).scope();
		scope.$apply(function() {
			<?php if (!empty($_GET['pi'])): ?>
			scope.settings_mode = 'payment';
			<?php else: ?>
			scope.settings_mode = 'plans';
			<?php endif; ?>
		});
	});
 
</script>

<?php $this->load->view('layout/analytics_snippets', ['intercom' => 1]); ?>
