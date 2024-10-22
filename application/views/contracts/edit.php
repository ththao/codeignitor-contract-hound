<div class="layout-panel">
	<div class="layout-panel-body">
		<div class="layout-panel-main">
			<div class="layout-section">
				<div class="layout-panel-left">
					<div class="divider divider-simple">
						<div class="divider-content">
							<div class="divider-title">
								<h6>Contract Details</h6>
							</div>
							<div class="divider-separator">
								<hr/>
							</div>
						</div>
					</div>

					<form id="contract-details" class="details" method="post" action="/contracts/edit/<?php echo $oContract->contract_id;?>">
						<div class="form-grid form-minimal">
							<table>
								<tr>
									<td class="form-label"><label>Name</label></td>
									<td colspan="3" class="form-response">
										<input name="name" class="form-control" type="text" placeholder="contract title..." value="<?php echud($oContract->name); ?>" />
									</td>
								</tr>
								<tr>
									<td class="form-label"><label>Company</label></td>
									<td colspan="3" class="form-response">
										<input name="company" class="form-control" type="text" placeholder="company name..." value="<?php echud($oContract->company); ?>" />
									</td>
								</tr>
								<tr>
									<td class="form-label"><label>Start</label></td>
									<td class="form-response">
										<input name="start_date" class="form-control" type="text" placeholder="" data-jquery="datepicker" value="<?php
											if ($oContract->start_date) { echo convertto_local_datetime($oContract->start_date,$time_zone,'%x'); /*echo date('n/j/Y',strtotime($oContract->start_date)); } else { echo '';*/ } ?>" />
									</td>
									<td class="form-label"><label>End</label></td>
									<td class="form-response">
										<input name="end_date" class="form-control" type="text" placeholder="" data-jquery="datepicker" value="<?php
											if ($oContract->end_date) { echo convertto_local_datetime($oContract->end_date,$time_zone,'%x'); /*echo date('n/j/Y',strtotime($oContract->end_date)); } else { echo '';*/ } ?>" />
									</td>
								</tr>
								<tr>
									<td class="form-label"><label>Value</label></td>
									<td class="form-response">
										<input name="valued" class="form-control" type="text" placeholder="10000..." value="<?php
										if ($oContract->valued === null) { echo ''; } else { echo $sCurrency.number_format($oContract->valued); } ?>" />
									</td>
									<td class="form-label"><label>Type *</label></td>
									<td class="form-response">
										<select id="contract_type" name="type" class="form-control" required>
											<option value="">choose type...</option>
											<option <?php if ($oContract->type): ?>selected <?php endif; ?>value="1"><?php tle('buy-side'); ?></option>
											<option <?php if (!$oContract->type): ?>selected <?php endif; ?>value="0"><?php tle('sell-side'); ?></option>
										</select>
									</td>
								</tr>
								<?php foreach ($oCustomFields as $oCustomField):
									$mValue = $oCustomField->default_value;
									foreach ($oCustomFieldValueTexts as $oCustomFieldValueText) {
										if ($oCustomFieldValueText->custom_field_id == $oCustomField->custom_field_id) {
											$mValue = $oCustomFieldValueText->field_value;
										}
									} ?>
								<tr data-cfid="<?php echo $oCustomField->custom_field_id; ?>">
									<td class="form-label"><label><?php echud($oCustomField->label_text); ?><?php echo $oCustomField->required ? ' *' : ''; ?></label></td>
									<td class="form-response" colspan="3">
										<?php if ($oCustomField->type==CustomFieldModel::TYPE_MULTILINE): ?>
										<textarea name="cf[<?php echo $oCustomField->custom_field_id; ?>]" class="form-control" placeholder="<?php echud($oCustomField->description); ?>" elastic=""><?php echud($mValue); ?></textarea>
										<?php else: ?>
										<input name="cf[<?php echo $oCustomField->custom_field_id; ?>]" class="form-control" type="text" placeholder="<?php echud($oCustomField->description); ?>" value="<?php echud($mValue); ?>"/>
										<?php endif; ?>
									</td>
								</tr>
								<?php endforeach; ?>
							</table>
						</div>
						<div>
							<button type="submit" class="btn btn-primary">Save Changes</button>
							<a href="/contracts/view/<?php echo $oContract->contract_id; ?>" class="btn btn-text" >Cancel</a>
						</div>
					</form>

					<div class="divider divider-simple">
						<div class="divider-content">
							<div class="divider-title">
								<h6>Users Who Have Access</h6>
								<small id="member-count">(<?php echo count($aTeamMembers); ?>)</small>
							</div>
							<div class="divider-separator">
								<hr/>
							</div>
							<div class="divider-actions">
								<a href="#" class="btn btn-default btn-sm" id="view-members-change">
									edit
								</a>
								<a href="/contracts/update_team/<?php echo $oContract->contract_id; ?>" class="btn btn-default btn-sm">
									<span data-icon="add-small"></span>
								</a>
							</div>
						</div>
					</div>

					<div class="members" id="members-view">
						<?php foreach ($aTeamMembers as $iTeamMemberId=>$oMember): ?>
						<a href="/users/profile/<?php echo $oMember->member_id; ?>" class="member" id="member-<?php echo $oMember->member_id; ?>">
							<div class="member-content">
								<div class="member-graphic">
									<div class="avatar avatar-medium" style="background-image: url(<?php
									if ($oMember->avatar): ?>/uas/<?php echo $oMember->avatar; ?><?php else: ?>/ui/img/samples/avatar1.jpg<?php endif;
									?>)">
										<img src="<?php
										if ($oMember->avatar): ?>/uas/<?php echo $oMember->avatar; ?><?php else: ?>/ui/img/samples/avatar1.jpg<?php endif;
										?>" />
									</div>
								</div>
								<div class="member-body">
									<div class="member-name">
										<h6><?php $oMember->name?echud($oMember->name):echud($oMember->email); ?></h6>
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
							<div class="member-content" data-member-id="<?php echo $oMember->member_id; ?>">
								<div class="member-graphic">
									<div class="avatar avatar-medium" style="background-image: url(<?php
									if ($oMember->avatar): ?>/uas/<?php echo $oMember->avatar; ?><?php else: ?>/ui/img/samples/avatar1.jpg<?php endif;
									?>)">
										<img src="<?php
										if ($oMember->avatar): ?>/uas/<?php echo $oMember->avatar; ?><?php else: ?>/ui/img/samples/avatar1.jpg<?php endif;
										?>" />
									</div>
								</div>
								<div class="member-body">
									<div class="member-name">
										<h6><?php $oMember->name?echud($oMember->name):echud($oMember->email); ?></h6>
									</div>
									<div class="member-meta">
										<div class="dropdown">
											<a class="member-level" href="#" data-toggle="dropdown"><?php echo $oMember->level; ?> <span class="caret"></span></a>
											<ul class="dropdown-menu">
												<li><a href="#">Editor</a></li>
												<li><a href="#">View Only</a></li>
											</ul>
										</div>
									</div>
								</div>
								<div class="member-actions">
									<a href="#" class="member-action-remove">
										<span data-icon="close-small">Remove</span>
									</a>
								</div>
							</div>
							<?php endforeach; ?>
						</div>

						<div class="form-footer" id="member-change-controls">
							<a id="save-team-members" href="#" class="btn btn-primary">Save Changes</a>
							<a id="cancel-team_members" href="#" class="btn btn-text">Cancel</a>
						</div>
					</div>

					<div class="divider divider-simple">
						<div class="divider-content">
							<div class="divider-title">
								<h6>Reminders</h6>
								<small>(<?php echo number_format($oReminders->count); ?>)</small>
							</div>
							<div class="divider-separator">
								<hr/>
							</div>
							<div class="divider-actions">
								<a href="#" class="btn btn-default btn-sm" ng-hide="contract_reminders_editing" ng-click="contract_reminders_editing=true">
									edit
								</a>
								<a href="/reminders/add/<?php echo $oContract->contract_id; ?>" class="btn btn-default btn-sm">
									<span data-icon="add-small"></span>
								</a>
							</div>
						</div>
					</div>

					<div class="reminders" ng-hide="contract_reminders_editing">
						<?php $iCurrentTime = time(); $i5Days = strtotime('+5 days'); $i21Days = strtotime('+21 days'); foreach ($oReminders as $oReminder):
							$iAlertDate = strtotime($oReminder->alert_date); ?>
						<div class="reminder reminder-<?php if ($iAlertDate < $i5Days): ?>danger<?php elseif ($iAlertDate < $i21Days): ?>warning<?php else: ?>default<?php endif; ?>">
							<div class="reminder-content">
								<a href="/contracts/view/<?php echo $oReminder->contract_id; ?>" class="reminder-time"><?php //echo date('n/j',$iAlertDate); ?><?php echo convertto_local_datetime($iAlertDate,$time_zone,'%x',true); ?></a>
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
								<div class="reminder-time"><?php //echo date('n/j',strtotime($oReminder->alert_date)); ?><?php echo convertto_local_datetime($oReminder->alert_date,$time_zone,'%x'); ?></div>
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

					<div class="conversation">
						<div class="conversation-content">
							<div class="conversation-body" data-jquery="scroll-bottom">
								<?php foreach ($aLogs as $oLog):
									if ($oLog->type == ContractLogModel::TYPE_UPDATE):
								?>
								<div class="message default">
									<div class="message-content">
										<div class="message-body ">
											<div class="message-header">
												<h6>Contract Hound <small><?php //echo date('n/j g:ia',strtotime($oLog->create_date)); ?><?php echo convertto_local_datetime($oLog->create_date,$time_zone,'%x %X'); ?></small></h6>
											</div>
											<div class="message-activity">
												<p><?php echud($oLog->message); ?></p>
											</div>
											<?php /* ?>
											<div class="message-attachment">
												<a href="#" class="attachment">
													<div class="attachment-content">
														<div class="attachment-graphic">
															<span data-icon="file"></span>
														</div>
														<div class="attachment-body">
															<div class="attachment-name">
																<h6>CMS Vendor Contract.pdf</h6>
															</div>
														</div>
													</div>
												</a>
											</div>
											<?php */ ?>
										</div>
									</div>
								</div>
								<?php elseif ($oLog->type == ContractLogModel::TYPE_NOTE): ?>
								<div class="messages">
									<div class="message">
										<div class="message-content">
											<div class="message-graphic">
												<?php $oLogOwner = null; if (!empty($aLogOwners[$oLog->member_id])) {
													$oLogOwner = $aLogOwners[$oLog->member_id];
												}?>
												<div class="avatar avatar-medium" style="background-image: url(<?php
													if (!empty($oLogOwner) && $oLogOwner->avatar): ?>/uas/<?php echo $oLogOwner->avatar; ?><?php else: ?>/ui/img/samples/avatar1.jpg<?php endif;
														?>)">
													<img src="<?php
													if (!empty($oLogOwner) && $oLogOwner->avatar): ?>/uas/<?php echo $oLogOwner->avatar; ?><?php else: ?>/ui/img/samples/avatar1.jpg<?php endif;
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
								</div>
								<?php else: ?>
								<div class="message danger">
									<div class="message-content">
										<div class="message-body ">
											<div class="message-header">
												<h6>Contract Hound <small><?php //echo date('n/j g:ia',strtotime($oLog->create_date)); ?><?php echo convertto_local_datetime($oLog->create_date,$time_zone,'%x %X'); ?></small></h6>
											</div>
											<div class="message-comment">
												<blockquote><?php echud($oLog->message); ?></blockquote>
											</div>
										</div>
									</div>
								</div>
								<?php endif; endforeach; ?>
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
								<li><a href="#"><strong>Upload new version</strong><p class="help-block">Upload a new version of this contract.</p></a></li>
								<?php /*<li><a href="#"><strong>Mute notifications</strong><p class="help-block">You won't receive notifications from this contract. Unmute anytime.</p></a></li>*/ ?>
								<li><a href="/contracts/change_board/<?php echo $oContract->contract_id; ?>"><strong>Move..</strong><p class="help-block">Move this contract to a different board.</p></a></li>
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
						<a target="_blank" href="/ctcssa/<?php echo $oContract->file_hash; ?>" class="btn btn-primary">
							<span>View</span>
							<span data-icon="right-small"></span>
						</a>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<script src="/assets/js/plugins/validate/jquery.validate.min.js"></script>
<script>
	$('#view-members-change').click(function() {
		$(this).hide();
		$('#members-view').hide();
		$('#members-change').show();
	});

	$('.member-meta .dropdown-menu a').click(function() {
		$(this).parent().parent().siblings('.member-level').html($(this).text()+' <span class="caret"></span>');
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
		memberContent = $(this).parent().parent();
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
	
	var rules = {
		name:{
			required:true,
		},
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
	};
	<?php foreach ($oCustomFields as $oCustomField) { ?>
		<?php if ($oCustomField->required) { ?>
		rules['cf[' + <?php echo $oCustomField->custom_field_id; ?> + ']'] = {required:true};
		<?php } ?>
	<?php } ?>
	
	$("#contract-details").submit(function(e) {
		e.preventDefault();
	}).validate({
		rules: rules,
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
