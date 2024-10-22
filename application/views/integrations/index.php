<div class="layout-section">
    <div class="tabs">
        <div class="tabs-content">
            <div class="tabs-header">
                <h2>Integrations</h2>
            </div>
        </div>
    </div>
    <p class="help-block">Authorize and configure integrations for your entire account. You may be asked to provide your
        credentials for integrated applications before seeing those features within Contract Hound.</p>

    <div class="integration">
        <div class="integration-content">
            <div class="integration-graphic">
                <img src="https://www.docusign.com/sites/all/themes/custom/docusign/favicons/apple-touch-icon-180x180.png" />
            </div>
            <div class="integration-body">
                <div class="integration-title"><h4>Docusign <small ng-show="docusign_activated" class="text-success">(activated)</small></h4></div>
                <div class="integration-description">
                    <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod
                        tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis
                        nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. </p></div>
                <div class="integration-options" ng-show="!docusign_activated">
                    <a href="#" ng-click="popup('../external-auth')" class="btn btn-sm btn-success">Enable</a>
                    <a target="_blank" href="https://go.docusign.com/o/trial/" target="_blank" class="btn btn-sm btn-text">Free Trial</a>
                    <a  target="_blank"href="https://docusign.com" target="_blank" class="btn btn-sm btn-text">View Website</a>
                </div>
                <div class="integration-options" ng-show="docusign_activated">
                    <a href="#" ng-click="revoke()" class="btn btn-sm btn-text text-danger">Disconnect</a>
                    <a href="../docusign-settings" class="btn btn-sm btn-text">Settings</a>
                    <a target="_blank" href="https://docusign.com" target="_blank" class="btn btn-sm btn-text">View Website</a>
                </div>
            </div>
        </div>

        <div class="divider divider-gap">
            <div class="divider-content">
                <div class="divider-title">
                    <h6>Upcoming Integrations</h6>
                </div>
                <div class="divider-separator">
                    <hr/>
                </div>
            </div>
        </div>

        <p class="help-block">Contract Hound is partnering with great technologies like Lorem ipsum dolor sit amet, consectetur
            adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad. Lorem ipsum dolor
            sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut.</p>

        <a target="_blank" href="https://pandadoc.com" class="integration integration-small">
            <div class="integration-content">
                <div class="integration-graphic">
                    <img src="https://lh5.ggpht.com/JJ6wUCq79bqSHstY3mbk1-Vd6bQuxqpebPU0sXbZPkutoPEbWxklqmHZPVzVCS9l3Q=w300" />
                </div>
                <div class="integration-body">
                    <div class="integration-title"><h6>Pandadoc</h6></div>
                </div>
            </div>
        </a>

        <a target="_blank" href="https://pandadoc.com" class="integration integration-small">
            <div class="integration-content">
                <div class="integration-graphic">
                    <img src="https://www.box.com/themes/custom/box/favicons/apple-touch-icon.png?v=wAvXQoyyWO" />
                </div>
                <div class="integration-body">
                    <div class="integration-title"><h6>Box.com</h6></div>
                </div>
            </div>
        </a>

        <div class="divider divider-gap">
            <div class="divider-content">
                <div class="divider-title">
                    <h6>Looking for more?</h6>
                </div>
                <div class="divider-separator">
                    <hr/>
                </div>
                <div class="divider-actions">
                    <a href="#feedback" class="btn btn-info btn-sm" data-toggle="modal">
                    request an integration
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="feedback">
	<div class="modal-container">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span data-icon="close-small">Close</span></button>
					<h3 class="modal-title">Request an Integration</h3>
				</div>
				<div class="modal-body">
					<form class="details" >
						<div class="form-grid form-minimal">
							<table>
								<tr>
									<td class="form-label"><label>App Name</label></td>
									<td class="form-response">
										<input class="form-control" type="text" placeholder="name of integration/service..." />
									</td>
								</tr>
								<tr>
									<td class="form-label"><label>App URL</label></td>
									<td class="form-response">
										<input class="form-control" type="url" placeholder="website of service..." />
									</td>
								</tr>
								<tr>
									<td class="form-label"><label>Description</label></td>
									<td class="form-response">
										<textarea class="form-control" placeholder="Please tell us what specific features you would use beween this app
                                            and Contract Hound..."></textarea>
									</td>
								</tr>
							</table>
						</div>
					</form>

				</div>
				<div class="modal-footer">
					<a href="#" class="btn btn-primary">Submit</a>
				</div>
			</div>
		</div>
	</div>
</div>



<script>
$(document).ready(function(){
	var scope = angular.element($('body')[0]).scope();
	scope.$apply(function() {
		scope.popup = function(site){
			var href = site;
			var target = 'new';
			var width = 480;
			var height = 480;
			var left = (screen.width - width)/2;
			var top = (screen.height - height)/2;
			var popup = window.open(href,target,'width='+width+',height='+height+',top='+top+',left='+left+',titlebar=no,toolbar=no,location=no,status=no,menubar=no');
			popup.onbeforeunload = function(){
				scope.$apply(function() {
					scope.docusign_activated = true;
				});
				$.notify( { title: 'Docusign Enabled', message: 'You can now sign and annotate documents directly from Contract Hound.' } , { type: 'success' } );
			}
		}
		scope.revoke = function(){
			if ( confirm('Are you sure you want to disconnect your Docusign account from Contract Hound?') ) {
				scope.docusign_activated = false;
				$.notify( { title: 'Docusign Disconnected', message: 'Your Docusign account is now disconnected from Contract Hound. You may reactivate the integration at any time.' } , { type: 'info' } );
			}
		}
	});
});
</script>
