<script src="/ui/country-select/js/countrySelect.js"></script>
<link rel="stylesheet" type="text/css" href="/ui/country-select/css/countrySelect.css">
<style>
    .country-select { width: 100%; }
    #country { background-color: #e9eef5; cursor: pointer; }
    #country:hover { background-color: #dce1e9; }
</style>

<div class="layout-panel">
	<div class="layout-panel-body">
		<div class="layout-panel-main">
			<div class="layout-section">
				<div class="profile">
					<div class="profile-content">
						<div class="profile-photo">
							<div class="avatar avatar-xlarge"
								style="background-image: url(<?php if ($oMember->avatar): ?>/uas/<?php echo $oMember->avatar; ?><?php else: ?>/ui/img/avatars/default.png<?php endif; ?>)">
								<img src="<?php if ($oMember->avatar): ?>/uas/<?php echo $oMember->avatar; ?><?php else: ?>/ui/img/avatars/default.png<?php endif; ?>" />
								<div class="avatar-actions">
									<a href="#" class="avatar-action avatar-action-inner">Update</a>
									<a href="#" class="avatar-action avatar-action-outer text-danger">Remove</a>
								</div>
							</div>
						</div>

						<div class="profile-body">
							<form method="post" action="/members/settings" enctype="multipart/form-data">
								<input type="hidden" name="remove_avatar" value="0" />
								<div class="form-grid form-grid-large">
									<table>
										<tr>
											<td class="form-label"><label>Name:</label></td>
											<td colspan="1" class="form-response">
												<input name="first_name" class="form-control input-lg" type="text"
													placeholder="Jon" value="<?php echud($oMember->first_name); ?>" />
											</td>
											<td colspan="1" class="form-response">&nbsp;</td>
											<td colspan="1" class="form-response">
												<input name="last_name" class="form-control input-lg" type="text"
													placeholder="Doe" value="<?php echud($oMember->last_name); ?>" />
											</td>
										</tr>
										<?php if ($bCurrentMemberIsAccountOwner): ?>
										<tr>
											<td class="form-label"><label>Company:</label></td>
											<td colspan="3" class="form-response">
												<input name="company" class="form-control input-lg" type="text"
													placeholder="My Company" value="<?php echud($oMember->company); ?>" />
											</td>
										</tr>
										<?php endif; ?>
										<tr>
											<td class="form-label"><label>Role:</label></td>
											<td colspan="3" class="form-response">
												<input name="role" class="form-control input-lg" type="text"
													placeholder="Contract Manager" value="<?php echud($oMember->role); ?>" />
											</td>
										</tr>
										<tr>
											<td class="form-label"><label>Email:</label></td>
											<td colspan="3" class="form-response">
												<input name="email" class="form-control input-lg" type="text"
													placeholder="email@address.com" value="<?php echud($oMember->email); ?>" />
											</td>
										</tr>
										<tr>
											<td class="form-label"><label>New Password:</label></td>
											<td colspan="3" class="form-response">
												<input name="new_password" class="form-control input-lg" type="password" value="" />
											</td>
										</tr>
										<tr>
											<td class="form-label"><label>Locale:</label></td>
											<td colspan="3" class="form-response">
												<input type="text" class="form-control input-lg" id="country" placeholder="Select Country" readonly />
												<input type="hidden" id="country_code" name="country_code" />
											</td>
										</tr>
										<tr>
											<td class="form-label"><label>Currency:</label></td>
											<td colspan="3" class="form-response">
												<select name="currency" id="currency" class="form-control input-lg">
													<?php
													   if ($currencies) {
													       if (!$oMember->currency) {
													           $oMember->currency = 'USD';
													       }
													       foreach ($currencies as $currency) {
													           $selected = $oMember->currency == $currency ? 'selected="selected"' : '' ;
													           echo '<option '.$selected.' value="'.$currency.'">'.$currency.'</option>';
												           }
											           }
													?>
												</select>
											</td>
										</tr>
									</table>
								</div>
								<div class="profile-body-actions">
									<a href="/welcome" class="btn btn-text btn-lg" ng-click="editing_user=false">Cancel</a>
									<button type="submit" class="btn btn-primary btn-lg">Save Changes</button>
								</div>
							</form>
						</div>
					</div>
				</div>
				<?php if (!empty($oAccessLogs) && $oAccessLogs->count): ?>
				<div class="col-md-6 col-md-offset-3 col-sm-12 table-responsive">
					<h4>Login Activity (last <?php echo $oAccessLogs->count; ?>)</h4>
					<table class="table table-bordered">
						<thead>
							<tr>
								<th>Date</th>
								<th>First Name</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ($oAccessLogs as $oAccessLog): ?>
							<tr>
								<th scope="row"><?php //echo date('n/j g:ia',strtotime($oAccessLog->create_date)); ?><?php echo convertto_local_datetime($oAccessLog->create_date,$time_zone,'%x %X'); ?>
								</th>
								<td><?php echo $oAccessLog->getReadableActionType(); ?></td>
							</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
	<div class="layout-panel-header">
		<div class="title">
			<div class="title-content">
				<div class="title-name">
					<h3>My Settings</h3>
				</div>
			</div>
		</div>
	</div>
</div>
<script>

	$(document).ready(function() {
		var countries = <?php echo json_encode($countries); ?>;
		
		Dropzone.autoDiscover = false;
		var drop = new Dropzone(".avatar-action-inner",
			{
				url : '/members/settings_ajax',
				maxFilesize : 15, // 15mb
				paramName : 'avatar',
				uploadMultiple : false,
				maxFiles : 1,
				dictResponseError : 'Unable to complete request at this time.',
				dictFileTooBig : 'Files must be 15 MB or less.',
				dictMaxFilesExceeded : 'You can upload a maximun if 5 files at a time',
				success: function(file, response){
					console.log('uploaded: '+file);
					//console.log('/uas/'+response.src);
					//alert(response);

					response = jQuery.parseJSON(response);
					$('.avatar').css('background-image', 'url(/uas/'+response.src+'?r='+Math.floor(Math.random() * 100)+')');

					$.notify({message: 'Avatar Updated.'},{delay: 7000, type: 'success'});
				}
			}
		);
		
    	$("#country").countrySelect({
    		defaultCountry: "<?php echo $defaultCountry; ?>",
    		preferredCountries: ['us', 'au', 'gb', 'sg'],
			responsiveDropdown: true
    	});
            	
    	$(document).on('click', '#country', function(e) {
    		$('.selected-flag').trigger('click');
    	});
		
		$(document).on('change', '#country_code', function() {
			var currency = countries[$(this).val()];
			if (!currency) {
				currency = 'USD';
			}
			$('#currency').val(currency);
		});
	});
</script>