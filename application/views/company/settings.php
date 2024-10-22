<!DOCTYPE html>
<html dir="ltr" lang="en-US">
	<!-- application layout -->
	<head>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8">
		<title>Company Settings | Contract Hound</title>
		
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
							<h2 class="modal-title">Company Settings</h2>
							<p>You are the primary owner of this account. You can <a href="../transfer-company">transfer ownership</a> to another team member.</p>
						</div>
						<div class="modal-body">
					
							<h4>Company Name</h4>
							<input class="form-control input-lg" placeholder="ACME Co." value="ACME Strategic Marketing" />
							<p class="help-block">Your company name is used throughout Contract Hound.</p>
					
							<h4>Company URL</h4>
							<div class="input-group input-group-lg">
								<span class="input-group-addon text-light text-italic">http://</span>
								<input type="text" class="form-control" placeholder="acme" value="acmestrategy" />
								<span class="input-group-addon text-light text-italic">.contracthound.com</span>
							</div>
							<p class="help-block">This is a unique web address used to access your Contract Hound account. In the event that you change your URL, users visiting the old address will be forwarded to your new URL.</p>
		
						</div>
						<div class="modal-footer modal-footer-left">
							<a href="#" class="btn btn-lg btn-primary" data-dismiss="modal">Save</a>
							<a href="#" class="btn btn-lg btn-text" ng-dismiss="modal">Cancel</a>
						</div>
					</div>
				</div>
			</div>
		</div>
		
		<script>
			$('#company-settings').modal('show');
			$('#company-settings').on('hide.bs.modal', function (e) {
				window.location.href = "<?php echo site_url('welcome'); ?>";
			});
		</script>

		<?php $this->load->view('layout/analytics_snippets', ['intercom' => 1, 'google' => 1]); ?>
	</body>
</html>
