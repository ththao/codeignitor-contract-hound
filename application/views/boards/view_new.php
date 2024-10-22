<style>
	.sub-folders {
		display: flex;
		flex-wrap: nowrap;
		overflow: auto;
	}

	.sub-folder {
		border: 1px solid #DEDEDE;
		border-radius: 5px;
		padding-left: 5px;
		padding-right: 5px;
		padding-bottom: 10px;
		float: left;
		margin-right: 10px;
		cursor: pointer;
	}

	.sub-folder small {
		margin-left: 0px;
		white-space: nowrap;
	}
</style>
<script>
	$(document).ready(function() {
		$(document).on('click', '.sub-folder', function() {
			window.location = $(this).attr('href');
		});
	});
</script>
<div class="layout-panel">
	<div class="layout-panel-body" style="border-top: <?php echo $oSubBoards->count ? '180' : '80'; ?>px solid transparent;">
		<div class="layout-panel-main">
			<div class="layout-section">
				<div class="divider">
					<div class="divider-content">
						<div class="divider-title">
							<h6>Contracts</h6>
							<small>(<?php echo number_format($oContracts->count); ?>)</small>
						</div>
						<div class="divider-separator">
							<hr />
						</div>
						<div class="divider-actions">
							<div class="dropdown">
								<a href="#" class="text-light text-italic" data-toggle="dropdown">
									<span full>Sort by: </span><strong><?php
																		switch ($sSortSC) {
																			case 'cd':
																				echo 'Date Created<span full> (Newest)';
																				break;
																			case 'na':
																				echo 'Name<span full> (A-Z)';
																				break;
																			case 'va':
																				echo 'Vendor<span full> (A-Z)';
																				break;
																			case 'aa':
																				echo 'Amount<span full> (Lowest)';
																				break;
																			case 'ad':
																				echo 'Amount<span full> (Highest)';
																				break;
																			case 'sd':
																				echo 'Start<span full> (Latest)';
																				break;
																			case 'ed':
																				echo 'End<span full> (Latest)';
																				break;
																		} ?></span></strong>
									<span class="caret"></span>
								</a>
								<ul class="dropdown-menu dropdown-menu-right">
									<li><a href="/boards/view/<?php echo $oBoard->board_id; ?>?s=na">Name <em>(A-Z)</em></a></li>
									<li><a href="/boards/view/<?php echo $oBoard->board_id; ?>?s=va">Vendor <em>(A-Z)</em></a></li>
									<?php /*<li><a href="#">Owner <em>(A-Z)</em></a></li>*/ ?>
									<li class="divider"></li>
									<li><a href="/boards/view/<?php echo $oBoard->board_id; ?>?s=ad">Amount <em>(Highest)</em></a></li>
									<li><a href="/boards/view/<?php echo $oBoard->board_id; ?>?s=aa">Amount <em>(Lowest)</em></a></li>
									<li class="divider"></li>
									<li><a href="/boards/view/<?php echo $oBoard->board_id; ?>?s=sd">Start <em>(Latest)</em></a></li>
									<li><a href="/boards/view/<?php echo $oBoard->board_id; ?>?s=ed">End <em>(Latest)</em></a></li>
								</ul>
							</div>
						</div>
					</div>
				</div>

				<div class="table-responsive table-alignment">
					<table class="table table-hover table-borderless table-justified">
						<thead>
							<tr>
								<th>Contract</th>
								<th>Vendor</th>
								<th>Amount</th>
								<th>Type</th>
								<th>Start</th>
								<th>End</th>
								<th class="cell-small"></th>
								<th>Owner</th>
								<th class="cell-small"></th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ($oContracts as $oContract) :
								if (!in_array($oContract->contract_id, $aContractIdsWithAccess)) : ?>
									<tr>
										<td><a href="/contracts/view/<?php echo $oContract->contract_id; ?>" class="cell-link cell-protected"><?php echud($oContract->name); ?></a></td>
										<td><a href="/contracts/view/<?php echo $oContract->contract_id; ?>" class="cell-link"></a></td>
										<td><a href="/contracts/view/<?php echo $oContract->contract_id; ?>" class="cell-link"></a></td>
										<td><a href="/contracts/view/<?php echo $oContract->contract_id; ?>" class="cell-link"></a></td>
										<td><a href="/contracts/view/<?php echo $oContract->contract_id; ?>" class="cell-link"></a></td>
										<td><a href="/contracts/view/<?php echo $oContract->contract_id; ?>" class="cell-link"></a></td>
										<?php if (!empty($aOwners[$oContract->owner_id])) :
											$oOwner = $aOwners[$oContract->owner_id]; ?>
											<td class="cell-small">
												<a href="/users/profile/<?php echo $oOwner->member_id; ?>" class="cell-link">
													<div class="avatar" style="background-image: url(<?php if ($oOwner->avatar) : ?>/uas/<?php echo $oOwner->avatar; ?><?php else : ?>/ui/img/avatars/default.png<?php endif; ?>)">
														<img src="<?php if ($oOwner->avatar) : ?>/uas/<?php echo $oOwner->avatar; ?><?php else : ?>/ui/img/avatars/default.png<?php endif; ?>" />
													</div>
												</a>
											</td>
											<td><a href="/users/profile/<?php echo $oOwner->member_id; ?>" class="cell-link alternate"><?php if ($oOwner->name) {
																																			echud($oOwner->name);
																																		} else {
																																			echud($oOwner->email);
																																		} ?></a></td>
										<?php else : ?>
											<td colspan="2">&nbsp;</td>
										<?php endif; ?>
										<td><a href="/contracts/view/<?php echo $oContract->contract_id; ?>" class="cell-link"></a></td>
									</tr>
								<?php else : ?>
									<tr>
										<td><a href="/contracts/view/<?php echo $oContract->contract_id; ?>" class="cell-link"><?php echud($oContract->name); ?></a></td>
										<td><a href="/contracts/view/<?php echo $oContract->contract_id; ?>" class="cell-link"><?php echud($oContract->company); ?></a></td>
										<td><a href="/contracts/view/<?php echo $oContract->contract_id; ?>" class="cell-link"><?php if ($oContract->valued) {
																																	echo $sCurrency . number_format($oContract->valued);
																																} ?></a></td>
										<td><a href="/contracts/view/<?php echo $oContract->contract_id; ?>" class="cell-link"><?php echo ($oContract->type == ContractModel::TYPE_SELL_SIDE) ? tl('Sell-side') : tl('Buy-side'); ?></a></td>
										<td><a href="/contracts/view/<?php echo $oContract->contract_id; ?>" class="cell-link"><?php if ($oContract->start_date) { /*echo date('n/j/Y',strtotime($oContract->start_date));*/
																																	echo convertto_local_datetime($oContract->start_date, $time_zone, '%x');
																																} ?></a></td>
										<td><a href="/contracts/view/<?php echo $oContract->contract_id; ?>" class="cell-link"><?php if ($oContract->end_date) { /*echo date('n/j/Y',strtotime($oContract->end_date)); */
																																	echo convertto_local_datetime($oContract->end_date, $time_zone, '%x');
																																} ?></a></td>
										<?php if (!empty($aOwners[$oContract->owner_id])) :
											$oOwner = $aOwners[$oContract->owner_id]; ?>
											<td class="cell-small">
												<a href="/users/profile/<?php echo $oOwner->member_id; ?>" class="cell-link">
													<div class="avatar" style="background-image: url(<?php if ($oOwner->avatar) : ?>/uas/<?php echo $oOwner->avatar; ?><?php else : ?>/ui/img/avatars/default.png<?php endif; ?>)">
														<img src="<?php if ($oOwner->avatar) : ?>/uas/<?php echo $oOwner->avatar; ?><?php else : ?>/ui/img/avatars/default.png<?php endif; ?>" />
													</div>
												</a>
											</td>
											<td><a href="/users/profile/<?php echo $oOwner->member_id; ?>" class="cell-link alternate"><?php if ($oOwner->name) {
																																			echud($oOwner->name);
																																		} else {
																																			echud($oOwner->email);
																																		} ?></a></td>
										<?php else : ?>
											<td colspan="2">&nbsp;</td>
										<?php endif; ?>

										<td class="cell-small dropdown">
											<a href="/contracts/view/<?php echo $oContract->contract_id; ?>" class="cell-link text-light" data-toggle="dropdown"><span class="caret"></span></a>
											<ul class="dropdown-menu dropdown-menu-right">
												<li><a href="/boards/remove_contract/<?php echo $oContract->contract_id; ?>"><?php echo lang('Remove from board'); ?></a></li>
											</ul>
										</td>
									</tr>
							<?php endif;
							endforeach; ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>

	<div class="layout-panel-header">
		<div class="title">
			<div class="title-content">
				<div class="title-name">
					<h4>
						<a href="/boards"><span data-icon="board"></span></a>
						<?php echo $oBoard->parent_link; ?><?php echud($oBoard->name); ?>
						<small><?php echo lang('Board'); ?></small>
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
								<li>
									<a href="#" data-toggle="modal" data-target="#rename-board">
										<strong><?php echo lang('Rename Board'); ?></strong>
										<p class="help-block"><?php echo lang('Change the name of your board so your team can easily find its contracts later.'); ?></p>
									</a>
								</li>
								<li class="divider"></li>
								<li>
									<a href="#" data-toggle="modal" data-target="#delete-board"><strong><?php echo lang('Delete Board'); ?></strong>
										<p class="help-block"><?php echo lang('All contracts will be removed from this board and moved to "All Contracts".'); ?></p>
									</a>
								</li>
								<li class="divider" mobile></li>
								<li mobile>
									<a href="#" data-toggle="modal" data-target="#add-sub-board"><strong><?php echo lang('Add Sub Board'); ?></strong></a>
								</li>
							</ul>
						</div>
					</div>
					<div class="btn-group" full>
						<a href="#" class="btn btn-primary" data-toggle="modal" data-target="#add-sub-board"><?php echo lang('Add Sub Board'); ?></a>
					</div>
					<div class="btn-group" full>
						<a href="/contracts/upload" class="btn btn-primary" <?php /* data-toggle="modal" data-target="#add-contracts"*/ ?>>Add Contracts</a>
					</div>
					<div class="btn-group" mobile>
						<a href="/contracts/upload" class="btn btn-primary" <?php /* data-toggle="modal" data-target="#add-contracts"*/ ?>><span data-icon="add">Add Contracts</span></a>
					</div>
				</div>
			</div>
		</div>
		<?php if ($oSubBoards->count) : ?>
			<div class="title">
				<div class="sub-folders">
					<?php foreach ($oSubBoards as $i => $oSubBoard) : ?>
						<div class="sub-folder" href="/boards/view/<?php echo $oSubBoard->board_id; ?>">
							<div class="sub-folder-name">
								<h5 style="color: #059672;">
									<span data-icon="board"></span> <?php echud($oSubBoard->name); ?><br />
									<small><?php echo ($oSubBoard->contract_count ? $oSubBoard->contract_count : '0') . ($oSubBoard->contract_count == 1 ? ' contract' : ' contracts'); ?>&nbsp;-&nbsp;<?php echo ($oSubBoard->sub_board_count ? $oSubBoard->sub_board_count : '0') . ($oSubBoard->sub_board_count == 1 ? ' sub folder' : ' sub folders'); ?></small>
								</h5>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		<?php endif; ?>
	</div>
</div>


<div class="modal fade" id="delete-board">
	<div class="modal-container">
		<div class="modal-dialog">
			<div class="modal-content">
				<form method="post" action="/boards/delete/<?php echo $oBoard->board_id; ?>">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span data-icon="close-small">Close</span></button>
						<h3 class="modal-title"><?php echo lang('Delete this Board?'); ?></h3>
						<p><?php echo lang('Are you sure you want to delete this board?'); ?></p>
					</div>
					<div class="modal-body">
						<?php /*	<h4>Move Contracts to...</h4>
					<select class="form-control input-lg" data-value="0">
						<option value="0">All Contracts</option>
						<option value="1">Buy Side
						<option value="2">Sell-Side</option>
						<option value="3">Marketing</option>
						<option value="4">Xerox Contracts</option>
					</select>
					<p class="help-block">
						When you delete this board, your contracts will still be available in <a href="../browse-contracts">All Contracts</a>. Would you also like to these contracts to a different board?
					</p>*/ ?>
						<p><?php echo lang('Contracts for this board will be unassociated.'); ?></p>
					</div>
					<div class="modal-footer">
						<button type="submit" class="btn btn-lg btn-danger"><?php echo lang('Delete Board'); ?></button>
						<a href="#" class="btn btn-lg btn-text" data-dismiss="modal">Cancel</a>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<div class="modal modal-sm fade" id="rename-board">
	<div class="modal-container">
		<div class="modal-dialog">
			<div class="modal-content">
				<form method="post" action="/boards/rename_board/<?php echo $oBoard->board_id; ?>">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span data-icon="close-small">Close</span></button>
						<h3 class="modal-title"><?php echo lang('Rename this board'); ?></h3>
					</div>
					<div class="modal-body">
						<input name="name" type="text" class="form-control input-lg" value="<?php echud($oBoard->name); ?>" />
						<p class="help-block"><?php echo lang('Enter a name that describes the kind of contracts that will live here, like "Marketing Team Contracts" or "Sell-side Contracts."'); ?></p>
					</div>
					<div class="modal-footer modal-footer-left">
						<button type="submit" class="btn btn-lg btn-primary"><?php echo lang('Rename Board'); ?></button>
						<a href="#" class="btn btn-lg btn-text" data-dismiss="modal">Cancel</a>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<div class="modal modal-sm fade" id="add-sub-board">
	<div class="modal-container">
		<div class="modal-dialog">
			<div class="modal-content">
				<form method="post" action="/boards/add_sub_board/<?php echo $oBoard->board_id; ?>">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span data-icon="close-small">Close</span></button>
						<h3 class="modal-title"><?php echo lang('New Sub Board'); ?></h3>
					</div>
					<div class="modal-body">
						<input name="name" type="text" class="form-control input-lg" value="" placeholder="<?php echo lang('Marketing Board...'); ?>" />
						<p class="help-block"><?php echo lang('Enter a name that describes the kind of contracts that will live here, like "Marketing Team Contracts" or "Sell-side Contracts."'); ?></p>
					</div>
					<div class="modal-footer modal-footer-left">
						<button type="submit" class="btn btn-lg btn-primary"><?php echo lang('Create Board'); ?></button>
						<a href="#" class="btn btn-lg btn-text" data-dismiss="modal">Cancel</a>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>