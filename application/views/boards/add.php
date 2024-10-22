<!DOCTYPE html>
<html dir="ltr" lang="en-US">
	<!-- application layout -->
	<head>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8">
		<title><?php echo lang('New Board'); ?></title>

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
		<div class="modal fade" id="new-board">
			<div class="modal-container modal-container-large">
				<div class="modal-dialog">
					<form method="post" action="/boards/add">
					<div class="modal-content" ng-hide="upload_step">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span data-icon="close"><?php echo lang('Close'); ?></span></button>
							<h2 class="modal-title"><?php echo lang('New Board'); ?></h2>
							<p><?php echo lang('Use boards to group contracts and stay organized.  Create boards to organize contracts by client, business goal, contract type, or anything else.'); ?></p>
						</div>
						<div class="modal-body">
							<h4><?php echo lang('Name your Board'); ?></h4>
							<input name="name" class="form-control input-lg" placeholder="<?php echo lang('Marketing Board...'); ?>" />
							<p class="help-block"><?php echo lang('Enter a name that describes the kind of contracts that will live here, like "Marketing Team Contracts" or "Sell-side Contracts."'); ?></p>
						</div>
						<div class="modal-footer modal-footer-left">
							<button type="submit" class="btn btn-lg btn-primary"><?php echo lang('Create Board'); ?></button>
							<a href="/welcome" class="btn btn-lg btn-text" ng-dismiss="modal"><?php echo lang('Cancel'); ?></a>
						</div>
					</div>
					</form>
				</div>
			</div>
		</div>

		<script>
			$('#new-board').modal('show');
			$('#new-board').on('hide.bs.modal', function (e) {
				window.location.href = "<?php echo site_url('welcome'); ?>";
			});
			<?php if ($this->session->flashdata('error')): ?>
			var notifications = new Array(
				[{ title: 'Error', message: '<?php echo $this->session->flashdata('error'); ?>' },{ type: 'danger' }]
			);
			<?php endif; ?>
		</script>
    <?php $this->load->view('layout/analytics_snippets', ['intercom' => 1, 'google' => 1]); ?>

	</body>
</html>
