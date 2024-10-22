					<div class="layout-section">
						<div class="tabs">
							<div class="tabs-content">
								<div class="tabs-header">
									<h2>DocuSign</h2>
								</div>
							</div>
						</div>
						<div>
						
							<div class="integration integration-large">
								<div class="integration-content">
									<div class="integration-graphic">
										<img src="https://www.docusign.com/sites/all/themes/custom/docusign/favicons/apple-touch-icon-180x180.png" />
									</div>
									<div class="integration-body">
										<div class="integration-title"><h4>DocuSign <small ng-show="docusign_activated" class="text-success">(activated)</small></h4></div>
										<div class="integration-description"><p>Whether you’re approving a purchase, closing a sale, or signing an agreement, it’s easy with DocuSign—reliable and trusted worldwide for electronic signatures.</p></div>
										<?php if (empty($oToken)): ?>
										<div class="integration-options">
											<a href="/docusign/connect_account" class="btn btn-sm btn-success">Enable</a>
											<a href="https://go.docusign.com/o/trial/" target="_blank" class="btn btn-sm btn-text">Free Trial</a>
											<a href="https://docusign.com" target="_blank" class="btn btn-sm btn-text">View Website</a>
										</div>
										<?php else: ?>
										<div class="integration-options">
											<a href="/docusign/disconnect" class="btn btn-sm btn-text text-danger">Disconnect</a>
											<!-- <a href="../docusign-settings" class="btn btn-sm btn-text">Settings</a> -->
											<a href="https://docusign.com" target="_blank" class="btn btn-sm btn-text">View Website</a>
										</div>
										<?php endif; ?>
									</div>
								</div>
							</div>

							<?php if (!empty($oToken)): ?>
							<div>
								<div class="divider">
									<div class="divider-content">
										<div class="divider-title">
											<h6>DocuSign Settings</h6>
										</div>
										<div class="divider-separator">
											<hr/>
										</div>
									</div>
								</div>
								<div class="details">
									<div class="form-grid form-minimal">
										<table>
											<tr>
												<td class="form-label"><label>Account Name</label></td>
												<td class="form-response">
													<div class="form-control-static"><?php echud($oToken->account_name); ?></div>
												</td>
											</tr>
											<tr>
												<td class="form-label"><label>Email</label></td>
												<td class="form-response">
													<div class="form-control-static"><?php echud($oToken->email); ?></div>
												</td>
											</tr>
										</table>
									</div>
								</div>
							</div>
							<?php endif; ?>
						</div>
					</div>
