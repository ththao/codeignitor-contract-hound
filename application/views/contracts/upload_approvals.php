<!DOCTYPE html>
<html dir="ltr" lang="en-US">
	<!-- application layout -->
	<head>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8">
		<title>Upload Contract | Contract Hound</title>

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
		<script src="/ui/bootstrap-validator/dist/validator.min.js"></script>
		<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.4.5/angular.min.js"></script>
		<script src="//d2wy8f7a9ursnm.cloudfront.net/v7/bugsnag.min.js"></script>
		<script>Bugsnag.start({apiKey: '<?= $_ENV['BUGSNAG_API_KEY'] ?>', releaseStage: '<?= ENVIRONMENT ?>'});</script>
		<script src="/ui/js/app.js"></script>

		<link rel="stylesheet" type="text/css" href="/ui/jqueryui/jquery-ui-1.11.4/jquery-ui.structure.min.css" />
		<link rel="stylesheet" type="text/css" href="/ui/suggest/css/bootstrap-suggest.css" />
		<link rel="stylesheet" type="text/css" href="/ui/tokenfield/dist/css/bootstrap-tokenfield.min.css" />

		<link rel="stylesheet" type="text/css" href="/ui/css/app.css" />

	</head>
	<body data-ng-app="ContractHoundApp">
		<div class="modal fade" id="upload-contract">
			<div class="modal-container">
				<div class="modal-dialog">

					<div class="alert alert-danger" id="uploaderrormessage" style="display:none;">
					</div>

					<div class="modal-content" ng-hide="upload_step">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span data-icon="close-small">Close</span></button>
							<h3 class="modal-title">Add Contracts</h3>
							<p>You can add contracts by dragging them into this window. Contracts should be in a PDF or DOC format.</p>
						</div>
						<div class="modal-body">
							<div class="row">
								<div class="col-sm-8" full>
									<div class="row">
										<div class="col-xs-5">
											<img src="/ui/img/instructions/upload.png" class="graphic graphic-medium" />
										</div>
										<div class="col-xs-7">
											<h4>Drag &amp; Drop</h4>
											<p class="help-block">You can add contracts by dragging &amp; dropping the file into this window.</p>
										</div>
									</div>
								</div>
								<div class="col-sm-4">
									<h4 full>Browse</h4>
									<label id="single-add" class="btn btn-default btn-file btn-lg" data-placeholder="Choose Files"></label>
								</div>
							</div>
						</div>
						<div class="modal-footer">
							<a id="skip-to-files" style="display:none;" href="#" ng-click="upload_step=1"><em>Skip to folders (demo only)</em></a>
							<div class="navigator">
								<ul>
									<li class="active"><a href="#">1</a></li>
									<li><span>2</span></li>
									<li><span>3</span></li>
								</ul>
							</div>
						</div>
					</div>

					<div class="modal-content" ng-show="upload_step==1">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span data-icon="close-small">Close</span></button>
							<h3 class="modal-title"><?php echo lang('Choose a Board'); ?></h3>
							<p><?php echo lang('Organize these contracts by adding them to a board.'); ?></p>
						</div>
						<div class="modal-body">

							<select name="board_id" class="form-control input-lg" data-value="0">
								<option value="0"><?php echo lang('I\'ll choose a board later...'); ?></option>
								<?php foreach ($oBoards as $oBoard): ?>
								<option value="<?php echo $oBoard->board_id; ?>"><?php echud($oBoard->board_path); ?></option>
								<?php echo $oBoard->sub_board_options; ?>
								<?php endforeach; ?>
							</select>

							<div class="attachments">
								<div class="file-upload-preview-template">
									<div class="attachment attachment-editable">
										<div class="attachment-content">
											<div class="attachment-graphic">
												<span data-icon="file"></span>
											</div>
											<div class="attachment-body">
												<div class="attachment-name">
													<h6 data-dz-name> </h6>
												</div>
												<div class="attachment-extra">
													<div class="progress">
														<div class="progress-bar progress-bar-striped active" role="progressbar" data-dz-uploadprogress style="width: 0%;">
															<span class="sr-only">Uploading</span>
														</div>
													</div>
												</div>
											</div>
											<?php /*if ($iCurrentlyLoggedInMemberId == 1): ?>
											<div class="attachment-actions">
												<a href="#" class="attachment-action-remove">
													<span data-icon="close-small">Remove</span>
												</a>
											</div>
											<?php endif;*/ ?>
										</div>
									</div>
								</div>

							</div>

							<?php /*<p class="help-block">
								Drag &amp; Drop additional contracts or <a href="#">browse for files</a>.
							</p>*/ ?>

						</div>
						<div class="modal-footer">
							<a href="#" class="btn btn-lg btn-text" ng-click="upload_step=0">Back</a>
							<a href="#" class="btn btn-lg btn-primary" ng-click="upload_step=2">Next</a>
							<div class="navigator">
								<ul>
									<li><a href="#" ng-click="upload_step=0">1</a></li>
									<li class="active"><a href="#">2</a></li>
									<li><span>3</span></li>
								</ul>
							</div>
						</div>
					</div>

					<div class="modal-content" ng-show="upload_step==2">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span data-icon="close-small">Close</span></button>
							<h3 class="modal-title">Choose Team Members</h3>
							<p>Choose users to view, edit, and approve these contracts. You can also configure these settings later.</p>
						</div>
						<div class="modal-body">


							<form ng-submit="$('#add-users').val('').blur(); member_entered=true">
								<input class="form-control input-lg" placeholder="Add members by name, or email" id="add-users" />
								<input type="submit" class="hidden" />
							</form>

							<div class="members">
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
							</div>

							<?php if ($bCurrentSubHasApprovalAccess): ?>
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
													<div class="workflow-step" ng-show="workflow_1" id="workflow_1">
														<div class="workflow-members "></div>
														<div class="workflow-title">
															<div class="workflow-label">Step</div>
															<div class="workflow-option">
																<div class="dropdown">
																	<a href="#" class="workflow-option-link" data-toggle="dropdown"><span class="requirement">Require All</span> <span class="caret"></span></a>
																	<ul class="dropdown-menu">
																		<li><a href="#"><p class="help-block"><strong>Require Any</strong> Require only one approval before this contract proceeds to the next approval step.</p></a></li>
																		<li class="divider"></li>
																		<li><a href="#"><p class="help-block"><strong>Require All</strong> Require all members to approve before this contract proceeds to the next approval step.</p></a></li>
																	</ul>
																</div>
															</div>
															<div class="workflow-actions">
																<a href="#" ng-click="workflow_remove('workflow_1')"><span data-icon="close-small"></span></a>
															</div>
														</div>
														<div class="workflow-background"></div>
													</div>
													<div class="workflow-step" ng-show="workflow_2" id="workflow_2">
														<div class="workflow-members "></div>
														<div class="workflow-title">
															<div class="workflow-label">Step</div>
															<div class="workflow-option">
																<div class="dropdown">
																	<a href="#" class="workflow-option-link" data-toggle="dropdown"><span class="requirement">Require All</span> <span class="caret"></span></a>
																	<ul class="dropdown-menu">
																		<li><a href="#"><p class="help-block"><strong>Require Any</strong> Require only one approval before this contract proceeds to the next approval step.</p></a></li>
																		<li class="divider"></li>
																		<li><a href="#"><p class="help-block"><strong>Require All</strong> Require all members to approve before this contract proceeds to the next approval step.</p></a></li>
																	</ul>
																</div>
															</div>
															<div class="workflow-actions">
																<a href="#" ng-click="workflow_remove('workflow_2')"><span data-icon="close-small"></span></a>
															</div>
														</div>
														<div class="workflow-background"></div>
													</div>
													<div class="workflow-step" ng-show="workflow_3" id="workflow_3">
														<div class="workflow-members "></div>
														<div class="workflow-title">
															<div class="workflow-label">Step</div>
															<div class="workflow-option">
																<div class="dropdown">
																	<a href="#" class="workflow-option-link" data-toggle="dropdown"><span class="requirement">Require All</span> <span class="caret"></span></a>
																	<ul class="dropdown-menu">
																		<li><a href="#"><p class="help-block"><strong>Require Any</strong> Require only one approval before this contract proceeds to the next approval step.</p></a></li>
																		<li class="divider"></li>
																		<li><a href="#"><p class="help-block"><strong>Require All</strong> Require all members to approve before this contract proceeds to the next approval step.</p></a></li>
																	</ul>
																</div>
															</div>
															<div class="workflow-actions">
																<a href="#" ng-click="workflow_remove('workflow_3')"><span data-icon="close-small"></span></a>
															</div>
														</div>
														<div class="workflow-background"></div>
													</div>
													<div class="workflow-step" ng-show="workflow_4" id="workflow_4">
														<div class="workflow-members "></div>
														<div class="workflow-title">
															<div class="workflow-label">Step</div>
															<div class="workflow-option">
																<div class="dropdown">
																	<a href="#" class="workflow-option-link" data-toggle="dropdown"><span class="requirement">Require All</span> <span class="caret"></span></a>
																	<ul class="dropdown-menu">
																		<li><a href="#"><p class="help-block"><strong>Require Any</strong> Require only one approval before this contract proceeds to the next approval step.</p></a></li>
																		<li class="divider"></li>
																		<li><a href="#"><p class="help-block"><strong>Require All</strong> Require all members to approve before this contract proceeds to the next approval step.</p></a></li>
																	</ul>
																</div>
															</div>
															<div class="workflow-actions">
																<a href="#" ng-click="workflow_remove('workflow_4')"><span data-icon="close-small"></span></a>
															</div>
														</div>
														<div class="workflow-background"></div>
													</div>
													<div class="workflow-step" ng-show="workflow_5" id="workflow_5">
														<div class="workflow-members "></div>
														<div class="workflow-title">
															<div class="workflow-label">Step</div>
															<div class="workflow-option">
																<div class="dropdown">
																	<a href="#" class="workflow-option-link" data-toggle="dropdown"><span class="requirement">Require All</span> <span class="caret"></span></a>
																	<ul class="dropdown-menu">
																		<li><a href="#"><p class="help-block"><strong>Require Any</strong> Require only one approval before this contract proceeds to the next approval step.</p></a></li>
																		<li class="divider"></li>
																		<li><a href="#"><p class="help-block"><strong>Require All</strong> Require all members to approve before this contract proceeds to the next approval step.</p></a></li>
																	</ul>
																</div>
															</div>
															<div class="workflow-actions">
																<a href="#" ng-click="workflow_remove('workflow_5')"><span data-icon="close-small"></span></a>
															</div>
														</div>
														<div class="workflow-background"></div>
													</div>
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
							<a href="#" class="btn btn-lg btn-text" ng-click="upload_step=1">Back</a>
							<a id="finish-upload" href="#" class="btn btn-lg btn-primary">Finish</a>
							<div class="navigator">
								<ul>
									<li><a href="#" ng-click="upload_step=0">1</a></li>
									<li><a href="#" ng-click="upload_step=1">2</a></li>
									<li class="active"><span>3</span></li>
								</ul>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="dropzone">
			<div class="dropzone-container">
				<div class="dropzone-content">
					<div class="dropzone-body">
						<div class="text-center">
							<img src="/ui/img/instructions/upload-white.png" class="graphic graphic-medium" />
							<h1>Drop Contracts Here...</h1>
							<p class="text-large"><em>Contracts should be in a PDF or DOC format.</em></p>
						</div>
					</div>
				</div>
			</div>
		</div>

		<script>
			// remove members from a step
			$(document).on('click','.workflow-members .member-remove',function(){
				var members = $(this).closest('.workflow-members');
				$(this).closest('.member').remove();
				var count = members.find('.member').length;
				members.toggleClass('is-multiple',count>1);
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
					scope.files = [];
					scope.filesize = 0;
					scope.filesizeexceeded = false;
					scope.filesexceeded = false;
					scope.has_approvals = true;
					scope.require_approvals = false;
					scope.upload_step = 0;
					scope.workflow_1 = true;
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

			$('#upload-contract').modal('show');
			$('#upload-contract').on('hide.bs.modal', function (e) {
				window.location.href = "/contracts";
			});

			$(document).on('click','.dropdown-menu a',function(){
				$(this).parent().parent().parent().find('.member-level').html($(this).text());
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

			$('#finish-upload').click(function() {
				boardId = $('select[name=board_id]').val();
				userAccess = [];
				$('.member-editable').each(function() {
					if ($('input[type=hidden]',this).length) {
						sMember = $('input[type=hidden]',this).val()+'//'+$('.member-level',this).text().replace(' <span class="caret"></span>','');
						userAccess.push(sMember);
					}
				});

				workflowData = gatherWorkflow();
				//console.log(workflowData);

				$.ajax({
					url: '/contracts/ajax_finish_upload/',
					type: "POST",
					data: {
						board: boardId,
						users: userAccess,
						workflow: workflowData
					},
					dataType: "json",
					async: false,
					success: function (data) {
						console.log('success');
						if (data.success == 1) {
							if (data.contract_id !== undefined) {
								window.location.href = "/contracts/view/"+data.contract_id;
							} else {
								$('#upload-contract').modal('hide');
							}
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

			$(document).ready(function() {
				previewTemplate = $('.file-upload-preview-template').html();
				$('.file-upload-preview-template').remove();
				Dropzone.autoDiscover = false;
				var drop = new Dropzone("body",
					{
						url : '/contracts/upload_ajax',
						previewTemplate: previewTemplate,
						previewsContainer: ".attachments",
						maxFilesize : 20, // mb
						paramName : 'contract_file',
						uploadMultiple : true,
						maxFiles : 5,
						dictResponseError : 'Unable to complete request at this time.',
						dictFileTooBig : 'Files must be 15 MB or less.',
						dictMaxFilesExceeded : 'You can upload a maximun if 5 files at a time',
						success: function(file, response){
							console.log('uploaded: '+file);
							//alert(response);
						}
					}
				);
				drop.on("error", function(file, error) {
					if ($('#uploaderrormessage').is(':hidden')) {
						$('#uploaderrormessage').html('File '+file.name+' could not be added.&nbsp;&nbsp;&nbsp;'+error);
						$('#uploaderrormessage').show(error);
					} else {
						currentErrorMessage = $('#uploaderrormessage').html();
						$('#uploaderrormessage').html(currentErrorMessage+'<br/>File '+file.name+' could not be added.&nbsp;&nbsp;&nbsp;'+error);
					}
					$('.progress-bar',file.previewElement).removeClass('progress-bar-success').addClass('progress-bar-danger').width('100%');
					//drop.removeFile(file);
  				});

				drop.on("addedfile", function(file) {
					console.log(file.name + ' ' + file.size);
					$('#skip-to-files').click();
  				});
				drop.on('complete', function(file) {
					$('.progress .progress-bar',file.previewElement).removeClass('progress-bar-striped')
						.removeClass('active').addClass('progress-bar-success');
					$('.progress .progress-bar .sr-only',file.previewElement).text('100% Complete');
				});
				$('#single-add').click(function() {
					$('body').click();
				});
			});

			$(function() {
				$('.workflow-option .dropdown-menu a').click(function() {
					newText = $('strong',this).text();
					$(this).parent().parent().parent().find('.requirement').text(newText);
				});

				$(document).on('click','.member-action-remove',function(){
					$(this).parents('.member-editable').remove();
					return false;
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
					<?php foreach ($oUsers as $oMember):
						if ($oMember->member_id == $iCurrentlyLoggedInMemberId) {
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
							console.log('select: not real item');
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
										'<a href="#" class="member-action member-action-remove">'+
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
								.append( "<a href='#' class='text-italic'>Add member via email <span class='text-light'>— </span><span class='text-dark'>"+$( "#add-users" ).val()+"</span></a>" )
								.appendTo( ul );
						} else {
							return $( "<li>" )
								.append( "<div class='value-"+item.value+"'><h6>" + item.fullname + " <small>" + item.email + "</small></h6></div>" )
								.appendTo( ul );

						}
					};
				});
		</script>
    <?php $this->load->view('layout/analytics_snippets', ['intercom' => 1, 'google' => 1]); ?>

	</body>
</html>
