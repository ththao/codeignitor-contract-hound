<!DOCTYPE html>
<html dir="ltr" lang="en-US">
	<head>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8">
		<title>Update Team | Contract Hound</title>

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
		<div class="modal fade" id="switch_account">
			<div class="modal-container">
				<div class="modal-dialog">
					<form id="form-update-team">
					<div class="modal-content" ng-hide="upload_step">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span data-icon="close-small">Close</span></button>
							<h3 class="modal-title">Switch Accounts</h3>
							<p>You have access to multiple accounts. Click "Switch" below to log in to a different account.</p>
						</div>
						<div class="modal-body">
							<div class="table-responsive">
							    <table class="table">
							        <thead>
							            <tr>
								            <th>#</th>
							                <th>Company Name</th>
							                <th>Owner&apos;s Email</th>
							                <th>&nbsp;</th>
							            </tr>
							        </thead>

							        <tbody>
								        <tr>
									        <td><?php echo $oMember->parent_id; ?></td>
									        <td><?php echud($oMember->company); ?></td>
									        <td><?php echud($oMember->email); ?></td>
							                <td>
								                <?php if ($iCurrentlyLoggedInParentId == $oMember->parent_id): ?>
							                	Currently Logged Into
							                	<?php else: ?>
							                	<a class="btn btn-success" href="<?php echo site_url('members/member_login_as/'.$oMember->parent_id); ?>">Switch</a>
							                	<?php endif; ?>
							                </td>
								        </tr>
								        <?php foreach ($oOtherMemberAccounts as $oOtherMemberAccount): ?>
							            <tr>
									        <td><?php echo $oOtherMemberAccount->parent_id; ?></td>
							                <td><?php echud($oOtherMemberAccount->parent_company_name); ?></td>
							                <td><?php echud($oOtherMemberAccount->parent_email); ?></td>
							                <td>
								                <?php if ($iCurrentlyLoggedInParentId == $oOtherMemberAccount->parent_id): ?>
							                	Currently Logged Into
							                	<?php else: ?>
							                	<a class="btn btn-success" href="<?php echo site_url('members/member_login_as/'.$oOtherMemberAccount->parent_id); ?>">Switch</a>
							                	<?php endif; ?>
							                </td>
							            </tr>
							            <?php endforeach; ?>
							        </tbody>
							    </table>
							</div>
						</div>
						<div class="modal-footer">
							<a href="/welcome" class="btn btn-lg btn-text" data-dismiss="modal">Cancel</a>
						</div>
					</div>
					</form>
				</div>
			</div>
		</div>

		<script>
			$('#switch_account').modal('show');

			$('#switch_account').on('hide.bs.modal', function (e) {
				window.location.href = "/welcome";
			});
		</script>
    <?php $this->load->view('layout/analytics_snippets', ['intercom' => 1, 'google' => 1]); ?>
    
	</body>
</html>
