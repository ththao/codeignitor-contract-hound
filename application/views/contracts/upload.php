<!DOCTYPE html>
<html dir="ltr" lang="en-US">
	<head>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8">
		<title>Upload Contracts | Contract Hound</title>
		
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
					<div class="modal-content main-upload" ng-hide="upload_step">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span data-icon="close-small">Close</span></button>
							<h3 class="modal-title">Add Contracts</h3>
							<p>You can add contracts to Contract Hound by dragging them into this window. Contracts should be in a PDF or DOC format.</p>
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
											<p class="help-block">You can add contracts to Contract Hound by dragging &amp; dropping the file into this window.</p>
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
							<a id="skip-to-files" style="display:none;" href="#" ng-click="upload_step=1"><em>Skip to boards (demo only)</em></a>
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
							<h3 class="modal-title">Choose a Board</h3>
							<p>Organize these contracts by adding them to a board.</p>
						</div>
						<div class="modal-body">
							
							<select name="board_id" class="form-control input-lg" data-value="0">
								<option selected value="0">I’ll choose a board later...</option>
								<?php foreach ($oBoards as $oBoard): ?>
								<option value="<?php echo $oBoard->board_id; ?>"><?php echud($oBoard->name); ?></option>
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
											<?php /*<div class="attachment-actions">
												<a href="#" class="attachment-action-remove">
													<span data-icon="close-small">Remove</span>
												</a>
											</div>*/ ?>
										</div>
									</div>
								</div>

							</div>
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
							<p>Choose who can see and edit these contracts. You change this information on individual contracts later.</p>
						</div>
						<div class="modal-body">
							<p><input class="form-control input-lg" placeholder="Add members by name, email or @mention..." id="add-users" /></p>
							
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
												<h6><?php $oOwner->name?echud($oOwner->name):echud($oOwner->email); ?></h6>
											</div>
											<div class="member-meta">
												<span>Owner</span>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="modal-footer">
							<a href="#" class="btn btn-lg btn-text" ng-click="upload_step=1">Back</a>
							<a id="finish-upload" href="#" class="btn btn-lg btn-primary"<?php /* data-dismiss="modal"*/ ?>>Finish</a>
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
			$('#upload-contract').modal('show');
			$('#upload-contract').on('hide.bs.modal', function (e) {
				window.location.href = "//app.contracthound.com/contracts";
			});

			$(document).on('click','.dropdown-menu a',function(){
				$(this).parent().parent().parent().find('.member-level').html($(this).text());
			});

			$('#finish-upload').click(function() {
				boardId = $('select[name=board_id]').val();
				userAccess = [];
				$('.member-editable').each(function() {
					if ($('input[type=hidden]',this).length) {
						sMember = $('input[type=hidden]',this).val()+'//'+$('.member-level',this).text().replace(' <span class="caret"></span>','');
						userAccess.push(sMember);
					}
				});
				console.log(userAccess);

				$.ajax({
					url: 'https://app.contracthound.com/contracts/ajax_finish_upload/',
					type: "POST",
					data: {
						board_id: boardId,
						users: userAccess
					},
					dataType: "json",
					async: false,
					success: function (data) {
						console.log('success');
						if (data.success == 1) {
							if (data.contract_id !== undefined) {
								window.location.href = "//app.contracthound.com/contracts/view/"+data.contract_id;
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
						url : 'https://app.contracthound.com/contracts/upload_ajax',
						previewTemplate: previewTemplate,
						previewsContainer: ".attachments",
						maxFilesize : 15, // 15mb
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
				drop.on("addedfile", function(file) {
					//console.log(file);
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
						$( "#add-users" ).val( ui.item.value );
						return false;
					},
					select: function( event, ui ) {
						$( "#add-users" ).val('');
						// add to list
						sAvatar = '/ui/img/avatars/default.png';
						if (ui.item.avatar.length) {
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
												'<h6>'+ui.item.fullname+'</h6>'+
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
