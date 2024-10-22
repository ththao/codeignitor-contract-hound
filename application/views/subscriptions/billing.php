<div class="wrapper wrapper-content">
	<div class="row">
		<div class="col-md-8 col-md-offset-2 col-sm-12 col-lg-6 col-lg-offset-3">
			<div class="ibox float-e-margins">
				<div class="ibox-title">
					<h5>You&apos;ve Selected</h5>
				</div>
				<div class="ibox-content">
					<div class="row">
						<div class="col-sm-12">
							<h5><?php echo $aPlanDetails['name']; ?></h5>
							<ul>
								<?php foreach($aPlanDetails['basics'] as $sBasic): ?>
									<li><?php echo $sBasic; ?></li>
								<?php endforeach; ?>
								<?php foreach($aPlanDetails['extras'] as $sExtra): ?>
									<li><?php echo $sExtra; ?></li>
								<?php endforeach; ?>
							</ul>
							<div class="selected-plan-footer">
								$<?php echo $aPlanDetails['price']; ?>/mo
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="wrapper wrapper-content">
	<div class="row">
		<div class="col-md-8 col-md-offset-2 col-sm-12 col-lg-6 col-lg-offset-3">
			<div class="ibox float-e-margins">
				<div class="ibox-title">
					<h5><?php if (!empty($iRequestedPlanId)): ?>Complete Your Billing Info To Continue<?php else: ?>Update Your Billing Info Below<?php endif; ?></h5>
				</div>
				<div class="ibox-content">
					<div class="row">
						<div class="col-sm-12">
							<?php echo form_open('subscriptions/billing/'.$iRequestedPlanId,'method="post" role="form" class="form-horizontal"') ?>
								<div class="form-group"><label class="col-sm-2 control-label">Name</label>
									<div class="col-sm-10">
										<div class="row">
											<div class="col-md-6"><input type="text" name="first_name" value="<?php echud($oBillingInfo->first_name); ?>"
												id="first_name" maxlength="255" placeholder="First" class="form-control"></div>
											<div class="col-md-6"><input type="text" name="last_name" value="<?php echud($oBillingInfo->last_name); ?>"
												id="last_name" maxlength="255" placeholder="Last" class="form-control"></div>
										</div>
									</div>
								</div>

								<div class="hr-line-dashed"></div>

								<div class="form-group"><label class="col-sm-2 control-label">Address</label>
									<div class="col-sm-10"><input type="text" name="address" value="<?php echud($oBillingInfo->address); ?>"
										id="address" maxlength="60" class="form-control"></div>
								</div>

								<div class="form-group"><label class="col-sm-2 control-label">Address2</label>
									<div class="col-sm-10"><input type="text" name="address2" value="<?php echud($oBillingInfo->address2); ?>"
										id="address2" maxlength="60" class="form-control"></div>
								</div>

								<div class="form-group"><label class="col-sm-2 control-label">City</label>
									<div class="col-sm-10"><input type="text" name="city" value="<?php echud($oBillingInfo->city); ?>"
										id="city" maxlength="40" class="form-control"></div>
								</div>

								<div class="form-group"><label class="col-sm-2 control-label">State</label>
									<div class="col-sm-3"><input type="text" name="state" value="<?php echud($oBillingInfo->state); ?>"
										id="state" maxlength="2" class="form-control"></div>
								</div>

								<div class="form-group"><label class="col-sm-2 control-label">Zip</label>
									<div class="col-sm-3"><input type="text" name="zip" value="<?php echud($oBillingInfo->zip); ?>"
										id="zip" maxlength="20" class="form-control"></div>
								</div>

								<div class="form-group">
									<label class="col-sm-2 control-label">Country</label>
									<div class="col-sm-8">
										<?php echo country_dropdown('country','country','form-control m-b','US',array('US','UM','GB'),false,'US'); ?>
									</div>
								</div>

								<div class="hr-line-dashed"></div>

								<div class="form-group">
									<label class="col-sm-4 control-label">Card Type</label>
									<div class="col-sm-8">
										<select class="form-control m-b" id="cc_type" name="cc_type">
											<option value="1" selected="selected">Visa</option>
											<option value="2">Mastercard</option>
											<option value="3">American Express</option>
											<option value="4">Discover</option>
										</select>
									</div>
								</div>

								<div class="form-group"><label class="col-sm-4 control-label">Credit Card Number</label>
									<div class="col-sm-8"><input type="text" name="cc_number" value=""
										placeholder="<?php if ($oBillingInfo->cc_last_4) { echo 'Last used ended with '.$oBillingInfo->cc_last_4; } ?>"
										id="cc_number" class="form-control"></div>
								</div>

								<div class="form-group">
									<label class="col-sm-4 control-label">Expiration Date</label>
									<div class="col-sm-4">
										<select class="form-control m-b" id="cc_expire_month" name="cc_expire_month">
											<?php if (!$oBillingInfo->cc_expire) {
												$iMonth = 1;
											} else {
												$iMonth = date('M',strtotime($oBillingInfo->cc_expire));
											} ?>
											<option value="01"<?php if ($iMonth == 1): ?> selected="selected"<?php endif; ?>>Jan (01)</option>
											<option value="02"<?php if ($iMonth == 2): ?> selected="selected"<?php endif; ?>>Feb (02)</option>
											<option value="03"<?php if ($iMonth == 3): ?> selected="selected"<?php endif; ?>>Mar (03)</option>
											<option value="04"<?php if ($iMonth == 4): ?> selected="selected"<?php endif; ?>>Apr (04)</option>
											<option value="05"<?php if ($iMonth == 5): ?> selected="selected"<?php endif; ?>>May (05)</option>
											<option value="06"<?php if ($iMonth == 6): ?> selected="selected"<?php endif; ?>>Jun (06)</option>
											<option value="07"<?php if ($iMonth == 7): ?> selected="selected"<?php endif; ?>>Jul (07)</option>
											<option value="08"<?php if ($iMonth == 8): ?> selected="selected"<?php endif; ?>>Aug (08)</option>
											<option value="09"<?php if ($iMonth == 9): ?> selected="selected"<?php endif; ?>>Sep (09)</option>
											<option value="10"<?php if ($iMonth == 10): ?> selected="selected"<?php endif; ?>>Oct (10)</option>
											<option value="11"<?php if ($iMonth == 11): ?> selected="selected"<?php endif; ?>>Nov (11)</option>
											<option value="12"<?php if ($iMonth == 12): ?> selected="selected"<?php endif; ?>>Dec (12)</option>
										</select>
									</div>
									<div class="col-sm-4">
										<select class="form-control m-b" id="cc_expire_year" name="cc_expire_year">
											<?php if (!$oBillingInfo->cc_expire) {
												$sYear = date('Y');
											} else {
												$sYear = date('Y',strtotime($oBillingInfo->cc_expire));
											}
											for ($i=0;$i<7;$i++):
												$sYearBase = str_pad(date('y'),'0',STR_PAD_LEFT) + $i;
												$sYearFull = '20'.$sYearBase; ?>
											<option value="<?php echo $sYearBase; ?>"<?php if ($sYear == $sYearFull): ?> selected="selected"<?php endif; ?>><?php echo $sYearFull; ?></option>
											<?php endfor; ?>
										</select>
									</div>
								</div>

								<div class="form-group"><label class="col-sm-4 control-label">CVV</label>
									<div class="col-sm-3"><input type="text" name="cvv" value="" id="cvv" maxlength="4" class="form-control"></div>
								</div>

								<div class="hr-line-dashed"></div>

								<div>
									<button class="btn btn-sm btn-primary pull-right m-t-n-xs" type="submit"><strong>Submit</strong></button>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="wrapper wrapper-content last-box">
	<div class="row">
		<div class="col-md-8 col-md-offset-2 col-sm-12 col-lg-6 col-lg-offset-3">
			<div class="ibox float-e-margins">
				<div class="ibox-content">
					<div class="row">
						<div class="col-sm-12">
							<p>100% Satisfaction Guarantee. You can switch packages or cancel any time.</p>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<style>
	.ibox { margin-bottom: 0; }
	.wrapper-content { padding: 0 10px 20px; }
	.last-box { padding-bottom: 80px;}
</style>
