<!DOCTYPE html>
<html dir="ltr" lang="en-US">
	<head>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8">
		<title>Transfer Company Ownership | Contract Hound</title>
		
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
		<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.4.5/angular.min.js"></script>
		<script src="//d2wy8f7a9ursnm.cloudfront.net/v7/bugsnag.min.js"></script>
		<script>Bugsnag.start({apiKey: '<?= $_ENV['BUGSNAG_API_KEY'] ?>', releaseStage: '<?= ENVIRONMENT ?>'});</script>
		<script src="/ui/js/app.js"></script>

		<link rel="stylesheet" type="text/css" href="/ui/jqueryui/jquery-ui-1.11.4/jquery-ui.structure.min.css" />
		<link rel="stylesheet" type="text/css" href="/ui/suggest/css/bootstrap-suggest.css" />
		<link rel="stylesheet" type="text/css" href="/ui/tokenfield/dist/css/bootstrap-tokenfield.min.css" />

		<link rel="stylesheet" type="text/css" href="/ui/css/app.css" />

	</head>
	<body ng-app="ContractHoundApp">
		<div class="modal fade" id="company-settings">
			<div class="modal-container modal-container-large">
				<div class="modal-dialog">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span data-icon="close">Close</span></button>
							<h2 class="modal-title">Transfer Company Ownership</h2>
							<p>Account owners have total control over an account. Once an account has been transferred, only the current owner may transfer it back.</p>
						</div>
						<div class="modal-body">
					
							<h4>New owner of ACME Co.</h4>
							
							<div class="members">
								<label class="member member-option">
									<input type="radio" name="transfer" checked value="1" />
									<div class="member-content">
										<div class="member-graphic">
											<div class="avatar avatar-medium" style="background-image: url(/ui/img/samples/avatar1.jpg)">
												<img src="/ui/img/samples/avatar1.jpg" />
											</div>
										</div>
										<div class="member-body">
											<div class="member-name">
												<h6>John Doe</h6>
											</div>
											<div class="member-meta">
												<span>jdoe@acme.co</span>
											</div>
										</div>
									</div>
								</label>
								<label class="member member-option">
									<input type="radio" name="transfer" value="2" />
									<div class="member-content">
										<div class="member-graphic">
											<div class="avatar avatar-medium" style="background-image: url(/ui/img/samples/avatar2.jpg)">
												<img src="/ui/img/samples/avatar2.jpg" />
											</div>
										</div>
										<div class="member-body">
											<div class="member-name">
												<h6>Ron Dillard</h6>
											</div>
											<div class="member-meta">
												<span>rdillard@acme.co</span>
											</div>
										</div>
									</div>
								</label>
								<label class="member member-option">
									<input type="radio" name="transfer" value="3" />
									<div class="member-content">
										<div class="member-graphic">
											<div class="avatar avatar-medium" style="background-image: url(/ui/img/samples/avatar3.jpg)">
												<img src="/ui/img/samples/avatar3.jpg" />
											</div>
										</div>
										<div class="member-body">
											<div class="member-name">
												<h6>Martha Escobedo</h6>
											</div>
											<div class="member-meta">
												<span>mescobedo@acme.co</span>
											</div>
										</div>
									</div>
								</label>
							</div>
		
							<p class="help-block">You may only transfer ownership to active team members within your account.</p>
		
							<h4>Current Owner Password</h4>
							<input type="password" class="form-control" />
							<p class="help-block">Enter your password to verify the transfer.</p>
		
						</div>
						<div class="modal-footer modal-footer-left">
							<a href="#" class="btn btn-lg btn-warning" data-dismiss="modal">Transfer Ownership</a>
							<a href="/users" class="btn btn-lg btn-text" ng-dismiss="modal">Cancel</a>
						</div>
					</div>
				</div>
			</div>
		</div>
		
		<script>
			$('#company-settings').modal('show');
			$('#company-settings').on('hide.bs.modal', function (e) {
				window.location.href = "<?php echo site_url('users'); ?>";
			});
		</script>

		<?php $this->load->view('layout/analytics_snippets', ['intercom' => 1, 'google' => 1]); ?>
	</body>
</html>
