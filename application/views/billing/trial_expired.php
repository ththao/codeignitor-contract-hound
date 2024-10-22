<!DOCTYPE html>
<html dir="ltr" lang="en-US">
	<!-- application layout -->
	<head>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8">
		<title>Expired Trial | Contract Hound</title>
		
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
		<div class="modal modal-lg fade" id="overage-notice">
			<div class="modal-container">
				<div class="modal-dialog">
					<div class="modal-content" >
						<div class="modal-header modal-header-ad background-orange text-white">
							<div class="modal-header-ad-background" style="background-image: url(/ui/img/ads/overage.jpg);"></div>
							<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span data-icon="close-small">Close</span></button>
							<h3 class="modal-title">Your Trial has Expired</h3>
						</div>
						<div class="modal-body">
							<div class="text-large">
								<p>Your 14 day trial has ended. Although your trial has expired you can still log in and access your contracts, 
									however you will not be able to upload any additional contracts and any reminders scheduled during your 
									trial will not be sent. Click the link below to upgrade.</p>
							</div>
						</div>
						<div class="modal-footer">
							<a href="/contracts" class="btn btn-default btn-lg">View Contracts</a>
							<a href="/billing/upgrade" class="btn btn-success btn-lg">Upgrade Today</a>
						</div>
					</div>
				</div>
			</div>
		</div>
		
		<script>
			$('#overage-notice').modal('show');
			$('#overage-notice').on('hide.bs.modal', function (e) {
				window.location.href = "<?php echo site_url('welcome'); ?>";
			});
		</script>

		<?php $this->load->view('layout/analytics_snippets', ['intercom' => 1, 'google' => 1]); ?>

	</body>
</html>
