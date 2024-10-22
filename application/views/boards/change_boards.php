<!DOCTYPE html>
<html dir="ltr" lang="en-US">
	<head>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8">
		<title>Move Contract | Contract Hound</title>
		
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
		<div class="modal fade" id="upload-contract">
			<div class="modal-container">
				<div class="modal-dialog">
					<div class="modal-content">
						<form method="post" action="/contracts/change_board/<?php echo $oContract->contract_id; ?>">
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span data-icon="close-small">Close</span></button>
								<h3 class="modal-title">Choose a Folder</h3>
								<p>Move this contract to a new folder</p>
							</div>
							<div class="modal-body">
								<select id="board_id" name="board_id" class="form-control input-lg" data-value="0">
									<?php $bFirst = true; foreach ($oBoards as $oBoard): ?>
									<option<?php if ($bFirst): ?> selected<?php $bFirst = false; endif; ?> value="<?php echo $oBoard->board_id; ?>"><?php echud($oBoard->board_path); ?></option>
									<?php echo $oBoard->sub_board_options; ?>
									<?php endforeach; ?>
								</select>
							</div>
							<div class="modal-footer">
								<input type="submit" class="btn btn-lg btn-primary" value="Move Contract">
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
		
		<script>
			$('#upload-contract').modal('show');
			$('#upload-contract').on('hide.bs.modal', function (e) {
				window.location.href = "<?php echo site_url('contracts/view/'.$oContract->contract_id); ?>";
			});
		</script>
    <?php $this->load->view('layout/analytics_snippets', ['intercom' => 1, 'google' => 1]); ?>

	</body>
</html>
