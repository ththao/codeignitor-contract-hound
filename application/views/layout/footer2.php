						</div>
						<div class="layout-header">
							<div class="search">
								<div class="search-content">
									<div class="search-icons">
										<div data-icon="menu" mobile="" ng-click="layout='menu'"> </div>
									</div>
									<form class="search-body">
									</form>
									<div class="search-actions" ng-show="!search">
										<div class="dropdown" full="">
											<a href="#" class="btn btn-default" data-toggle="dropdown">
												<span>New</span>
												<i class="caret"></i>
											</a>
											<ul class="dropdown-menu dropdown-menu-right dropdown-menu-large">
												<li>
													<a href="/contracts/upload">
														<strong>New Contract</strong>
														<p class="help-block">Add new contracts to Contract Hound.</p>
													</a>
												</li>
												<li class="divider"></li>
												<li>
													<a href="/boards/add">
														<strong><?php echo lang('New Board'); ?></strong>
														<p class="help-block"><?php echo lang('Boards help you organize your contracts.'); ?></p>
													</a>
												</li>
											</ul>
										</div>
										<div class="dropdown" mobile="">
											<a href="#" class="btn btn-primary" data-toggle="dropdown">
												<span data-icon="add"><?php echo lang('New Contract or Board'); ?></span>
											</a>
											<ul class="dropdown-menu dropdown-menu-right dropdown-menu-large">
												<li>
													<a href="/contracts/upload">
														<strong>New Contract</strong>
														<p class="help-block">Add new contracts to Contract Hound.</p>
													</a>
												</li>
												<li class="divider"></li>
												<li>
													<a href="/boards/add">
														<strong><?php echo lang('New Board'); ?></strong>
														<p class="help-block"><?php echo lang('Boards help you organize your contracts.'); ?></p>
													</a>
												</li>
											</ul>
										</div>
										<a href="#" class="btn btn-default" ng-click="layout='notifications'">
											<span data-icon="notification"></span>
										</a>
										<a href="https://help.contracthound.com" class="btn btn-default" target="_blank">
											<span data-icon="help"></span>
										</a>
									</div>
									<div class="search-actions" ng-show="search">
										<a href="#" class="btn btn-link" ng-click="search=null">
											<span data-icon="close">Clear Search</span>
										</a>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="layout-overlay" ng-click="layout=null">
						<div class="layout-overlay-close">
							<div data-icon="close"></div>
						</div>
					</div>
				</div>

				<div class="layout-menu">
					<div class="layout-content">
						<div class="layout-body">
							<div class="menu">
								<div class="menu-content">
									<div class="menu-body">
										<?php /* upgrade notice */
										if ($bCurrentMemberIsAccountOwner && !empty($bShowUpgrade)): /*?>
										<div class="notice">
											<div class="notice-content">
												<div class="notice-header">
													<div class="notice-header-text">
														<h6>Usage</h6>
														<p class="help-block">48 of 50 Contracts</p>
													</div>
													<div class="notice-header-action">
														<a href="#" class="btn btn-sm btn-default">Upgrade</a>
													</div>
												</div>
												<div class="notice-body">
													<div class="progress">
														<div class="progress-bar progress-bar-danger" style="width: 96%"></div>
													</div>
												</div>
											</div>
										</div>
										<?php */ elseif ($bCurrentMemberIsAccountOwner && !empty($oCurrentParentSub) && in_array($oCurrentParentSub->status,array(SubscriptionModel::StatusTrial,SubscriptionModel::StatusExpired))):
											if ($oCurrentParentSub->expire_date >= date('Y-m-d H:i:s')):
										?>
										<div class="notice">
											<div class="notice-content">
												<div class="notice-header">
													<div class="notice-header-text">
														<h6>Free Trial</h6>
														<p class="help-block"><?php
															$iDaysRemaining = days_diff($oCurrentParentSub->expire_date);
															if ($iDaysRemaining < 0) { $iDaysRemaining = 0; }
															echo $iDaysRemaining; ?> Day<?php if ($iDaysRemaining > 1): ?>s<?php endif; ?> Left</p>
													</div>
													<div class="notice-header-action">
														<a href="/billing/upgrade" class="btn btn-sm btn-default">Upgrade</a>
													</div>
												</div>
											</div>
										</div>
										<?php else: ?>
										<div class="notice">
											<div class="notice-content">
												<div class="notice-header">
													<div class="notice-header-text">
														<h6>Free Trial</h6>
														<p class="help-block">Expired</p>
													</div>
													<div class="notice-header-action">
														<a href="/billing/upgrade" class="btn btn-sm btn-default">Upgrade</a>
													</div>
												</div>
											</div>
										</div>
										<?php endif; endif; ?>

										<div class="menu-section">
											<div class="menu-section-body">
												<ul class="list-group list-group-bold list-group-trim">
													<li><a href="/welcome" class="list-group-item<?php if (!empty($bIsDashboard)): ?> active<?php endif; ?>">Dashboard</a></li>
													<?php if (!empty($_SESSION['member_orig_parent_id']) && !empty($_SESSION['member_parent_id']) && $_SESSION['member_parent_id'] != $_SESSION['member_orig_parent_id']): ?>
													<li>
														<a href="/members/member_login_as" class="list-group-item"><?php 
															if (!empty($_SESSION['member_switch_parent_name'])): ?>Current Account: <?php 
																echud($_SESSION['member_switch_parent_name']); ?><br/><?php 
															endif; ?><span class="btn btn-primary btn-xs" style="margin-top:5px">Switch Back To Primary Account</span></a>
													</li>
													<?php endif; ?>
												</ul>
											</div>
										</div>

										<div class="menu-section">
											<div class="menu-section-header">
												<div class="divider">
													<div class="divider-content">
														<div class="divider-title">
															<h6>Contracts</h6>
															<small>Recent</small>
														</div>
														<div class="divider-separator">
															<hr/>
														</div>
														<div class="divider-actions">
															<a href="/contracts/upload" class="btn btn-default btn-sm btn-icon">
																<span data-icon="add-small"></span>
															</a>
														</div>
													</div>
												</div>
											</div>
											<div class="menu-section-body">
												<ul class="list-group list-group-trim">
													<?php if (!empty($oContractsSidebar)):
														foreach ($oContractsSidebar as $oContractSidebar): ?>
													<a href="/contracts/view/<?php echo $oContractSidebar->contract_id; ?>" class="list-group-item"><?php
														if (strlen($oContractSidebar->name) > 25) {
															echud(substr($oContractSidebar->name,0,25).'...');
														} else {
															echud($oContractSidebar->name);
														}
													?></a>
													<?php endforeach; endif; ?>
												</ul>
												<div class="menu-section-more">
													<a class="small" href="/contracts">View All</a>
												</div>
											</div>
										</div>
										<div class="menu-section <?php echo $this->uri->segment(1) == 'welcome' ? 'hide' : ''; ?>">
											<div class="menu-section-header">
												<div class="divider">
													<div class="divider-content">
														<div class="divider-title">
															<h6><?php echo lang('Boards'); ?></h6>
															<small>Recent</small>
														</div>
														<div class="divider-separator">
															<hr/>
														</div>
														<div class="divider-actions">
															<a href="/boards/add" class="btn btn-default btn-sm btn-icon">
																<span data-icon="add-small"></span>
															</a>
														</div>
													</div>
												</div>
											</div>
											<div class="menu-section-body">
												<ul class="list-group list-group-trim">
													<?php if (!empty($oBoardsSidebar)):
														foreach ($oBoardsSidebar as $oBoardSidebar): ?>
													<a href="/boards/view/<?php echo $oBoardSidebar->board_id; ?>" class="list-group-item"><?php echud($oBoardSidebar->name); ?></a>
													<?php endforeach; endif; ?>
												</ul>
												<div class="menu-section-more">
													<a class="small" href="/boards">View All</a>
												</div>
											</div>
										</div>
									</div>
								</div>
								<div class="menu-footer">
									<div class="menu-credits">
										<div class="menu-logo">
											<a href="/welcome" class="menu-logo-link">
												<img src="/ui/img/logos/contracthound-lockup-menu.svg" />
											</a>
										</div>
									</div>
								</div>
							</div>
						</div>

						<div class="layout-header">
							<div class="account dropdown">
								<div class="account-content" data-toggle="dropdown">
									<div class="account-image">
										<div class="avatar avatar-medium" style="background-image: url(<?php if (!empty($_SESSION['member_avatar'])): ?>/uas/<?php echo $_SESSION['member_avatar'];
										else: ?>/ui/img/avatars/default.png<?php endif; ?>)">
											<img src="<?php if (!empty($_SESSION['member_avatar'])): ?>/uas/<?php echo $_SESSION['member_avatar'];
												else: ?>/ui/img/avatars/default.png<?php endif; ?>" />
										</div>
									</div>
									<div class="account-name">
										<h4><?php if (!empty($_SESSION['member_name'])) { echud($_SESSION['member_name']); } else { echo 'User'; } ?> </h4>
									</div>
									<div class="account-caret">
										<div data-icon="down-small">Show Menu</div>
									</div>
								</div>
								<ul class="dropdown-menu dropdown-menu-large dropdown-menu-notice-top">
									<li class="dropdown-header">User Settings</li>
									<li><a href="/members/settings"><strong>My Settings</strong></a></li>
									<?php /*<li><a href="/members/contact_preferences"><strong>Contact Preferences</strong></a></li>*/ ?>

									<li class="divider"></li>
									<li><a href="/customfields"><strong>Custom Fields</strong></a></li>

									<?php if ($bCurrentMemberIsAccountOwner): ?>
									<li class="divider"></li>
									<li class="dropdown-header">Admin Settings</li>
									<?php if ($bCurrentMemberIsAccountOwner && can_access_feature('docusign',$iCurrentMemberId)): ?>
									<li><a href="/integrations"><strong>Integrations</strong></a></li>
									<?php endif; ?>
									<li><a href="/users"><strong>User Management</strong></a></li>
									<?php if ($bCurrentMemberIsAccountOwner && !empty($oCurrentParentSub)): ?>
									<li><a href="/billing"><strong>Billing</strong></a></li>
									<li><a href="/billing/upgrade"><strong>Upgrade</strong></a></li>
									<?php endif; ?>
									<?php endif; ?>

									<?php if (!empty($bCurrentMemberHasAccessToOtherAccounts)): ?>
									<li class="divider"></li>
									<li><a href="/members/member_login_as"><strong>Switch Account</strong></a></li>
									<?php endif; ?>

									<?php if (!empty($bCurrentlyLoggedInMemberIsAdmin) && empty($_SESSION['admin_member_id'])): ?>
									<li class="divider"></li>
									<li><a href="/members/admin_list"><strong>Admin - Members</strong></a></li>
									<?php elseif (!empty($_SESSION['admin_member_id'])): ?>
									<li class="divider"></li>
									<li><a href="/members/admin_logout"><strong>Admin - Logout</strong></a></li>
									<?php endif; ?>

									<li class="divider"></li>
									<li><a target="_blank" href="https://help.contracthound.com"><strong>Help</strong></a></li>
									<li><a href="/members/logout"><strong>Sign Out</strong></a></li>
								</ul>
							</div>
						</div>
					</div>
				</div>

				<!-- notifications -->
				<div class="layout-notifications">
					<div class="layout-content">
						<div class="layout-body">
							<div class="notifications">
								<div class="messages">
									<?php if ($aSidebarNotifications && is_array($aSidebarNotifications)): ?>
									<?php foreach ($aSidebarNotifications as $oLog):
										$oLogOwner = null;
										if ($oLog->member) { $oLogOwner = $oLog->member; }

									if ($oLog->type == ContractLogModel::TYPE_UPDATE): ?>
									<div class="message linked">
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
														?> <small><?php //echo date('n/j g:ia',strtotime($oLog->create_date)); ?><?php echo convertto_local_datetime($oLog->create_date,$time_zone,'%x %X'); ?>
														</small></h6>
												</div>
												<div class="message-activity">
													<p><?php echud($oLog->message); ?> <a href="/contracts/view/<?php echo $oLog->contract_id; ?>"><?php echud($oLog->name); ?></a></p>
												</div>
											</div>
										</div>
										<a class="message-link" href="/contracts/view/<?php echo $oLog->contract_id; ?>">Entire Message Link</a>
									</div>
									<?php elseif ($oLog->type == ContractLogModel::TYPE_FULLY_APPROVED): ?>
									<div class="message success linked">
										<div class="message-content">
											<div class="message-body ">
												<div class="message-header">
													<h6>Contract Hound <small><?php //echo date('n/j g:ia',strtotime($oLog->create_date)); ?><?php echo convertto_local_datetime($oLog->create_date,$time_zone,'%x %X'); ?>
														</small></h6>
												</div>
												<div class="message-activity">
													<p><a href="/contracts/view/<?php echo $oLog->contract_id; ?>"><?php echud($oLog->name); ?></a> is now fully approved.</p>
												</div>
											</div>
										</div>
										<a class="message-link" href="/contracts/view/<?php echo $oLog->contract_id; ?>">Entire Message Link</a>
									</div>
									<?php elseif ($oLog->type == ContractLogModel::TYPE_APPROVED): ?>
									<div class="message linked">
										<div class="message-content">
											<div class="message-graphic">
												<div class="avatar avatar-medium avatar-icon success">
													<span data-icon="approved"></span>
												</div>
											</div>
											<div class="message-body">
												<div class="message-header">
													<h6><?php if (!empty($oLogOwner) && $oLogOwner->name){ echud($oLogOwner->name); } elseif (!empty($oLogOwner)) { echud($oLogOwner->email); } else { echo 'Missing'; }
														?> <small><?php //echo date('n/j g:ia',strtotime($oLog->create_date)); ?><?php echo convertto_local_datetime($oLog->create_date,$time_zone,'%x %X'); ?>
														</small></h6>
												</div>
												<div class="message-activity">
													<p>approved <a href="/contracts/view/<?php echo $oLog->contract_id; ?>"><?php echud($oLog->name); ?></a>.</p>
												</div>
											</div>
										</div>
										<a class="message-link" href="/contracts/view/<?php echo $oLog->contract_id; ?>">Entire Message Link</a>
									</div>
									<?php elseif ($oLog->type == ContractLogModel::TYPE_REJECTED): ?>
									<div class="message linked">
										<div class="message-content">
											<div class="message-graphic">
												<div class="avatar avatar-medium avatar-icon danger">
													<span data-icon="rejected"></span>
												</div>
											</div>
											<div class="message-body">
												<div class="message-header">
													<h6><?php if (!empty($oLogOwner) && $oLogOwner->name){ echud($oLogOwner->name); } elseif (!empty($oLogOwner)) { echud($oLogOwner->email); } else { echo 'Missing'; }
														?> <small><?php //echo date('n/j g:ia',strtotime($oLog->create_date)); ?><?php echo convertto_local_datetime($oLog->create_date,$time_zone,'%x %X'); ?>
														</small></h6>
												</div>
												<div class="message-activity">
													<p>rejected <a href="/contracts/view/<?php echo $oLog->contract_id; ?>"><?php echud($oLog->name); ?></a>.</p>
												</div>
											</div>
										</div>
										<a class="message-link" href="/contracts/view/<?php echo $oLog->contract_id; ?>">Entire Message Link</a>
									</div>
									<?php elseif ($oLog->type == ContractLogModel::TYPE_NOTE): ?>
									<div class="message linked">
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
													?> <small><?php //echo date('n/j g:ia',strtotime($oLog->create_date)); ?><?php echo convertto_local_datetime($oLog->create_date,$time_zone,'%x %X'); ?>
														</small></h6>
												</div>
												<div class="message-activity">
													<p>added a comment to <a href="/contracts/view/<?php echo $oLog->contract_id; ?>"><?php echud($oLog->name); ?></a></p>
												</div>
												<div class="message-comment">
													<blockquote><?php echud($oLog->message); ?></blockquote>
												</div>
											</div>
										</div>
										<a class="message-link" href="/contracts/view/<?php echo $oLog->contract_id; ?>">Entire Message Link</a>
									</div>
									<?php else: ?>
									<div class="message danger linked">
										<div class="message-content">
											<div class="message-body ">
												<div class="message-header">
													<h6>Contract Hound <small><?php// echo date('n/j g:ia',strtotime($oLog->create_date)); ?><?php echo convertto_local_datetime($oLog->create_date,$time_zone,'%x %X'); ?>
														</small></h6>
												</div>
												<div class="message-comment">
													<blockquote><?php echud($oLog->message); ?></blockquote>
												</div>
											</div>
										</div>
										<a class="message-link" href="/contracts/view/<?php echo $oLog->contract_id; ?>">Entire Message Link</a>
									</div>
									<?php endif; endforeach; ?>
									<?php endif; ?>
								</div>
							</div>
						</div>

						<div class="layout-header">
							<div class="notifications-header">
								<div class="notifications-header-content">
									<div class="notifications-header-title">
										<h4>Notifications</h4>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		
		<style>[data-icon="help"]:before{content:"\003F"; font-family: 'Lato', sans-serif; line-height: 1;}</style>