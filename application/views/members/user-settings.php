<!DOCTYPE html>
<html dir="ltr" lang="en-US">
	<!-- application layout -->
	<head>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8">
		<title>User Settings | Contract Hound</title>

		<link rel="shortcut icon" href="/ui/img/logos/contracthound-favicon.png" />
		<meta name="viewport" content="width=device-width, maximum-scale=1.0, minimal-ui" />

		<script src="//code.jquery.com/jquery-1.9.1.min.js"></script>
		<script src="/ui/jqueryui/jquery-ui-1.11.4/jquery-ui.min.js"></script>
		<script src="/ui/modernizr/modernizr.js"></script>
		<script src="/ui/bootstrap/js/bootstrap.min.js"></script>
		<script src="/ui/suggest/js/bootstrap-suggest.js"></script>
		<script src="/ui/dropzone/dropzone.js"></script>
		<script src="/ui/tokenfield/dist/bootstrap-tokenfield.min.js"></script>
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
		<div class="modal fade" id="user-settings">
			<div class="modal-container modal-container-large">
				<div class="modal-dialog">
					<div class="modal-content">
						<div class="modal-header">
							<a style="float:right; color: #909fae;" href="/welcome"><span data-icon="close">Close</span></a>
							<div class="tabs">
								<div class="tabs-content">
									<div class="tabs-header">
										<h2>User Settings</h2>
									</div>
									<div class="tabs-body">
										<ul class="nav nav-tabs">
											<li ng-class="{active:(settings_mode=='profile')}"><a href="#" 
												ng-click="settings_mode='profile'">Profile</a></li>
											<li ng-class="{active:(settings_mode=='preferences')}"><a href="#" 
												ng-click="settings_mode='preferences'">Preferences</a></li>
										</ul>
									</div>
								</div>
							</div>
						</div>
						<div ng-show="settings_mode=='profile'">
							<div class="modal-body">
								<div class="profile">
									<div class="profile-content">
										<div class="profile-photo">
											<div class="avatar avatar-xlarge" 
												style="background-image: url(<?php if ($oMember->avatar): ?>/uas/<?php echo $oMember->avatar; ?><?php else: ?>/ui/img/samples/avatar1.jpg<?php endif; ?>)">
												<img src="<?php if ($oMember->avatar): ?>/uas/<?php echo $oMember->avatar; ?><?php else: ?>/ui/img/samples/avatar1.jpg<?php endif; ?>" />
												<div class="avatar-actions">
													<a href="#" class="avatar-action avatar-action-inner">Update</a>
													<a href="#" class="avatar-action avatar-action-outer text-danger">Remove</a>
												</div>
											</div>
										</div>

										<div class="profile-body">
											<form method="post" action="/members/settings" enctype="multipart/form-data">
											<div class="form-grid form-grid-large" ng-click="editing_user=true">
												<table>
													<tr>
														<td class="form-label"><label>Name:</label></td>
														<td colspan="3" class="form-response">
															<input class="form-control input-lg" type="text" 
																placeholder="Jon Doe" value="<?php echud($oMember->name); ?>" />
														</td>
													</tr>
													<tr>
														<td class="form-label"><label>Role:</label></td>
														<td colspan="3" class="form-response">
															<input class="form-control input-lg" type="text" 
																placeholder="Sales Manager" value="<?php echud($oMember->role); ?>" />
														</td>
													</tr>
													<tr>
														<td class="form-label"><label>Email:</label></td>
														<td colspan="3" class="form-response">
															<input class="form-control input-lg" type="text"
																placeholder="email@address.com" value="<?php echud($oMember->email); ?>" />
														</td>
													</tr>
													<?php /*<tr>
														<td class="form-label"><label>Picture:</label></td>
														<td colspan="3" class="form-response">
															<input name="avatar" class="form-control input-lg" type="file" value="" />
														</td>
													</tr>*/ ?>
													<tr>
														<td class="form-label"></td>
														<td colspan="3" class="form-response">
															<p class="help-block">Click fields to edit profile information.</p>
														</td>
													</tr>
												</table>
											</div>
											<div class="profile-body-actions" ng-show="editing_user">
												<a href="/welcome" class="btn btn-text btn-lg" ng-click="editing_user=false">Cancel</a>
												<button type="submit" class="btn btn-primary btn-lg">Save Changes</button>
											</div>
											</form>
										</div>
									</div>
								</div>
							</div>
							<div class="modal-footer modal-footer-left" ng-show="!editing_user">
								<a href="#" class="btn btn-default">Change Password</a>
								<a href="#" class="btn btn-text">Company Settings</a>
							</div>
						</div>

						<div ng-show="settings_mode=='preferences'">
							<form method="post" action="/members/settings/cp">
							<div class="modal-body">
								<div class="divider divider-gap">
									<div class="divider-content">
										<div class="divider-title">
											<small>Emails</small>
										</div>
										<div class="divider-separator">
											<hr/>
										</div>
									</div>
								</div>
								<h4>I would like to receive notifications by email...</h4>
								<div class="form-inline">
									<div class="form-group">
										<label class="option">
											<input type="radio" name="email-notifications"<?php if ($oMember->notify_contract_changes == 0): ?> checked<?php endif; ?> value="0" />
											<i class="option-icon"></i>
											Immediately
										</label>
									</div>
									<div class="form-group">
										<label class="option">
											<input type="radio" name="email-notifications"<?php if ($oMember->notify_contract_changes == 1): ?> checked<?php endif; ?> value="1" />
											<i class="option-icon"></i>
											Once per day
										</label>
									</div>
									<div class="form-group">
										<label class="option">
											<input type="radio" name="email-notifications"<?php if ($oMember->notify_contract_changes == 2): ?> checked<?php endif; ?> value="2" />
											<i class="option-icon"></i>
											Never
										</label>
									</div>
								</div>
								<p class="help-block help-block-margins">We will still email you about reminders
									 you've set or issues with your account.</p>

								<div class="divider divider-gap">
									<div class="divider-content">
										<div class="divider-title">
											<small>Notifications</small>
										</div>
										<div class="divider-separator">
											<hr/>
										</div>
									</div>
								</div>

								<h4>Notify me when...</h4>
								<label class="option">
									<input name="notify_contract_changes" type="checkbox" value="1"<?php if ($oMember->notify_contract_changes): ?> checked<?php endif; ?> />
									<i class="option-icon"></i>
									Someone changes the details of one of my contracts
								</label>
								<label class="option">
									<input name="notify_add_comment" type="checkbox" value="1"<?php if ($oMember->notify_add_comment): ?> checked<?php endif; ?> />
									<i class="option-icon"></i>
									New comments are added to one of my contracts
								</label>
								<label class="option">
									<input name="notify_board_change" type="checkbox" value="1"<?php if ($oMember->notify_board_change): ?> checked<?php endif; ?> />
									<i class="option-icon"></i>
									Boards are created or deleted
								</label>
								<label class="option">
									<input name="notify_contract_status_change" type="checkbox" value="1"<?php if ($oMember->notify_contract_status_change): ?> checked<?php endif; ?> />
									<i class="option-icon"></i>
									Contracts are created, archived, or deleted
								</label>
								<label class="option">
									<input name="notify_contract_ending" type="checkbox" value="1"<?php if ($oMember->notify_contract_ending): ?> checked<?php endif; ?> />
									<i class="option-icon"></i>
									Contracts are nearing their end date
								</label>
								<p class="help-block help-block-margins">Remember that you can mute notifications for a single contract on the contract's detail page.</p>

							</div>
							<div class="modal-footer">
								<a href="/welcome" class="btn btn-text btn-lg">Cancel</a>
								<button type="submit" class="btn btn-primary btn-lg">Save Changes</button>
							</div>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>

		<script>
			$('#user-settings').modal('show');
			$(document).ready(function(){
				var scope = angular.element($('body')[0]).scope();
				scope.$apply(function() {
					<?php if (!empty($bShowCp)): ?>
					scope.settings_mode = 'preferences';
					<?php else: ?>
					scope.settings_mode = 'profile';
					<?php endif; ?>
				});
			});
			$('#reminder-modal').on('hide.bs.modal', function (e) {
				window.location.href = "//app.contracthound.com/welcome";
			});
		</script>
    <?php $this->load->view('layout/analytics_snippets', ['intercom' => 1, 'google' => 1]); ?>

	</body>
</html>
