<!DOCTYPE html>
<html dir="ltr" lang="en-US">
	<head>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8">
		<title>New Reminder | Contract Hound</title>

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
    <script>
        var userLocale = '<?= $locale; ?>';
        var locale_date_format = '<?= $date_format; ?>';
    </script>
		<!--<script src="/ui/jqueryui/jquery-ui-1.11.4/i18n/jquery.ui.datepicker-<?/*= $locale */?>.min.js"></script>-->
		<script src="//d2wy8f7a9ursnm.cloudfront.net/v7/bugsnag.min.js"></script>
		<script>Bugsnag.start({apiKey: '<?= $_ENV['BUGSNAG_API_KEY'] ?>', releaseStage: '<?= ENVIRONMENT ?>'});</script>
		<script src="/ui/js/app.js"></script>

		<link rel="stylesheet" type="text/css" href="/ui/jqueryui/jquery-ui-1.11.4/jquery-ui.structure.min.css" />
		<link rel="stylesheet" type="text/css" href="/ui/suggest/css/bootstrap-suggest.css" />
		<link rel="stylesheet" type="text/css" href="/ui/tokenfield/dist/css/bootstrap-tokenfield.min.css" />

		<link rel="stylesheet" type="text/css" href="/ui/css/app.css" />

	</head>
	<body ng-app="ContractHoundApp">
		<div class="modal fade" id="reminder-modal">
			<div class="modal-container">
				<div class="modal-dialog">
					<form action="/reminders/add/<?php if (!empty($oContract)) { echo $oContract->contract_id; } ?>" method="post">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span data-icon="close-small">Close</span></button>
							<h3 class="modal-title">New Reminder<?php if (!empty($oContract)):?> <small><?php echud($oContract->name); ?></small><?php endif; ?></h3>
						</div>
						<div class="modal-body">
							<?php if (empty($oContract)):?>
							<div class="form-grid form-grid-large">
								<table>
									<tr>
										<td class="form-label"><label>Choose Contract</label></td>
										<td class="form-response">
											<select name="contract_id" class="form-control input-lg" data-value="0">
												<option selected value="0">Contracto 1-o</option>
												<option value="1">Cras justo odio</option>
												<option value="2">Dapibus ac facilisis in</option>
												<option value="3">Morbi leo risus</option>
												<option value="4">This one has a really long name porta ac consectetur ac</option>
												<option value="5">Vestibulum at eros</option>
											</select>
										</td>
									</tr>
								</table>
							</div>
							<?php endif; ?>

							<p class="text-large text-light">
								Create a reminder for
								<a href="#" ng-click="selecting_members=true">
									all team members
								</a>
								on
								<input class="form-control input-lg input-link" size="12" type="text" id="reminder-date" name="alert_date" value="<?php
								echo convertto_local_datetime(date('Y-m-d H:i:s',strtotime('+1 Day')),$time_zone); ?>" data-jquery="datepicker" />

							</p>

							<div ng-show="selecting_members==true">

								<div class="divider divider-flush">
									<div class="divider-content">
										<div class="divider-title">
											<h6>Choose Members</h6>
											<small>(<?php echo number_format(count($aTeamMembers)); ?> selected)</small>
										</div>
										<div class="divider-separator">
											<hr/>
										</div>
										<div class="divider-actions">
											<a id="select-all" href="#" class="btn btn-link btn-link btn-sm">
												all
											</a>
											<a id="select-none" href="#" class="btn btn-link btn-sm">
												none
											</a>
											<a id="select-cancel" href="#" class="btn btn-default btn-sm" ng-click="selecting_members=false">
												cancel
											</a>
										</div>
									</div>
								</div>

								<div class="members">
									<?php foreach ($aTeamMembers as $oMember): ?>
									<label class="member member-option">
										<input type="checkbox" ng-checked="reminder_members.indexOf(1)!=-1" name="reminder_members[]" value="<?php echo $oMember->member_id; ?>" />
										<div class="member-content">
											<div class="member-graphic member-graphic-small">
												<div class="avatar" style="background-image: url(<?php
												if ($oMember->avatar): ?>/uas/<?php echo $oMember->avatar; ?><?php else: ?>/ui/img/avatars/default.png<?php endif;
												?>)">
													<img src="<?php
													if ($oMember->avatar): ?>/uas/<?php echo $oMember->avatar; ?><?php else: ?>/ui/img/avatars/default.png<?php endif;
													?>" />
												</div>
											</div>
											<div class="member-body">
												<div class="member-name">
													<h6><?php $oMember->name?echud($oMember->name):echud($oMember->email); ?></h6>
												</div>
											</div>
										</div>
									</label>
									<?php endforeach; ?>
								</div>

							</div>

							<textarea name="message" class="form-control input-lg" placeholder="Type a descriptive reminder with instructions..."></textarea>
						</div>
						<div class="modal-footer">
							<a href="/contracts<?php if (!empty($oContract)): ?>/view/<?php echo $oContract->contract_id; endif; ?>" class="btn btn-lg btn-text" ng-dismiss="modal">Cancel</a>
							<button type="submit" class="btn btn-lg btn-primary">Save Reminder</button>
						</div>
						</form>
					</div>

				</div>
			</div>
		</div>

		<script>
			$('#select-all, #select-cancel').click(function() {
				$('input[type=checkbox]').prop('checked', true);
				$('.divider-title small').text('( '+$('input[type=checkbox]:checked').length+' SELECTED)');
				return false;
			});

			$('#select-none').click(function() {
				$('input[type=checkbox]').prop('checked', false);
				$('.divider-title small').text('( '+$('input[type=checkbox]:checked').length+' SELECTED)');
				return false;
			});

			$('input[type=checkbox]').change(function() {
				$('.divider-title small').text('( '+$('input[type=checkbox]:checked').length+' SELECTED)');
			});

			$('#reminder-modal').modal('show');
			$('#reminder-modal').on('hide.bs.modal', function (e) {
				<?php if (!empty($oContract)): ?>
				window.location.href = "/contracts/view/<?php echo $oContract->contract_id; ?>";
				<?php else: ?>
				window.location.href = "/contracts";
				<?php endif; ?>
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
