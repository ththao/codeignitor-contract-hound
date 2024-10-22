<!DOCTYPE html>
<html dir="ltr" lang="en-US">
	<!-- application layout -->
	<head>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8">
		<title>Delete Contract Supporting Document | Contract Hound</title>
		
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
		<div class="modal modal-sm fade" id="delete-contract">
			<div class="modal-container">
				<div class="modal-dialog">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span data-icon="close-small">Close</span></button>
							<h3 class="modal-title">Delete this supporting document, <?php echud($oSupportDoc->file_name); ?>?</h3>
						</div>
						<div class="modal-footer modal-footer-left">
							<a href="/contracts/delete_contract_support_doc/<?php echo $oSupportDoc->contract_support_doc_id; ?>?cdc=1" class="btn btn-lg btn-danger">Delete Document</a>
							<a href="/contracts/view/<?php echo $oContract->contract_id; ?>" class="btn btn-lg btn-text" data-dismiss="modal">Cancel</a>
						</div>
					</div>
				</div>
			</div>
		</div>
		
		<script>
			$('#delete-contract').modal('show');
			$('#delete-contract').on('hide.bs.modal', function (e) {
				window.location.href = "<?php echo site_url('contracts/view/'.$oContract->contract_id); ?>";
			});
		</script>

		<?php $this->load->view('layout/analytics_snippets', ['intercom' => 1, 'google' => 1]); ?>
    
	</body>
</html>
