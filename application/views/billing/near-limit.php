<!DOCTYPE html>
<html dir="ltr" lang="en-US">
	<!-- application layout -->
	<head>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8">
		<title>Account Notice | Contract Hound</title>
		
		<link rel="shortcut icon" href="/ui/img/logos/contracthound-favicon.png" />
		<meta name="viewport" content="width=device-width, maximum-scale=1.0, minimal-ui" />
		
		<script src="//code.jquery.com/jquery-1.9.1.min.js"></script>
		<script src="/ui/jqueryui/jquery-ui-1.11.4/jquery-ui.min.js"></script>
		<script src="/ui/modernizr/modernizr.js"></script>
		<script src="/ui/bootstrap/js/bootstrap.min.js"></script>
		<script src="/ui/suggest/js/bootstrap-suggest.js"></script>
		<script src="/ui/dropzone/dropzone.js"></script>
		<script src="/ui/tokenfield/dist/bootstrap-tokenfield.min.js"></script>
		<script src="/ui/bootstrap-notify/bootstrap-notify.min.js"></script>
		<script src="/ui/bootstrap-validator/dist/validator.min.js"></script>
		<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.4.5/angular.min.js"></script>
		<script src="//d2wy8f7a9ursnm.cloudfront.net/v7/bugsnag.min.js"></script>
		<script>Bugsnag.start({apiKey: '<?= $_ENV['BUGSNAG_API_KEY'] ?>', releaseStage: '<?= ENVIRONMENT ?>'});</script>
		<script src="/ui/js/app.js"></script>

		<link rel="stylesheet" type="text/css" href="/ui/jqueryui/jquery-ui-1.11.4/jquery-ui.structure.min.css" />
		<link rel="stylesheet" type="text/css" href="/ui/suggest/css/bootstrap-suggest.css" />
		<link rel="stylesheet" type="text/css" href="/ui/tokenfield/dist/css/bootstrap-tokenfield.min.css" />

		<link rel="stylesheet" type="text/css" href="/ui/css/app.css" />
	</head>
	<body data-ng-app="ContractHoundApp">
		<div class="modal fade" id="account-notice">
			<div class="modal-container">
				<div class="modal-dialog">
					<div class="modal-content" >
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span data-icon="close-small">Close</span></button>
							<h3 class="modal-title">Upgrade your account for more space</h3>
						</div>
						<div class="modal-body">
							<h4>You have almost reached the limit of your current plan.</h4>
							<div class="progress">
								<div class="progress-bar progress-bar-danger" style="width: <?php echo (($iPlanCount/$oSub->contract_limit)*100); ?>%"></div>
							</div>
							<p class="help-block">You are currently using <?php echo number_format($iPlanCount); ?> of <?php echo number_format($oSub->contract_limit); 
								?> Contracts at $<?php echo number_format($oSub->price); ?> / mo. Upgrading is simple. You can add blocks of 50 contracts for $95 per month. Change your subscription at any time.</p>
		
							<div class="form-grid form-grid-large">
								<table>
									<tr>
										<td class="form-label">
											<label>Next Subscription Level</label>
										</td>
										<td class="form-response">
											<p class="form-control-static input-lg"><span style="padding-top:9px;line-height:27px;">
											<?php 
											$iNextSubLevelPrice = null;
											if (!empty($aPlans[$oSub->plan_id + 1])) {
												echo $aPlans[$oSub->plan_id + 1]['label'].' Contracts';
												$iNextSubLevelPrice = $aPlans[$oSub->plan_id + 1]['price'];
											} else {
												echo "Let&apos;s talk!";
											} ?></span></p>
										</td>
										<?php if (!empty($iNextSubLevelPrice)): ?>
										<td class="form-response form-compact">
											<label ng-hide="enterprise" class="text-large" id="display-price"><span>$<?php echo number_format($iNextSubLevelPrice); ?></span><em class="text-green text-normal">/mo</em></label>
										</td>
										<?php endif; ?>
									</tr>
								</table>
							</div>
						</div>
						<div class="modal-footer">
							<a href="/welcome" class="btn btn-text btn-lg" ng-dismiss="modal">No Thanks</a>
							<a href="/billing/upgrade" class="btn btn-primary btn-lg">Upgrade</a>
						</div>
					</div>
				</div>
			</div>
		</div>
		
		<script>
			$('#account-notice').modal('show');
			$('#account-notice').on('hide.bs.modal', function (e) {
				window.location.href = "<?php echo site_url('welcome'); ?>";
			});
			<?php /*$('#plan-select').change(function() {
				$('#display-price span').text('$'+$(':selected',$('#plan-select')).data('price'));
			})*/ ?>
		</script>
		
		<?php $this->load->view('layout/analytics_snippets', ['intercom' => 1, 'google' => 1]); ?>

	</body>
</html>
