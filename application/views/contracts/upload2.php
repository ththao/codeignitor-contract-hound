<!DOCTYPE html>
<html dir="ltr" lang="en-US">
	<!-- application layout -->
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
					
					<div class="modal-content" ng-hide="upload_step">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span data-icon="close-small">Close</span></button>
							<h3 class="modal-title">Add Contracts</h3>
							<p>You can add contracts to Contract Hound by dragging them into this window. Contracts should be in a PDF or DOC format.</p>
						</div>
						<div class="modal-body">
		
							<div class="alert alert-warning" ng-show="filesexceeded && !filesizeexceeded">
								Contract Hound lets you upload 5 contracts at a time. Click continue to configure the contracts ready for upload listed below.
							</div>
		
							<div class="alert alert-danger" ng-show="filesizeexceeded && !filesexeeded">
								The following contracts exceeded our 20 MB file size limit: file_name_here.doc. Consider saving this file in a small format and try again.
							</div>
		
							<div class="alert alert-danger" ng-show="filesizeexceeded && filesexeeded">
								Contract Hound lets you upload 5 contracts at a time and up to 20 MB per upload. Please try again.
							</div>
		
							<div ng-show="!files.length">
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
										<label class="btn btn-default btn-file btn-lg" data-placeholder="Choose Files"><input type="file" multiple/></label>
									</div>
								</div>
							</div>
							<div ng-show="files.length">
								
								<div class="attachments">
									<div class="attachment attachment-editable" ng-repeat="file in files">
										<div class="attachment-content">
											<div class="attachment-graphic">
												<span data-icon="file"></span>
											</div>
											<div class="attachment-body">
												<div class="attachment-name">
													<h6>[[file.name]]</h6>
												</div>
											</div>
											<a href="#" class="attachment-action" ng-click="deleteFile(file)">
												<span data-icon="close-small">Remove</span>
											</a>
										</div>
									</div>
								</div>
		
								<p class="help-block">Click "Continue" when you've added each of your new contracts or <a href="#">browse for files</a>.</p>
		
							</div>
						</div>
						<div class="modal-footer">
							<a ng-show="files.length" href="#" ng-click="upload_step=1" class="btn btn-primary btn-lg">Continue</a>
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
							
							<select class="form-control input-lg" data-value="0">
								<option value="0">I’ll choose a board later...</option>
								<option value="1">Buy Side
								<option value="2">Sell-Side</option>
								<option value="3">Marketing</option>
								<option value="4">Xerox Contracts</option>
							</select>
							
		
							<div class="attachments">
								<div class="attachment attachment-editable" ng-repeat="file in files">
									<div class="attachment-content">
										<div class="attachment-graphic">
											<span data-icon="file"></span>
										</div>
										<div class="attachment-body">
											<div class="attachment-name">
												<h6>[[file.name]]</h6>
											</div>
										</div>
										<a href="#" class="attachment-action" ng-click="deleteFile(file)">
											<span data-icon="close-small">Remove</span>
										</a>
									</div>
								</div>
							</div>
		
						</div>
						<div class="modal-footer">
							<a href="#" class="btn btn-lg btn-text" ng-click="upload_step=0">Back</a>
							<a href="#" class="btn btn-lg btn-primary" ng-click="upload_step=2">Continue</a>
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
							<form ng-submit="$('#add-users').val('').blur(); member_entered=true">
								<input class="form-control input-lg" placeholder="Add members by name, email or @mention..." id="add-users" />
								<input type="submit" class="hidden" />
							</form>
		
							<div class="members">
								<div class="member member-editable">
									<div class="member-content">
										<div class="member-graphic" style="background-image: url(/ui/img/samples/avatar1.jpg)">
											<div class="avatar avatar-medium" style="background-image: url(/ui/img/samples/avatar1.jpg)">
												<img src="/ui/img/samples/avatar1.jpg" />
											</div>
										</div>
										<div class="member-body">
											<div class="member-name">
												<h6>John Doe</h6>
											</div>
											<div class="member-meta">
												<span>Owner</span>
											</div>
										</div>
									</div>
								</div>
								<div class="member member-editable">
									<div class="member-content dropdown">
										<div class="member-graphic" data-toggle="dropdown" style="background-image: url(/ui/img/samples/avatar2.jpg)">
											<div class="avatar avatar-medium" style="background-image: url(/ui/img/samples/avatar2.jpg)">
												<img src="/ui/img/samples/avatar2.jpg" />
											</div>
										</div>
										<div class="member-body" data-toggle="dropdown">
											<div class="member-name">
												<h6>Ron Dillard</h6>
											</div>
											<div class="member-meta">
												<span class="link"><span id="role1">Editor</span> <span class="caret"></span></span>
											</div>
										</div>
										<a href="#" class="member-action">
											<span data-icon="close-small">Remove</span>
										</a>
										<ul class="dropdown-menu">
											<li><a href="#">Owner</a></li>
											<li><a href="#" onclick="$('#role1').html('Read-Only');">Read-Only</a></li>
										</ul>
									</div>
								</div>
								<div class="member member-editable" ng-show="member_entered">
									<div class="member-content dropdown">
										<div class="member-graphic" data-toggle="dropdown" style="background-image: url(/ui/img/samples/avatar3.jpg)">
											<div class="avatar avatar-medium" style="background-image: url(/ui/img/samples/avatar3.jpg)">
												<img src="/ui/img/samples/avatar3.jpg" />
											</div>
										</div>
										<div class="member-body" data-toggle="dropdown">
											<div class="member-name">
												<h6>Martha Escobedo</h6>
											</div>
											<div class="member-meta">
												<span class="link"><span id="role2">Read-Only</span> <span class="caret"></span></span>
											</div>
										</div>
										<a href="#" class="member-action">
											<span data-icon="close-small">Remove</span>
										</a>
										<ul class="dropdown-menu">
											<li><a href="#">Owner</a></li>
											<li><a href="#" onclick="$('#role2').html('Editor');">Editor</a></li>
										</ul>
									</div>
								</div>
							</div>
						</div>
						<div class="modal-footer">
							<a href="#" class="btn btn-lg btn-text" ng-click="upload_step=1">Back</a>
							<a href="#" id="finish-upload" class="btn btn-lg btn-primary" ng-click="upload_step=3">Finish</a>
							<div class="navigator">
								<ul>
									<li><a href="#" ng-click="upload_step=0">1</a></li>
									<li><a href="#" ng-click="upload_step=1">2</a></li>
									<li class="active"><span>3</span></li>
								</ul>
							</div>
						</div>
					</div>
		
					<div class="modal-content" ng-show="upload_step==3">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span data-icon="close-small">Close</span></button>
							<h3 class="modal-title">Complete Your Contracts</h3>
							<p>You successfully created 3 contracts in Board Name Goes Here. Now you can complete each contract by configuring reminders, renewal dates, and collaborators.</p>
						</div>
						<div class="modal-body">
		
							<div class="table-responsive table-alignment">
								<table class="table table-hover table-borderless table-justified table-compact">
									<thead>
										<tr>
											<th>Contract</th>
											<th>Owner</th>
											<th class="cell-small"></th>
										</tr>
									</thead>
									<tbody>
										<tr class="warning" data-toggle="tooltip" title="<div class='tooltip-warning'><h6>Your Account is Over Quota</h6><p>You have reached your quota of 50 contracts. Please upgrade your account to save this contract.</p></div>">
											<th><a href="../contract" class="cell-link">Contract_filename_237898327.pdf<span class="label label-warning">over quota</span></a></th>
											<td><a href="../profile" class="cell-link alternate">John Doe</a></td>
											<td class="cell-small"><a href="../profile" class="cell-link"><div class="avatar" style="background-image: url(/ui/img/samples/avatar1.jpg)"><img src="/ui/img/samples/avatar1.jpg" /></div></a></td>
										</tr>
										<tr class="success" data-toggle="tooltip" title="<div class='tooltip-success'><h6>Complete Contract Details</h6><p>Click this contract to configure details and notifications.</p></div>">
											<th><a href="../contract" class="cell-link">Contract_filename_237898326.pdf<span class="label label-success">new</span></a></th>
											<td><a href="../profile" class="cell-link alternate">John Doe</a></td>
											<td class="cell-small"><a href="../profile" class="cell-link"><div class="avatar" style="background-image: url(/ui/img/samples/avatar1.jpg)"><img src="/ui/img/samples/avatar1.jpg" /></div></a></td>
										</tr>
										<tr class="success" data-toggle="tooltip" title="<div class='tooltip-success'><h6>Complete Contract Details</h6><p>Click this contract to configure details and notifications.</p></div>">
											<th><a href="../contract" class="cell-link">Contract_filename_237898325.pdf<span class="label label-success">new</span></a></th>
											<td><a href="../profile" class="cell-link alternate">John Doe</a></td>
											<td class="cell-small"><a href="../profile" class="cell-link"><div class="avatar" style="background-image: url(/ui/img/samples/avatar1.jpg)"><img src="/ui/img/samples/avatar1.jpg" /></div></a></td>
										</tr>
									</tbody>
								</table>
							</div>
						</div>
						<div class="modal-footer">
							<a href="../browse-contracts" class="btn btn-text">View All Contracts</a>
						</div>
					</div>
		
				</div>
			</div>
		</div>
		
		<script>
		$('#upload-contract').modal('show');
		</script>
		
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
			Dropzone.autoDiscover = false;
			$(function() {
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
					{value: null},
				];

				$( "#add-users" ).autocomplete({
					minLength: 0,
					source: user_search,
					focus: function( event, ui ) {
						$( "#add-users" ).val( ui.item.value );
						return false;
					},
					select: function( event, ui ) {
						$( "#add-users" ).val( ui.item.fullname );
						return false;
					}
				})
				.autocomplete( "instance" )
				._renderItem = function( ul, item ) {
					if ( item.value === null ) {
						return $( "<li class='ui-separator'>" )
							.append( "<a href='#' class='text-italic'>Add member via email <span class='text-light'>— </span><span class='text-dark'>"+$( "#add-users" ).val()+"</span></a>" )
							.appendTo( ul );
					} else {
						return $( "<li>" )
							.append( "<div class='value-"+item.value+"'><h6>" + item.fullname + " <small>" + item.email + "</small></h6></div>" )
							.appendTo( ul );	
					}
				};

				var scope = angular.element($('body')[0]).scope();
				scope.$apply(function() {
					scope.files = [];
					scope.filesize = 0;
					scope.filesizeexceeded = false;
					scope.filesexceeded = false;
					scope.deleteFile = function (file) {
						var index = scope.files.indexOf(file);
						scope.files.splice(index, 1);
						scope.filesize -= file.size;
					}
				});
		
				var maxFiles = 5;
				var maxSize = 2;
		
				var mainDrop = $("body").dropzone({
					url : '/contracts/upload_ajax',
					thumbnailHeight: null,
					thumbnailWidth: null,
					previewTemplate: '<div style="display: none;"></div>',
					maxFiles: maxFiles, // 3 Files
					maxFilesize: maxSize, // 2 MB
					init: function(){
						this.on('addedfile', function(file) {
							scope.$apply(function() {
								if ( scope.files.length >= maxFiles ) {
									scope.filesexceeded = true;
									this.removeFile(file);
								} else if ( scope.filesize + ( file.size / 1000000 ) >= maxSize ) {
									scope.filesizeexceeded = true;
									mainDrop.removeFile(file);
								} else {
									scope.filesizeexceeded = false;
									scope.filesexceeded = false;
									scope.files.push({
										name: file.name,
										size: ( file.size / 1000 )
									});
									scope.filesize += ( file.size / 1000000 );
								}
							});
						});
						this.on('maxfilesexceeded',function(file){
							console.log(file)
						});
						this.on('dictMaxFilesExceeded',function(file){
							console.log(file)
						});
					}
				});
			});
		</script>
		
		<?php $this->load->view('layout/analytics_snippets', ['intercom' => 1, 'google' => 1]); ?>
	</body>
</html>
