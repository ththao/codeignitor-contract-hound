<style>
  .title .title-content .title-actions {
      width: auto;
      float: right;
    }
  #contract-details .form-control-static {
      height: auto;
  }
</style>
<div class="layout-panel">
	<div class="layout-panel-body">
		<div class="layout-panel-main">
			<div class="layout-section">
				<div class="layout-panel-left">
					<?php if (!empty($bCurrentUserPendingApproval)): ?>
					<div class="alert alert-info">
						<div class="alert-actions">
							<div class="dropdown">
								<a href="#" class="btn btn-info btn-sm" data-toggle="dropdown">Approval <span class="caret"></span></a>
								<ul class="dropdown-menu dropdown-menu-right">
									<li class="dropdown-header">Approval Status</li>
									<li>
										<a href="/approvals/approve_step/<?php echo $iCurrentUserPendingApprovalId; ?>">
											<p class="help-block">
												<strong class="approval-status approval-approved">
													<span class="approval-icon"><span data-icon="approved"></span></span>
													<span class="approval-value">Approve</span>
												</strong>
												Sends a notification to the members of the next approval step to approve/reject.
											</p>
										</a>
									</li>
									<li class="divider"></li>
									<li>
										<a href="/approvals/reject_step/<?php echo $iCurrentUserPendingApprovalId; ?>">
											<p class="help-block">
												<strong class="approval-status approval-rejected">
													<span class="approval-icon"><span data-icon="rejected"></span></span>
													<span class="approval-value">Reject</span>
												</strong>
												Terminates the approval workflow and sends a notification to the contract owner.
											</p>
										</a>
									</li>
								</ul>
							</div>
						</div>
						<strong>Approval</strong> It's your turn to approve/reject this contract.
					</div>
					<?php endif; ?>

					<div class="divider">
						<div class="divider-content">
							<div class="divider-title">
								<h6>Contract Details</h6>
							</div>
							<div class="divider-separator">
								<hr/>
							</div>
							<?php if ($bCurrentUserCanEdit): ?>
							<div class="divider-actions">
								<a href="#" class="btn btn-default btn-sm" ng-hide="contract_editing" ng-click="contract_editing=true;">edit</a>
							</div>
							<?php endif; ?>
						</div>
					</div>

					<form id="contract-details" class="details" method="post" action="/contracts/edit/<?php echo $oContract->contract_id; /* ng-submit="contract_editing=false"*/?>">
						<div class="form-grid form-minimal">
							<table>
								<tr ng-show="contract_editing" class="ng-hide">
									<td class="form-label"><label>Name</label></td>
									<td colspan="3" class="form-response">
										<input name="name" ng-show="contract_editing" class="form-control ng-hide" type="text" placeholder="contract title..." ng-model="contract_name" id="contract_name" />
									</td>
								</tr>
								<tr>
									<td class="form-label"><label>Company</label></td>
									<td colspan="3" class="form-response">
										<input name="company" ng-show="contract_editing" class="form-control ng-hide" type="text" placeholder="company name..." ng-model="contract_company" />
										<div ng-show="!contract_editing" class="form-control-static" placeholder="none..." ng-bind="contract_company"></div>
									</td>
								</tr>
								<tr>
									<td class="form-label"><label>Start</label></td>
									<td class="form-response">
										<input name="start_date" class="form-control ng-hide" type="text" placeholder="" data-jquery="datepicker" ng-model="contract_start" ng-show="contract_editing" />
										<div class="form-control-static" ng-show="!contract_editing" placeholder="none..." ng-bind="contract_start"></div>
									</td>
									<td class="form-label"><label>End</label></td>
									<td class="form-response">
										<input name="end_date" class="form-control ng-hide" type="text" placeholder="" data-jquery="datepicker" ng-model="contract_end" ng-show="contract_editing" />
										<div class="form-control-static" ng-show="!contract_editing" placeholder="none..." ng-bind="contract_end"></div>
									</td>
								</tr>
								<tr>
									<td class="form-label"><label>Value</label></td>
									<td class="form-response">
										<input name="valued" class="form-control ng-hide" type="text" placeholder="10000..." ng-model="contract_value" ng-show="contract_editing" />
										<div class="form-control-static" ng-show="!contract_editing" placeholder="none..." ng-bind="contract_value"></div>
									</td>
									<td class="form-label"><label>Type *</label></td>
									<td class="form-response">
										<select id="contract_type" name="type" class="form-control ng-hide" ng-show="contract_editing" required>
											<option value="">choose type...</option>
											<option <?php if ($oContract->type): ?>selected <?php endif; ?>value="1"><?php tle('buy-side'); ?></option>
											<option <?php if (!$oContract->type): ?>selected <?php endif; ?>value="0"><?php tle('sell-side'); ?></option>
										</select>
										<div class="form-control-static" ng-show="!contract_editing" placeholder="none..."><?php echo ($oContract->type?tl('buy-side'):tl('sell-side')); ?></div>
									</td>
								</tr>
								<tr ng-show="!contract_editing">
									<td class="form-label"><label>File</label></td>
									<td colspan="3" class="form-response"><p class="form-control-static"><a target="_blank" href="/ctcssa/<?php echo $oContract->file_hash; ?>"><em><?php echud($oContract->file_name); ?></em></a></p></td>
								</tr>
								<?php foreach ($oCustomFields as $oCustomField):
									$mValue = $oCustomField->default_value;
									foreach ($oCustomFieldValueTexts as $oCustomFieldValueText) {
										if ($oCustomFieldValueText->custom_field_id == $oCustomField->custom_field_id) {
											$mValue = $oCustomFieldValueText->field_value;
										}
									} ?>
								<tr data-cfid="<?php echo $oCustomField->custom_field_id; ?>">
									<td class="form-label"><label><span><?php echo str_replace("\n",'<br/>',word_wrap(retud($oCustomField->label_text),35,'<br/>',true)); ?></span></label></td>
									<td class="form-response" colspan="3">
										<div ng-show="contract_editing">
											<?php if ($oCustomField->type==CustomFieldModel::TYPE_MULTILINE): ?>
											<textarea name="cf[<?php echo $oCustomField->custom_field_id; ?>]" class="form-control" placeholder="<?php echud($oCustomField->description); ?>" elastic=""><?php echud($mValue); ?></textarea>
											<?php else: ?>
											<input name="cf[<?php echo $oCustomField->custom_field_id; ?>]" class="form-control" type="text" placeholder="<?php echud($oCustomField->description); ?>" value="<?php echud($mValue); ?>"/>
											<?php endif; ?>
										</div>
										<div ng-show="!contract_editing" ng-class="{'form-control-static':true,'form-control-static-multiline':(field.type=='multiline')}" placeholder="none..."><?php echo wordwrap(retud($mValue),45,'<br/>',true); ?></div>
									</td>
								</tr>
								<?php endforeach; ?>
							</table>
						</div>
						<div ng-show="contract_editing" class="ng-hide">
							<input type="submit" class="btn btn-primary" value="Save Changes"/>
							<a class="btn btn-text" ng-click="contract_editing=false;">Cancel</a>
						</div>
					</form>

					<div class="divider">
						<div class="divider-content">
							<div class="divider-title">
								<h6>Users Who Have Access</h6>
								<small id="member-count">(<?php echo count($aTeamMembers); ?>)</small>
							</div>
							<div class="divider-separator">
								<hr/>
							</div>
							<div class="divider-actions">
								<?php if ($bCurrentUserCanEdit): ?>
								<a href="#" class="btn btn-default btn-sm" id="view-members-change">
									edit
								</a>
								<a class="btn btn-default btn-sm" href="#approval-edit-warning" data-toggle="modal">
									<span data-icon="add-small"></span>
								</a>
								<?php endif; ?>
							</div>
						</div>
					</div>

					<div class="members" id="members-view">
						<?php foreach ($aTeamMembers as $iTeamMemberId=>$oMember): ?>
						<a href="/users/profile/<?php echo $oMember->member_id; ?>" class="member" id="member-<?php echo $oMember->member_id; ?>">
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
										<h6>
											<?php $oMember->name?echud($oMember->name):echud($oMember->email); ?>
											<?php if ($oMember->status == '0'): ?><small class="text-info">Pending...</small><?php endif; ?>
											<?php if ($oMember->status == '3'): ?><small class="text-danger">Suspended</small><?php endif; ?>
										</h6>
									</div>
									<div class="member-meta">
										<span><?php echo $oMember->level; ?></span>
									</div>
								</div>
							</div>
						</a>
						<?php endforeach; ?>
					</div>

					<div class="members" id="members-change" style="display: none;">
						<div class="member member-editable">
							<?php foreach ($aTeamMembers as $iTeamMemberId=>$oMember):
								if ($oMember->member_id == $oContract->owner_id) { continue; }
							?>
							<div class="member-content dropdown" data-member-id="<?php echo $oMember->member_id; ?>">
								<div class="member-graphic"  data-toggle="dropdown" style="background-image: url(<?php
								if ($oMember->avatar): ?>/uas/<?php echo $oMember->avatar; ?><?php else: ?>/ui/img/avatars/default.png<?php endif;
								?>)">
									<div class="avatar avatar-medium" style="background-image: url(<?php
									if ($oMember->avatar): ?>/uas/<?php echo $oMember->avatar; ?><?php else: ?>/ui/img/avatars/default.png<?php endif;
									?>)">
										<img src="<?php
										if ($oMember->avatar): ?>/uas/<?php echo $oMember->avatar; ?><?php else: ?>/ui/img/avatars/default.png<?php endif;
										?>" />
									</div>
								</div>
								<div class="member-body" data-toggle="dropdown">
									<div class="member-name">
										<h6><?php $oMember->name?echud($oMember->name):echud($oMember->email); ?></h6>
									</div>
									<div class="member-meta">
										<span class="link"><span class="member-level"><?php echo $oMember->level; ?></span> <span class="caret"></span></span>
									</div>
								</div>
								<?php /*<a href="#" class="member-action member-action-remove">
									<span data-icon="close-small">Remove</span>
								</a>*/ ?>
								<ul class="dropdown-menu">
									<li><a href="#" >Editor</a></li>
									<li><a href="#" >View Only</a></li>
								</ul>
							</div>
							<?php endforeach; ?>
						</div>

						<div class="form-footer" id="member-change-controls">
							<a id="save-team-members" href="#" class="btn btn-primary">Save Changes</a>
							<a id="cancel-team_members" href="#" class="btn btn-text">Cancel</a>
						</div>
					</div>

					<!-- start approvals -->
					<?php if (!empty($aApprovalStepsSorted) || !empty($oSignatures)): ?>
					<div class="divider">
						<div class="divider-content">
							<div class="divider-title">
								<h6>Workflow</h6>
								<small>(<?php echo number_format(count($aApprovalStepsSorted)); ?> Step<?php if (count($aApprovalStepsSorted) > 1):
									?>s<?php endif; if (count($oSignatures)):
									 ?>, <?php echo number_format(count($oSignatures)); ?> Signature<?php if (count($oSignatures) > 1):?>s<?php
								 endif; endif; ?>)</small>
							</div>
							<div class="divider-separator">
								<hr/>
							</div>
							<div class="divider-actions">
								<?php if ($bCurrentUserCanEdit && $bCurrentSubHasApprovalAccess): ?>
								<a class="btn btn-default btn-sm" href="#approval-edit-warning" data-toggle="modal">
									edit
								</a>
								<?php endif; ?>
								<a class="btn btn-default btn-sm" ng-click="summarize_approvals=false" ng-show="summarize_approvals">
									show steps
								</a>
								<a class="btn btn-default btn-sm" ng-click="summarize_approvals=true" ng-show="!summarize_approvals">
									collapse
								</a>
							</div>
						</div>
					</div>


					<div ng-class="{'approval-tables':true,'approval-tables-brief':summarize_approvals}">
						<?php $bShowSignatures = true; foreach ($aApprovalStepsSorted as $iStepId=>$aStep): ?>
						<div class="approval-table<?php
							if ($aStep['status'] == ContractApprovalModel::STATUS_PENDING ||
								$aStep['status'] == ContractApprovalModel::STATUS_REJECTED):
								$bShowSignatures = false; ?> approval-table-current<?php endif; ?>">
							<div class="approval-table-bullet"></div>
							<div class="table-responsive ">
								<table class="table table-borderless table-justified approvals">
									<tbody>
										<?php foreach ($aStep['steps'] as $oStep): ?>
										<tr class="<?php switch ($oStep->status) {
												case ContractApprovalModel::STATUS_PENDING:
													echo 'approval info';
													break;
												case ContractApprovalModel::STATUS_REJECTED:
													echo 'approval danger';
													break;
												case ContractApprovalModel::STATUS_APPROVED:
													echo 'approval success';
													break;
												case ContractApprovalModel::STATUS_WAITING:
													break;
												case ContractApprovalModel::STATUS_SKIPPED:
												default:
													echo 'approval default';
											} ?>">
											<td class="cell-small"><a href="/users/profile/<?php echo $oStep->member_id; ?>" class="cell-link"><div class="avatar" style="background-image: url(<?php
													if ($oStep->avatar): ?>/uas/<?php echo $oStep->avatar; ?><?php else: ?>/ui/img/avatars/default.png<?php endif;
														?>)"><img src="<?php
													if ($oStep->avatar): ?>/uas/<?php echo $oStep->avatar; ?><?php else: ?>/ui/img/avatars/default.png<?php endif;
														?>" /></div></a></td>
											<td><a href="/users/profile/<?php echo $oStep->member_id; ?>" class="cell-link primary"><?php $oStep->last_name?echud($oStep->first_name.' '.$oStep->last_name):echud($oStep->email); ?></a></td>
											<td class="cell-small cell-right">
												<?php if ($oStep->status != ContractApprovalModel::STATUS_WAITING): ?>
												<div class="approval-status approval-<?php switch ($oStep->status) {
													case ContractApprovalModel::STATUS_PENDING:
														echo 'waiting';
														break;
													case ContractApprovalModel::STATUS_REJECTED:
														echo 'rejected';
														break;
													case ContractApprovalModel::STATUS_APPROVED:
														echo 'approved';
														break;
													case ContractApprovalModel::STATUS_SKIPPED:
													default:
														break;
												} ?>">
													<span class="approval-key">Approval:</span>
													<span class="approval-value"><?php echo lang($oStep->readable_status); ?></span>
													<span class="approval-icon"><span data-icon="<?php switch ($oStep->status) {
														case ContractApprovalModel::STATUS_PENDING:
															echo 'waiting';
															break;
														case ContractApprovalModel::STATUS_REJECTED:
															echo 'rejected';
															break;
														case ContractApprovalModel::STATUS_APPROVED:
															echo 'approved';
															break;
														case ContractApprovalModel::STATUS_SKIPPED:
														default:
															break;
													} ?>"></span></span>
												</div>
												<?php endif; ?>
											</td>
											<?php if ($oStep->status == ContractApprovalModel::STATUS_PENDING): ?>
											<td class="cell-small ">
												<?php if ($oStep->member_id == $iCurrentlyLoggedInMemberId): ?>
													<div class="dropdown">
														<a href="#" class="cell-link text-light" data-toggle="dropdown"><span class="caret"></span></a>
														<ul class="dropdown-menu dropdown-menu-right">
															<li>
																<a href="/approvals/approve_step/<?php echo $oStep->contract_approval_id; ?>">
																	<p class="help-block">
																		<strong class="approval-status approval-approved">
																			<span class="approval-icon"><span data-icon="approved"></span></span>
																			<span class="approval-value">Approve</span>
																		</strong>
																		Sends a notification to the members of the next approval step to approve/reject.
																	</p>
																</a>
															</li>
															<li class="divider"></li>
															<li>
																<a href="/approvals/reject_step/<?php echo $oStep->contract_approval_id; ?>">
																	<p class="help-block">
																		<strong class="approval-status approval-rejected">
																			<span class="approval-icon"><span data-icon="rejected"></span></span>
																			<span class="approval-value">Reject</span>
																		</strong>
																		Terminates the approval workflow and sends a notification to the contract owner.
																	</p>
																</a>
															</li>
														</ul>
													</div>
												<?php else: ?>
												<div class="dropdown">
													<a href="#" class="cell-link text-light" data-toggle="dropdown"><span class="caret"></span></a>
													<ul class="dropdown-menu dropdown-menu-right">
														<li><a href="/approvals/send_approval_reminder/<?php echo $oStep->contract_approval_id; ?>">Send Reminder</a></li>
													</ul>
												</div>
												<?php endif; ?>
											</td>
											<?php else: ?>
											<td class="cell-small">

											</td>
											<?php endif; ?>
										</tr>
										<?php endforeach; ?>
									</tbody>
								</table>
							</div>
						</div>
						<?php endforeach; ?>
						<?php if (count($oSignatures)): ?>
						<div class="approval-table approval-table-signature<?php if ($bShowSignatures): ?> approval-table-current<?php endif; ?>">
							<div class="approval-table-bullet">
								<span data-icon="sign"></span>
							</div>
							<div class="table-responsive ">
								<table class="table table-borderless table-justified approvals">
									<tbody>
										<?php foreach ($oSignatures as $oSignature):
											if (empty($aSignatureMembers[$oSignature->member_id])) { continue; }
											$oSignatureMember = $aSignatureMembers[$oSignature->member_id]; ?>
										<tr class="<?php switch ($oSignature->status) {
												case ContractSignatureModel::STATUS_PENDING:
													echo 'approval info';
													break;
												case ContractSignatureModel::STATUS_SIGNED:
													echo 'approval success';
													break;
												case ContractSignatureModel::STATUS_REJECTED:
													echo 'approval danger';
													break;
												case ContractSignatureModel::STATUS_WAITING:
													break;
												default:
													echo 'approval default';
											} ?>">
											<td class="cell-small"><a href="/users/profile/<?php echo $oSignatureMember->member_id; ?>" class="cell-link"><div class="avatar" style="background-image: url(<?php
													if ($oSignatureMember->avatar): ?>/uas/<?php echo $oSignatureMember->avatar; ?><?php else: ?>/ui/img/avatars/default.png<?php endif;
														?>)"><img src="<?php
													if ($oSignatureMember->avatar): ?>/uas/<?php echo $oSignatureMember->avatar; ?><?php else: ?>/ui/img/avatars/default.png<?php endif;
														?>" /></div></a></td>
											<td><a href="/users/profile/<?php echo $oSignatureMember->member_id; ?>" class="cell-link primary"><?php $oSignatureMember->last_name?echud($oSignatureMember->first_name.' '.$oSignatureMember->last_name):echud($oSignatureMember->email); ?></a></td>
											<td class="cell-small cell-right">
												<div class="approval-status approval-na">
													<span class="approval-key"><?php
														switch ($oSignature->status) {
															case ContractSignatureModel::STATUS_WAITING:
															case ContractSignatureModel::STATUS_WAITING:
																echo 'Signature Required';
																break;
															case ContractSignatureModel::STATUS_SIGNED:
																echo 'Signed';
																break;
															case ContractSignatureModel::STATUS_REJECTED:
																echo 'Declined';
																break;
														}
													?></span>
												</div>
											</td>
											<td class="cell-meta cell-small ">
												<?php /*<div class="dropdown">
													<a href="#" class="cell-link text-light" data-toggle="dropdown"><span class="caret"></span></a>
													<ul class="dropdown-menu dropdown-menu-right">
														<li><a href="#">Sign with Docusign</a></li>
													</ul>
												</div> */ ?>
											</td>
										</tr>
										<?php endforeach; ?>
									</tbody>
								</table>
							</div>
						</div>
						<?php endif; ?>
					</div>
					<?php endif; ?>
					<!-- end approvals -->

					<div class="divider divider">
						<div class="divider-content">
							<div class="divider-title">
								<h6>Reminders</h6>
								<small>(<?php echo number_format($oReminders->count); ?>)</small>
							</div>
							<div class="divider-separator">
								<hr/>
							</div>
							<div class="divider-actions">
								<?php if ($bCurrentUserCanEdit): ?>
								<a href="#" class="btn btn-default btn-sm" ng-hide="contract_reminders_editing" ng-click="contract_reminders_editing=true">
									edit
								</a>
								<a href="/reminders/add/<?php echo $oContract->contract_id; ?>" class="btn btn-default btn-sm">
									<span data-icon="add-small"></span>
								</a>
								<?php endif; ?>
							</div>
						</div>
					</div>

					<div class="reminders" ng-hide="contract_reminders_editing">
						<?php $iCurrentTime = time(); $i5Days = strtotime('+5 days'); $i21Days = strtotime('+21 days'); foreach ($oReminders as $oReminder):
							$iAlertDate = strtotime($oReminder->alert_date); ?>
						<div class="reminder reminder-<?php if ($iAlertDate < $i5Days): ?>danger<?php elseif ($iAlertDate < $i21Days): ?>warning<?php else: ?>default<?php endif; ?>">
							<div class="reminder-content">
								<a href="/contracts/view/<?php echo $oReminder->contract_id; ?>" class="reminder-time"><?php echo convertto_local_datetime($oReminder->alert_date,$time_zone,'%x',true); ?></a>
								<a href="/contracts/view/<?php echo $oReminder->contract_id; ?>" class="reminder-body">
									<h6><?php echud($oReminder->name); ?></h6>
									<p><?php echud($oReminder->message); ?></p>
								</a>
								<a href="/reminders/dismiss/<?php echo $oReminder->reminder_id; ?>" class="reminder-actions"><span data-icon="close-small">Dismiss</span></a>
							</div>
						</div>
						<?php endforeach; ?>
					</div>

					<div class="reminders" ng-show="contract_reminders_editing">
						<?php foreach ($oReminders as $oReminder): ?>
						<div class="reminder reminder-default">
							<div class="reminder-content">
								<div class="reminder-time"><?php //echo date('n/j',strtotime($oReminder->alert_date)); ?><?php echo convertto_local_datetime($oReminder->alert_date,$time_zone); ?></div>
								<div class="reminder-body">
									<p><?php echud($oReminder->message); ?></p>
								</div>
								<div class="reminder-actions">
									<a href="/reminders/edit/<?php echo $oReminder->reminder_id; ?>"><span data-icon="edit">Edit</span></a>
									<a href="/reminders/delete/<?php echo $oReminder->reminder_id; ?>"><span data-icon="close-small">Remove</span></a>
								</div>
							</div>
						</div>
						<?php endforeach; ?>

						<div class="form-footer">
							<a href="#" class="btn btn-primary" ng-click="contract_reminders_editing=false">Done Editing</a>
						</div>
					</div>
					
					<div class="divider divider">
						<div class="divider-content">
							<div class="divider-title">
								<h6>Supporting Documents</h6>
								<small>(<?php echo $oSupportDocs->count ? $oSupportDocs->count : 0; ?>)</small>
							</div>
							<div class="divider-separator">
								<hr/>
							</div>
							<div class="divider-actions">
								<?php if ($oSupportDocs->count < $this->config->item('max_supporting_docs_per_contract')): ?>
                					<a href="/contracts/add_support_doc/<?php echo $oContract->contract_id; ?>" class="btn btn-default btn-sm pull-center"><span data-icon="add-small"></span></a>
                				<?php endif; ?>
							</div>
						</div>
					</div>
					
					<div class="supporting_documents" ng-hide="contract_supporting_documents_editing">
						<?php foreach ($oSupportDocs as $oSupportDoc): ?>
						<div class="supporting_document form-group">
							<div class="supporting_document_content row">
								<a class="col-lg-7" href="/ctcssa/<?php echo $oSupportDoc->file_hash; ?>" target="_blank"><strong><?php echud($oSupportDoc->file_name); ?></strong></a>
								<span class="col-lg-3"><?php //echo date('n/j/Y',strtotime($oSupportDoc->create_date)); ?><?php echo convertto_local_datetime($oSupportDoc->create_date,$time_zone,'%x'); ?></span>
								<a class="btn btn-danger btn-sm col-lg-2" href="/contracts/delete_contract_support_doc/<?php echo $oSupportDoc->contract_support_doc_id; ?>">Delete</a>
							</div>
						</div>
						<?php endforeach; ?>
					</div>
				</div>
			</div>
		</div>
		<div class="layout-panel-aside">
			<div class="layout-section">
				<div class="layout-panel-right">

					<div class="divider divider-simple" mobile>
						<div class="divider-content">
							<div class="divider-title">
								<h6>Conversation</h6>
								<small>&nbsp;</small>
							</div>
							<div class="divider-separator">
								<hr/>
							</div>
						</div>
					</div>

					<div class="conversation">
						<div class="conversation-content">
							<div class="conversation-body" data-jquery="scroll-bottom">
								<div class="messages">
									<?php foreach ($aLogs as $oLog):
										$oLogOwner = null;
										if (!empty($aLogOwners[$oLog->member_id])) {
											$oLogOwner = $aLogOwners[$oLog->member_id];
										}
										if ($oLog->type == ContractLogModel::TYPE_UPDATE):?>
									<div class="message">
										<div class="message-content">
											<div class="message-graphic">
												<div class="avatar avatar-medium" style="background-image: url(<?php
													if (!empty($oLogOwner) && $oLogOwner->avatar): ?>/uas/<?php echo $oLogOwner->avatar; ?><?php else: ?>/ui/img/avatars/default.png<?php endif;
														?>)">
													<img src="<?php
													if (!empty($oLogOwner) && $oLogOwner->avatar): ?>/uas/<?php echo $oLogOwner->avatar; ?><?php else: ?>/ui/img/avatars/default.png<?php endif;
														?>" />
												</div>
											</div>
											<div class="message-body">
												<div class="message-header">
													<h6><?php if (!empty($oLogOwner) && $oLogOwner->name){ echud($oLogOwner->name); } elseif (!empty($oLogOwner)) { echud($oLogOwner->email); } else { echo 'Missing'; }
														?> <small><?php //echo date('n/j g:ia',strtotime($oLog->create_date)); ?><?php echo convertto_local_datetime($oLog->create_date,$time_zone,'%x %X'); ?></small></h6>
												</div>
												<div class="message-activity">
													<p><?php echud($oLog->message); ?> <a href="/contracts/view/<?php echo $oContract->contract_id; ?>"><?php echud($oContract->name); ?></a></p>
												</div>
											</div>
										</div>
									</div>
									<?php elseif ($oLog->type == ContractLogModel::TYPE_DOCUSIGN_GENERIC): ?>
									<div class="message">
										<div class="message-content">
											<div class="message-graphic">
												<div class="avatar avatar-medium avatar-icon info">
													<span data-icon="sign"></span>
												</div>
											</div>
											<div class="message-body">
												<div class="message-header">
													<h6><?php echud($oLog->message); ?> <small><?php //echo date('n/j g:ia',strtotime($oLog->create_date)); ?><?php echo convertto_local_datetime($oLog->create_date,$time_zone,'%x %X'); ?></small></h6>
												</div>
												<div class="message-activity">
													<p>signed <a href="/contracts/view/<?php echo $oContract->contract_id; ?>"><?php echud($oContract->name); ?></a>.</p>
												</div>
											</div>
										</div>
									</div>
									<?php elseif ($oLog->type == ContractLogModel::TYPE_SIGNER_APPROVED): ?>
									<div class="message">
										<div class="message-content">
											<div class="message-graphic">
												<div class="avatar avatar-medium avatar-icon info">
													<span data-icon="sign"></span>
												</div>
											</div>
											<div class="message-body">
												<div class="message-header">
													<h6><?php if (!empty($oLogOwner) && $oLogOwner->name){ echud($oLogOwner->name); } elseif (!empty($oLogOwner)) { echud($oLogOwner->email); } else { echo 'Missing'; }
														?> <small><?php //echo date('n/j g:ia',strtotime($oLog->create_date)); ?><?php echo convertto_local_datetime($oLog->create_date,$time_zone,'%x %X'); ?></small></h6>
												</div>
												<div class="message-activity">
													<p>signed <a href="/contracts/view/<?php echo $oContract->contract_id; ?>"><?php echud($oContract->name); ?></a>.</p>
												</div>
											</div>
										</div>
									</div>
									<?php elseif ($oLog->type == ContractLogModel::TYPE_SIGNER_REJECTED): ?>
									<div class="message">
										<div class="message-content">
											<div class="message-graphic">
												<div class="avatar avatar-medium avatar-icon info">
													<span data-icon="sign"></span>
												</div>
											</div>
											<div class="message-body">
												<div class="message-header">
													<h6><?php if (!empty($oLogOwner) && $oLogOwner->name){ echud($oLogOwner->name); } elseif (!empty($oLogOwner)) { echud($oLogOwner->email); } else { echo 'Missing'; }
														?> <small><?php //echo date('n/j g:ia',strtotime($oLog->create_date)); ?><?php echo convertto_local_datetime($oLog->create_date,$time_zone,'%x %X'); ?></small></h6>
												</div>
												<div class="message-activity">
													<p>rejected <a href="/contracts/view/<?php echo $oContract->contract_id; ?>"><?php echud($oContract->name); ?></a>.</p>
												</div>
											</div>
										</div>
									</div>
									<?php elseif ($oLog->type == ContractLogModel::TYPE_FULLY_APPROVED): ?>
									<div class="message success linked">
										<div class="message-content">
											<div class="message-body ">
												<div class="message-header">
													<h6>Contract Hound <small><?php //echo date('n/j g:ia',strtotime($oLog->create_date)); ?><?php echo convertto_local_datetime($oLog->create_date,$time_zone,'%x %X'); ?></small></h6>
												</div>
												<div class="message-activity">
													<p><a href="/contracts/view/<?php echo $oLog->contract_id; ?>"><?php echud($oLog->name); ?></a> is now fully approved.</p>
												</div>
											</div>
										</div>
										<a class="message-link" href="/contracts/view/<?php echo $oLog->contract_id; ?>">Entire Message Link</a>
									</div>
									<?php elseif ($oLog->type == ContractLogModel::TYPE_APPROVED): ?>
									<div class="message ">
										<div class="message-content">
											<div class="message-graphic">
												<div class="avatar avatar-medium avatar-icon success">
													<span data-icon="approved"></span>
												</div>
											</div>
											<div class="message-body">
												<div class="message-header">
													<h6><?php if (!empty($oLogOwner) && $oLogOwner->name){ echud($oLogOwner->name); } elseif (!empty($oLogOwner)) { echud($oLogOwner->email); } else { echo 'Missing'; }
														?> <small><?php //echo date('n/j g:ia',strtotime($oLog->create_date)); ?><?php echo convertto_local_datetime($oLog->create_date,$time_zone,'%x %X'); ?></small></h6>
												</div>
												<div class="message-activity">
													<p>approved <a href="/contracts/view/<?php echo $oContract->contract_id; ?>"><?php echud($oContract->name); ?></a>.</p>
												</div>
											</div>
										</div>
									</div>
									<?php elseif ($oLog->type == ContractLogModel::TYPE_REJECTED): ?>
									<div class="message ">
										<div class="message-content">
											<div class="message-graphic">
												<div class="avatar avatar-medium avatar-icon danger">
													<span data-icon="rejected"></span>
												</div>
											</div>
											<div class="message-body">
												<div class="message-header">
													<h6><?php if (!empty($oLogOwner) && $oLogOwner->name){ echud($oLogOwner->name); } elseif (!empty($oLogOwner)) { echud($oLogOwner->email); } else { echo 'Missing'; }
														?> <small><?php //echo date('n/j g:ia',strtotime($oLog->create_date)); ?><?php echo convertto_local_datetime($oLog->create_date,$time_zone,'%x %X'); ?></small></h6>
												</div>
												<div class="message-activity">
													<p>rejected <a href="/contracts/view/<?php echo $oContract->contract_id; ?>"><?php echud($oContract->name); ?></a>.</p>
												</div>
											</div>
										</div>
									</div>
									<?php elseif ($oLog->type == ContractLogModel::TYPE_NOTE): ?>
									<div class="message">
										<div class="message-content">
											<div class="message-graphic">
												<div class="avatar avatar-medium" style="background-image: url(<?php
													if (!empty($oLogOwner) && $oLogOwner->avatar): ?>/uas/<?php echo $oLogOwner->avatar; ?><?php else: ?>/ui/img/avatars/default.png<?php endif;
														?>)">
													<img src="<?php
													if (!empty($oLogOwner) && $oLogOwner->avatar): ?>/uas/<?php echo $oLogOwner->avatar; ?><?php else: ?>/ui/img/avatars/default.png<?php endif;
														?>" />
												</div>
											</div>
											<div class="message-body">
												<div class="message-header">
													<h6><?php if (!empty($oLogOwner) && $oLogOwner->name){ echud($oLogOwner->name); } elseif (!empty($oLogOwner)) { echud($oLogOwner->email); } else { echo 'Missing'; }
													?> <small><?php //echo date('n/j g:ia',strtotime($oLog->create_date)); ?><?php echo convertto_local_datetime($oLog->create_date,$time_zone,'%x %X'); ?></small></h6>
												</div>
												<div class="message-comment">
													<blockquote><?php echud($oLog->message); ?></blockquote>
												</div>
											</div>
										</div>
									</div>
									<?php else: ?>
									<div class="message danger">
										<div class="message-content">
											<div class="message-body ">
												<div class="message-header">
													<h6>Contract Hound <small><?php //echo date('n/j g:ia',strtotime($oLog->create_date)); ?><?php echo convertto_local_datetime($oLog->create_date,$time_zone,'%x %X'); ?>
														</small></h6>
												</div>
												<div class="message-comment">
													<blockquote><?php echud($oLog->message); ?></blockquote>
												</div>
											</div>
										</div>
									</div>
									<?php endif; endforeach; ?>
									</div>
							</div>
							<div class="conversation-footer">
								<form method="post" action="/contracts/add_log/<?php echo $oContract->contract_id; ?>">
									<div ng-class="{'input-group input-group-lg': conversation_message}">
										<input type="text" name="message" class="form-control input-lg" placeholder="Add message and hit enter..." ng-model="conversation_message" />
										<div class="input-group-btn" ng-if="conversation_message">
											<button type="submit" class="btn btn-info btn-lg">
												<span data-icon="arrow-right">Send</span>
											</button>
										</div>
									</div>
									<input type="submit" class="hidden" value="Send"/>
								</form>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="layout-panel-header">
		<div class="title">
			<div class="title-content">
				<div class="title-name">
					<h4>
						<span data-icon="contract"></span>
						<?php echud($oContract->name); ?>
						<small>Contract</small>
					</h4>
				</div>
				<div class="title-actions">
					<div class="btn-group">
						<div class="dropdown">
							<a href="#" class="btn btn-default" data-toggle="dropdown">
								<span>Actions</span>
								<i class="caret"></i>
							</a>
							<ul class="dropdown-menu dropdown-menu-right dropdown-menu-large">
								<li><a href="/ctcssa/<?php echo $oContract->file_hash; ?>" target="_blank"><strong>Download</strong><p class="help-block">Download the latest version.</p></a></li>
								<li><a href="/contracts/upload_file_version/<?php echo $oContract->contract_id; ?>"><strong>Upload new version</strong><p class="help-block">Upload a new version of this contract.</p></a></li>
								<?php /*<li><a href="#"><strong>Mute notifications</strong><p class="help-block">You won't receive notifications from this contract. Unmute anytime.</p></a></li>*/ ?>
								<li><a href="/contracts/change_board/<?php echo $oContract->contract_id; ?>"><strong>Move..</strong><p class="help-block"><?php echo lang('Move this contract to a different board.'); ?></p></a></li>
								<li class="divider"></li>
								<li><a href="#" data-toggle="modal" data-target="#versions">Version History</a></li>
								<li class="divider"></li>
								<?php /*<li><a href="#"><strong>Archive</strong><p class="help-block">You can restore an archived contract whenever you want.</p></a></li>*/ ?>
								<li><a href="/contracts/delete/<?php echo $oContract->contract_id; ?>"><strong>Delete</strong><p class="help-block">Deleting a contract is permanent and cannot be undone.</p></a></li>
							</ul>
						</div>
					</div>
					<?php /*<div class="btn-group" full="">
						<a href="#" class="btn btn-warning">
							<span>On</span>
							<span data-icon="notification"></span>
						</a>
					</div>*/ ?>
					<div class="btn-group" full="">
						<?php $file_parts = pathinfo($oContract->file_name); ?>
						<?php if (isset($file_parts['extension']) && 'pdf' == $file_parts['extension']) { ?>
    						<a target="_blank" href="/ctcssa/view/<?php echo $oContract->file_hash; ?>" class="btn btn-primary">View</a>
    						<a target="_blank" href="/ctcssa/download/<?php echo $oContract->file_hash; ?>" class="btn btn-primary" style="border-left: 1px solid #FFF;">Download</a>
						<?php } else { ?>
							<a target="_blank" href="/ctcssa/download/<?php echo $oContract->file_hash; ?>" class="btn btn-primary">
    							<span>View</span>
    							<span data-icon="right-small"></span>
    						</a>
						<?php } ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="modal fade" id="approval-edit-warning">
	<div class="modal-container">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span data-icon="close-small">Close</span></button>
					<h3 class="modal-title">Warning</h3>
				</div>
				<div class="modal-body">
					<p>Updating Team Members or Editing the set Approval Process will result in a restart of the Approval Process</p>
				</div>
				<div class="modal-footer">
					<a href="#" class="btn btn-lg btn-text" data-dismiss="modal">Cancel</a>
					<a href="/contracts/update_team/<?php echo $oContract->contract_id; ?>" class="btn btn-lg btn-primary">Continue</a>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="versions">
	<div class="modal-container">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span data-icon="close-small">Close</span></button>
					<h3 class="modal-title">Version History</h3>
					<p>Review previous contract attachments for <a href="#"><?php echud($oContract->name); ?></a></p>

				</div>
				<div class="modal-body">
					<div class="table-responsive table-alignment">
						<table class="table table-borderless table-justified">
							<thead>
								<tr>
									<th>File Name</th>
									<th>Date Added</th>
								</tr>
							</thead>
							<tbody>
							<?php foreach ($oRevisions as $oRevision): ?>
								<tr>
									<td><a href="/ctcssa/<?php echo $oRevision->file_hash; ?>" target="_blank" class="cell-link primary"><?php echo $oRevision->file_name; ?></a></th>
									<td><span data-toggle="tooltip" title="<?php //echo date('m/d/Y \a\t g:ia T', strtotime($oRevision->revision_date)); ?><?php echo convertto_local_datetime($oRevision->revision_date,$time_zone,'%x at %I:%M%p %z'); ?>"><?php echo time_ago(convertto_local_datetime($oRevision->revision_date,$time_zone,'%x %X')); ?></span></td>
								</tr>
							<?php endforeach; ?>
							</tbody>
						</table>
					</div>
				</div>
				<div class="modal-footer modal-footer-left">
					<a href="#" class="btn btn-lg btn-info" data-dismiss="modal">Upload new version</a>
				</div>
			</div>
		</div>
	</div>
</div>

<script>
	$('.form-control.tags').tokenfield({});
	$(document).ready(function(){
		var scope = angular.element($('body')[0]).scope();
		scope.$apply(function() {
			scope.contract_name = '<?php echud($oContract->name); ?>';
			scope.contract_company = '<?php echud($oContract->company); ?>';
			scope.contract_start = '<?php if ($oContract->start_date) { echo convertto_local_datetime($oContract->start_date,$time_zone,'%x');//echo date('n/j/Y',strtotime($oContract->start_date)); } ?>';
			scope.contract_end = '<?php if ($oContract->end_date) {echo convertto_local_datetime($oContract->end_date,$time_zone,'%x');// echo date('n/j/Y',strtotime($oContract->end_date)); } ?>';
			scope.contract_value = '<?php if ($oContract->valued) { echo $sCurrency.number_format($oContract->valued,2); } ?>';

			<?php if ($iCurrentlyLoggedInMemberId == 1): ?>
			scope.has_approvals = true;
			scope.require_approvals = true;
			scope.summarize_approvals = true;
			scope.workflow_1 = true;
			scope.workflow_2 = true;
			scope.workflow_3 = true;
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
			<?php endif; ?>
		});
	});

	//$('#contract-details button[type=submit]').click(function() {

	//});

	$('#view-members-change').click(function() {
		$(this).hide();
		$('#members-view').hide();
		$('#members-change').show();
	});

	$(document).on('click','.dropdown-menu a',function(){
		$(this).parent().parent().parent().find('.member-level').html($(this).text());
	});

	$('#save-team-members').click(function() {
		// ajax adjustments
		userUpdates = [];
		$('.member-editable .member-content').each(function() {
			memberId = $(this).data('member-id');
			memberLevel = $('.member-level',this).text().replace(' <span class="caret"></span>','');
			if ($(this).is(':hidden')) {
				memberLevel = 'Removed';
			}
			userUpdates.push(memberId+'//'+memberLevel);
		});

		$.ajax({
			url: '/contracts/ajax_update_users/<?php echo $oContract->contract_id; ?>',
			type: "POST",
			data: {
				user_updates: userUpdates
			},
			dataType: "json",
			async: false,
			success: function (data) {
				console.log('success');
				if (data.success == 1) {
					$('#members-view a.deleted, .member-editable .member-content:hidden').remove();

					$('.member-editable .member-content').each(function () {
						memberId = $(this).data('member-id');
						$('a#member-' + memberId + ' .member-meta span').text($('.member-level', this).html().replace(' <span class="caret"></span>', ''));
					});

					$('#member-count').text('('+$('#members-view a').length+')');
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

		$('#members-change').hide();
		$('#view-members-change').show();
		$('#members-view').show();
		return false;
	});

	$('#cancel-team_members').click(function() {
		$('.member-editable .member-content').each(function() {
			memberId = $(this).data('member-id');
			memberLevel = $('a#member-'+memberId+' .member-meta span').text();
			$('.member-level',this).html(memberLevel+' <span class="caret"></span>');
		});

		$('.member-editable .member-content:hidden').show();
		$('#members-view a.deleted').removeClass('deleted');

		$('#members-change').hide();
		$('#view-members-change').show();
		$('#members-view').show();
		return true;
	});

	$('.member-action-remove').click(function() {
		memberContent = $(this).parent();
		memberId = memberContent.data('member-id');
		$('a#member-'+memberId).addClass('deleted');
		memberContent.hide();
		return false;
	});
	jQuery.validator.addMethod(
		"money",
		function(value, element) {
			var isValidMoney = /^[\d $.,]+$/.test(value);
			return this.optional(element) || isValidMoney;
		},
		"Please specify valid amount"
	);
	$.validator.addMethod("anyDate",
		function(value, element) {
			var dateformat1 = value.match(/^(0?[1-9]|[12][0-9]|3[0-1])[/., -](0?[1-9]|1[0-2])[/., -](((19|20)?\d{2})|\d{2})$/);
			var dateformat2 = value.match(/^(0?[1-9]|1[0-2])[/., -](0?[1-9]|[12][0-9]|3[0-1])[/., -]((19|20)?\d{2}|\d{2})$/);
			var dateformat3 = value.match(/^((19|20)?\d{2}|\d{2})[/., -](0?[1-9]|1[0-2])[/., -](0?[1-9]|[12][0-9]|3[0-1])$/);
			return this.optional(element) || dateformat1 || dateformat2 || dateformat3;
		},
		"Please enter a date in the correct format!"
	);
	$("#contract-details-edit").submit(function(e) {
		e.preventDefault();
	}).validate({
		rules:{
			company:{
				maxlength:200,
			},
			valued:{
				maxlength:20,
				money:true
			},
			type:{
				required:true,
			},
			start_date:{
				anyDate:true,
			},
			end_date:{
				anyDate:true,
			}
		},
		submitHandler: function(form) {
			if ($(form).valid()) {
				form.submit();
			}
		}
	});
</script>
<style>
	#contract_type { color: #333; font-style: normal; }
	.member.member-editable .member-content { margin-bottom: 10px; }
</style>
