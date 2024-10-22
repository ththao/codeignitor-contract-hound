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
		<div class="modal fade" id="upload-contract">
			<div class="modal-container">
				<div class="modal-dialog">
					<form id="form-update-team">
					<div class="modal-content" ng-hide="upload_step">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span data-icon="close-small">Close</span></button>
							<h3 class="modal-title">Choose User Contract Access</h3>
							<p>Choose who can see and edit these contracts. You change this information on individual contracts later.</p>
						</div>
						<div class="modal-body">
							<p><input name="new_member" class="form-control input-lg" placeholder="Add users by their email address" id="add-users" /></p>

							<div class="members">
								<?php $oOwner = $aTeamMembers[$oContract->owner_id]; ?>
								<div class="member member-editable">
									<div class="member-content">
										<div class="member-graphic">
											<div class="avatar avatar-medium" style="background-image: url(<?php
											if ($oOwner->avatar): ?>/uas/<?php echo $oOwner->avatar; ?><?php else: ?>/ui/img/avatars/default.png<?php endif;
											?>)">
												<img src="<?php
												if ($oOwner->avatar): ?>/uas/<?php echo $oOwner->avatar; ?><?php else: ?>/ui/img/avatars/default.png<?php endif;
												?>" />
											</div>
										</div>
										<div class="member-body">
											<div class="member-name">
												<h6 data-memid="<?php echo $oOwner->member_id; ?>"><?php $oOwner->name?echud($oOwner->name):echud($oOwner->email); ?></h6>
											</div>
											<div class="member-meta">
												<span>Owner</span>
											</div>
										</div>
									</div>
								</div>

								<?php foreach ($aTeamMembers as $oMember):
									if ($oMember->member_id == $oContract->owner_id || $oMember->status == MemberModel::StatusDeleted) {
										continue;
									} ?>
									<div class="member member-editable">
										<div class="member-content">
											<div class="member-graphic">
												<div class="avatar avatar-medium" style="background-image: url(<?php
												if ($oMember->avatar): ?>/uas/<?php echo $oMember->avatar; ?><?php else: ?>/ui/img/avatars/default.png<?php endif;
												?>)">
													<img src="<?php
													if ($oMember->avatar): ?>/uas/<?php echo $oMember->avatar; ?><?php else: ?>/ui/img/avatars/default.png<?php endif;
													?>" />
												</div>
											</div>
											<div class="member-body">
												<div class="member-name">
													<h6 data-memid="<?php echo $oMember->member_id; ?>"><?php $oMember->name?echud($oMember->name):echud($oMember->email); ?></h6>
												</div>
												<div class="member-meta">
													<div class="dropdown">
														<a href="#" data-toggle="dropdown"><span class="member-level"><?php echo $oMember->level; ?></span> <span class="caret"></span></a>
														<ul class="dropdown-menu">
															<li><a href="#">Editor</a></li>
															<li><a href="#">View Only</a></li>
														</ul>
													</div>
												</div>
											</div>
											<a href="#" class="member-action member-remove">
												<input type="hidden" value="<?php echo $oMember->member_id; ?>">
												<span data-icon="close-small">Remove</span>
											</a>
										</div>
									</div>
								<?php endforeach; ?>
							</div>

							<?php if (!empty($bCurrentSubHasApprovalAccess)): ?>
							<div ng-show="has_approvals">
								<div class="divider divider-flush">
									<div class="divider-content">
										<div class="divider-title">
											<h6>Approvals</h6>
										</div>
										<div class="divider-separator">
											<hr/>
										</div>
										<div class="divider-actions">
											<a href="#" class="btn btn-default btn-sm" ng-click="require_approvals=true" ng-show="!require_approvals">Add Approvals</a>
											<a href="#" class="btn btn-default btn-sm" ng-click="approvals_remove()" ng-show="require_approvals">Remove Approvals</a>
										</div>
									</div>
								</div>

								<div class="workflow workflow-editable" ng-show="require_approvals">
									<div class="workflow-content">
										<div class="workflow-header">
											<p class="help-block">Drag team members to a step in the approval workflow below. Members in the same step may approve in any order.</p>
										</div>
										<div class="workflow-body">
											<div class="workflow-steps">
												<div class="workflow-steps-list">
													<?php for ($iWorkflowId = 1; $iWorkflowId < 6; $iWorkflowId++): ?>
													<div class="workflow-step" ng-show="workflow_<?php echo $iWorkflowId; ?>" id="workflow_<?php echo $iWorkflowId; ?>">
														<div class="workflow-members <?php
															if (!empty($aApprovalStepsSorted[$iWorkflowId]) && count($aApprovalStepsSorted[$iWorkflowId]['steps']) > 1):?> is-multiple<?php endif; ?>">
															<?php if (!empty($aApprovalStepsSorted[$iWorkflowId])):
																foreach ($aApprovalStepsSorted[$iWorkflowId]['steps'] as $oStep): ?>
															<div class="member member-editable ui-draggable ui-draggable-handle">
																<div class="member-content">
																	<div class="member-graphic">
																		<div class="avatar avatar-medium" style="background-image: url(<?php
																			if ($oStep->avatar): ?>/uas/<?php echo $oStep->avatar; ?><?php else: ?>/ui/img/avatars/default.png<?php endif;
																				?>)">
																			<img src="<?php
																				if ($oStep->avatar): ?>/uas/<?php echo $oStep->avatar; ?><?php else: ?>/ui/img/avatars/default.png<?php endif;
																					?>">
																		</div>
																	</div>
																	<div class="member-body">
																		<div class="member-name">
																			<h6 data-memid="<?php echo $oStep->member_id; ?>"><?php $oStep->last_name?echud($oStep->first_name.' '.$oStep->last_name):echud($oStep->email); ?></h6>
																		</div>
																	</div>
																	<a href="#" class="member-action member-remove">
																		<span data-icon="close-small">Remove</span>
																	</a>
																</div>
															</div>
															<?php endforeach; endif; ?>
														</div>
														<div class="workflow-title">
															<div class="workflow-label">Step</div>
															<div class="workflow-option">
																<div class="dropdown">
																	<a href="#" class="workflow-option-link" data-toggle="dropdown">
																		<span class="requirement"><?php
																			if (!isset($aApprovalStepsSorted[$iWorkflowId]) || $aApprovalStepsSorted[$iWorkflowId]['type'] == 0):
																				?>Require All<?php else: ?>Require Any<?php endif; ?></span>
																		<span class="caret"></span></a>
																	<ul class="dropdown-menu">
																		<li><a href="#"><p class="help-block"><strong>Require Any</strong> Require only one approval before this contract proceeds to the next approval step.</p></a></li>
																		<li class="divider"></li>
																		<li><a href="#"><p class="help-block"><strong>Require All</strong> Require all members to approve before this contract proceeds to the next approval step.</p></a></li>
																	</ul>
																</div>
															</div>
															<div class="workflow-actions">
																<a href="#" ng-click="workflow_remove('workflow_<?php echo $iWorkflowId; ?>')"><span data-icon="close-small"></span></a>
															</div>
														</div>
														<div class="workflow-background"></div>
													</div>
													<?php endfor; ?>
													<a href="#" class="workflow-step workflow-step-add" ng-click="workflow_add()">
														<div class="workflow-background"></div>
														<div><span data-icon="add">Add</span></div>
													</a>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
							<?php endif; ?>

						</div>
						<div class="modal-footer">
							<a href="<?php /*echo '/contracts/view/'.$oContract->contract_id;*/ ?>#" class="btn btn-lg btn-text" data-dismiss="modal">Cancel</a>
							<button id="finish-update" type="submit" name="submit" class="btn btn-lg btn-primary">Finish</button>
						</div>
					</div>
					</form>
				</div>
			</div>
		</div>

		<script>
			$('#upload-contract').modal('show');

			$('#form-update-team').submit(function() {
				return false;
			});

			// remove members from a step
			$(document).on('click','.workflow-members .member-remove',function(){
				var members = $(this).closest('.workflow-members');
				$(this).closest('.member').remove();
				var count = members.find('.member').length;
				members.toggleClass('is-multiple',count>1);
				return false;
			});

			$(document).on('click','.dropdown-menu a',function(){
				$(this).parent().parent().parent().find('.member-level').html($(this).text());
			});

			$(document).on('click','.members .member-remove',function(){
				$(this).closest('.member').remove();
				return false;
			});

			$('.members .member').draggable({
				// connect to a sortable element
				connectToSortable: '.workflow-members',
				// append to body to avoid overflow clonflicts
				appendTo: 'body',
				// use a clone so that original members stay put
				helper: 'clone'
			});

			$('.workflow-members').sortable({
				// allow movement between steps
				connectWith: '.workflow-members',
				// add hover states
				over: function(event,ui){ $(this).addClass('is-over'); },
				out: function(event,ui){ $(this).removeClass('is-over'); },
				// checks after drop
				receive: function(event,ui){
					if ( $(ui.helper).siblings('.member[data-member="'+$(ui.helper).attr('data-member')+'"]').length ) {
						// remove if duplicate
						$(ui.helper).remove();
					} else {
						// remove non-essential content
						$(ui.helper).removeAttr('style')
						$(ui.helper).find('.member-content').removeClass('dropdown');
						$(ui.helper).find('.dropdown-menu').remove();

						// add remove if needed
						if ($(ui.helper).find('.member-remove').length == 0) {
							$(ui.helper).find('.member-body').after(
								'<a href="#" class="member-action member-remove">'+
									'<span data-icon="close-small">Remove</span>'+
								'</a>');
						}

					}
					// check if multiple
					$('.workflow-members').each(function(){
						var count = $(this).find('.member').length;
						$(this).toggleClass('is-multiple',count>1);
					});
				}
			});

			$(document).ready(function(){
				var scope = angular.element($('body')[0]).scope();
				scope.$apply(function() {
					scope.has_approvals = true;
					scope.require_approvals = <?php if (!empty($aApprovalStepsSorted)) { echo 'true'; } else { echo 'false'; } ?>;
					scope.upload_step = 0;
					scope.workflow_1 = true;
					<?php if (!empty($aApprovalStepsSorted[2])): ?>scope.workflow_2 = true;<?php endif; ?>
					<?php if (!empty($aApprovalStepsSorted[3])): ?>scope.workflow_3 = true;<?php endif; ?>
					<?php if (!empty($aApprovalStepsSorted[4])): ?>scope.workflow_4 = true;<?php endif; ?>
					<?php if (!empty($aApprovalStepsSorted[5])): ?>scope.workflow_5 = true;<?php endif; ?>
					scope.workflow_add = function(){
						for ( var i = 1 ; i <= 5 ; i++ ) {
							if ( !scope['workflow_'+i] ) {
								scope['workflow_'+i] = true;
								return;
							}
						}
						return false;
					}
					scope.workflow_remove = function(item){
						scope[item] = false;
						$('#'+item).removeClass('is-multiple').find('.member').remove();
						return false;
					}
					scope.approvals_remove = function(){
						$('.workflow-members').removeClass('is-multiple').find('.member').remove();
						scope.require_approvals = false;
						scope.workflow_1 = true;
						scope.workflow_2 = false;
						scope.workflow_3 = false;
						scope.workflow_4 = false;
						scope.workflow_5 = false;
						return false;
					}
				});
			});

			var gatherWorkflow = function() {
				aWorkflowStepData = [];
				$('.workflow-step').each(function() {
					stepId = $(this).prop('id').replace('workflow_','');
					var memberIds = [];
					$('h6',$(this)).each(function(){
						memberIds.push($(this).data('memid'));
					});

					if (stepId > 0 && $(this).is(':visible')) {
						stepType = $('.workflow-option-link',$(this)).text().trim();
						aWorkflowStepData.push({'members' : memberIds , 'type' : stepType});
					}
				});

				return aWorkflowStepData;
			}

			$(function() {
				$('#finish-update').click(function() {
					boardId = $('select[name=board_id]').val();
					userAccess = [];
					$('.member-editable').each(function() {
						if ($('input[type=hidden]',this).length) {
							sMember = $('input[type=hidden]',this).val()+'//'+$('.member-level',this).text().replace(' <span class="caret"></span>','');
							userAccess.push(sMember);
						}
					});

					workflowData = gatherWorkflow();
					console.log(workflowData);

					$.ajax({
						url: '/contracts/ajax_update_team/<?php echo $oContract->contract_id; ?>',
						type: "POST",
						data: {
							users: userAccess,
							workflow: workflowData
						},
						dataType: "json",
						async: false,
						success: function (data) {
							console.log('success');
							if (data.success == 1) {
								window.location.href = "/contracts/view/<?php echo $oContract->contract_id; ?>";
							} else {
								console.log(data.error);
								alert('Unable to complete request.  Please try again later.');
							}
						},
						error: function () {
							console.log('error');
							alert('Unable to complete request.  Please try again later.');
						}
					});

					return false;
				});

				$('.workflow-option .dropdown-menu a').click(function() {
					newText = $('strong',this).text();
					$(this).parent().parent().parent().find('.requirement').text(newText);
				});

				function user_search(request, response) {
					function hasMatch(s) { return s && s.toLowerCase().indexOf(request.term.toLowerCase())!==-1; }
					var i, l, obj, matches = [];
					if ( request.term === "" ) { response([]); return; }
					for (i = 0, l = users.length; i<l; i++) {
						obj = users[i];
						if (
							hasMatch('@'+obj.value)
							|| hasMatch(obj.fullname)
							|| hasMatch(obj.email)
							|| (obj.value===null)
						) {
							matches.push(obj);
						}
					}
					response(matches);
				}

				var users = [
					<?php foreach ($oAccountMembers as $oMember):
						if ($oMember->member_id == $oOwner->member_id) {
							continue;
						} ?>
					{value: '<?php echo $oMember->email; ?>', memid: '<?php echo $oMember->member_id; ?>', fullname: '<?php $oMember->name?echud($oMember->name):echud($oMember->email); ?>', email: '<?php echud($oMember->email); ?>', avatar: '<?php echo $oMember->avatar; ?>'},
					<?php endforeach; ?>
					{value: null}
				];

				$( "#add-users" ).autocomplete({
					minLength: 0,
					source: user_search,
					focus: function( event, ui ) {
						if (ui.item.memid === undefined) {
							console.log('focus: not real item');
							return false;
						}

						$( "#add-users" ).val( ui.item.value );
						return false;
					},
					select: function( event, ui ) {
						if (ui.item.memid === undefined) {
							console.log('not real item');
							return false;
						}

						if ($('input[type=hidden][value='+ui.item.memid+']').length) {
							console.log('member already in list');
							$( "#add-users" ).val('');
							return false;
						}

						$( "#add-users" ).val('');
						// add to list
						sAvatar = '/ui/img/avatars/default.png';
						if (ui.item.avatar !== undefined && ui.item.avatar.length) {
							sAvatar = '/uas/'+ui.item.avatar;
						}
						sNewUser =
							'<div class="member member-editable">'+
									'<div class="member-content dropdown">'+
										'<div class="member-graphic" data-toggle="dropdown" style="background-image: url('+sAvatar+')">'+
											'<div class="avatar avatar-medium" style="background-image: url('+sAvatar+')">'+
												'<img src="'+sAvatar+'" />'+
											'</div>'+
										'</div>'+
										'<div class="member-body" data-toggle="dropdown">'+
											'<div class="member-name">'+
												'<h6 data-memid="'+ui.item.memid+'">'+ui.item.fullname+'</h6>'+
											'</div>'+
											'<div class="member-meta">'+
												'<span class="link"><span class="member-level">View Only</span> <span class="caret"></span></span>'+
											'</div>'+
										'</div>'+
										'<a href="#" class="member-action member-remove">'+
											'<input type="hidden" value="'+ui.item.memid+'">'+
											'<span data-icon="close-small">Remove</span>'+
										'</a>'+
										'<ul class="dropdown-menu">'+
											'<li><a href="#">Editor</a></li>'+
											'<li><a href="#">View Only</a></li>'+
										'</ul>'+
									'</div>'+
								'</div>';
						$('.members').append(sNewUser);
						$('.members .member').last().draggable({
							// connect to a sortable element
							connectToSortable: '.workflow-members',
							// append to body to avoid overflow clonflicts
							appendTo: 'body',
							// use a clone so that original members stay put
							helper: 'clone'
						});
						return false;
					}
				})
				.autocomplete( "instance" )
				._renderItem = function( ul, item ) {
					if ( item.value === null ) {
						return $( "<li class='ui-separator'>" )
							.append( "<a href='#' class='text-italic'>Add member via email or name <span class='text-light'>— </span><span class='text-dark'>"+$( "#add-users" ).val()+"</span></a>" )
							.appendTo( ul );
					} else {
						return $( "<li>" )
							.append( "<div class='value-"+item.value+"'><h6>" + item.fullname + " <small>" + item.email + "</small></h6></div>" )
							.appendTo( ul );

					}
				};
			});

			<?php if ($this->session->flashdata('error')): ?>
			var notifications = new Array(
				[{ title: 'Error:', message: '<?php echo $this->session->flashdata('error'); ?>' },{ type: 'danger' }]
			);
			<?php endif; ?>

			$('#upload-contract').on('hide.bs.modal', function (e) {
				window.location.href = "/contracts/view/<?php echo $oContract->contract_id; ?>";
			});
		</script>
    <?php $this->load->view('layout/analytics_snippets', ['intercom' => 1, 'google' => 1]); ?>
      
	</body>
</html>
