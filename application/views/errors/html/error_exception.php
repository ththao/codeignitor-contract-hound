<?php
date_default_timezone_set('America/New_York');
if (!session_id()) {
	session_name('sascn');
	@session_start();
}
?><!DOCTYPE html>
<html dir="ltr" lang="en-US">
	<head>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8">
		<title>Error | Contract Hound</title>
		
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
	<body>
		<div class="modal fade" id="error">
			<div class="modal-container">
				<div class="modal-dialog">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span data-icon="close-small">Close</span></button>
							<h3 class="modal-title" data-error="exception">Error: EE</h3>
						</div>
						<div class="modal-body">
							<p>We have had an error, please click one of the other links to continue or contact support. Code: ee2</p>
							<?php if ((!empty($_SESSION['member_id']) && $_SESSION['member_id'] == $_ENV['ADMIN_USER_ID']) ||
									  (!empty($_SESSION['admin_member_id']))): ?>
								<h4>An uncaught Exception was encountered</h4>

								<p>Type: <?php echo get_class($exception); ?></p>
								<p>Message: <?php echo $message; ?></p>
								<p>Filename: <?php echo $exception->getFile(); ?></p>
								<p>Line Number: <?php echo $exception->getLine(); ?></p>

								<?php if (defined('SHOW_DEBUG_BACKTRACE') && SHOW_DEBUG_BACKTRACE === TRUE): ?>
									<p>Backtrace:</p>
									<?php foreach ($exception->getTrace() as $error): ?>
										<?php if (isset($error['file']) && strpos($error['file'], realpath(BASEPATH)) !== 0): ?>

											<p style="margin-left:10px">
												File: <?php echo $error['file']; ?><br />
												Line: <?php echo $error['line']; ?><br />
												Function: <?php echo $error['function']; ?>
											</p>
										<?php endif ?>
									<?php endforeach ?>
								<?php endif ?>
							<?php endif ?>
						</div>
						<div class="modal-footer">
							<a href="/help" class="btn btn-text btn-lg">Contact Support</a>
							<a href="/welcome" class="btn btn-primary btn-lg" ng-dismiss="modal">Continue</a>
						</div>
					</div>
				</div>
			</div>
		</div>
		
		<script>
			$('#error').modal('show');
			$('#error').on('hide.bs.modal', function (e) {
				window.location.href = "/welcome";
			});
		</script>
	</body>
</html>