					<div class="layout-section">
						<div class="tabs">
							<div class="tabs-content">
								<div class="tabs-header">
									<h2>Configure Fields</h2>
								</div>
							</div>
						</div>

						<div>

							<div class="divider divider-gap">
								<div class="divider-content">
									<div class="divider-title">
										<h6>Custom Fields <small>(editable per account)</small></h6>
									</div>
									<div class="divider-separator">
										<hr/>
									</div>
								</div>
							</div>

							<div class="fields">
								<div class="empty" ng-if="!custom_fields.length">
									<div class="empty-content">
										<h4>Track Custom Fields</h4>
										<p class="help-block">With custom fields, your team can track additional information per contract. Catalog the contract details relevant to your business.</p>
										<a class="btn btn-info btn-sm" ng-click="add_field()">Create Custom Fields</a>
									</div>
								</div>
								<div class="field field-header" ng-if="custom_fields.length">
									<div class="field-lockup">
										<div class="field-attr field-attr-medium">Field Label</div>
										<div class="field-attr field-attr-large">Description</div>
										<div class="field-attr field-attr-medium">Field Type</div>
										<div class="field-attr field-attr-small">Required</div>
										<div class="field-attr field-attr-medium"></div>
									</div>
								</div>

								<div ng-class="{'field field-item':true,'field-editing':(field.editing)}" ng-repeat="field in custom_fields">
									<div class="field-lockup">
										<input type="hidden" ng-model="field.fieldid"/>
										<div class="field-attr field-attr-medium field-attr-header">
											<div class="field-attr-label">Field Label</div>
											<div class="field-attr-value">
												<div ng-if="!field.editing"><span ng-if="!field.label" class="meta">none...</span><span ng-if="field.label">[[field.label]]</span></div>
												<div ng-if="field.editing"><input type="text" class="form-control" ng-model="field.label"/></div>
											</div>
										</div>
										<div class="field-attr field-attr-large">
											<div class="field-attr-label">Description</div>
											<div class="field-attr-value">
												<div ng-if="!field.editing" class="meta"><span ng-if="!field.desc" class="meta">none...</span><span ng-if="field.desc">[[field.desc]]</span></div>
												<div ng-if="field.editing"><input type="text" class="form-control" ng-model="field.desc"/></div>
											</div>
										</div>
										<div class="field-attr field-attr-medium">
											<div class="field-attr-label">Field Type</div>
											<div class="field-attr-value">
												<div ng-if="!field.editing || field.is_checkbox">[[field.type]]</div>
												<div ng-if="field.editing && !field.is_checkbox"><select class="form-control" ng-model="field.type"><option value="">Choose type...</option><option value="text">text</option><option value="multiline">multiline</option><option value="checkbox">checkbox</option></select></div>
											</div>
										</div>
										<div class="field-attr field-attr-small ">
											<div class="field-attr-label">Required</div>
											<div class="field-attr-value">
												<div ng-if="!field.editing || field.is_checkbox" class="field-required">
													<span ng-if="!field.required || field.is_checkbox" class="meta">no</span>
													<span ng-if="field.required">yes</span>
												</div>
												<div ng-if="field.editing && !field.is_checkbox">
													<label class="option option-slim">
														<input type="checkbox" ng-model="field.required">
														<i class="option-icon"></i>
														Required
													</label>
												</div>
											</div>
										</div>
										<div class="field-attr field-attr-medium">
											<div class="field-attr-label"></div>
											<div class="field-attr-value">
												<div ng-if="!field.editing" class="field-actions"><a ng-click="field.editing=true" class="text-italic">edit</a></div>
												<div ng-if="field.editing" class="field-actions field-buttons">
													<a ng-click="field.editing=false" class="btn btn-text btn-sm">cancel</a>
													<a ng-click="remove_field($index)" class="btn btn-text btn-sm text-danger">remove</a>
													<a ng-click="save_field($index)" class="btn btn-primary btn-sm">save</a>
												</div>
											</div>

										</div>
									</div>
								</div>
								<div class="fields-footer" ng-if="custom_fields.length">
									<a class="btn btn-info btn-sm" ng-click="add_field()">new custom field</a>
								</div>
							</div>


							<div class="divider divider-gap">
								<div class="divider-content">
									<div class="divider-title">
										<h6>Default Fields <small>(not changeable)</small></h6>
									</div>
									<div class="divider-separator">
										<hr/>
									</div>
								</div>
							</div>

							<div class="fields">
								<div class="field field-header">
									<div class="field-lockup">
										<div class="field-attr field-attr-medium">Field Label</div>
										<div class="field-attr field-attr-large">Description</div>
										<div class="field-attr field-attr-medium">Field Type</div>
										<div class="field-attr field-attr-small">Required</div>
									</div>
								</div>

								<div class="field field-item" ng-repeat="field in default_fields">
									<div class="field-lockup">
										<div class="field-attr field-attr-medium field-attr-header">
											<div class="field-attr-label">Field Label</div>
											<div class="field-attr-value">
												<div><span ng-if="!field.label" class="meta">none...</span><span ng-if="field.label">[[field.label]]</span></div>
											</div>
										</div>
										<div class="field-attr field-attr-large">
											<div class="field-attr-label">Description</div>
											<div class="field-attr-value">
												<div class="meta"><span ng-if="!field.desc" class="meta">none...</span><span ng-if="field.desc">[[field.desc]]</span></div>
											</div>
										</div>
										<div class="field-attr field-attr-medium">
											<div class="field-attr-label">Field Type</div>
											<div class="field-attr-value">
												<div>[[field.type]]</div>
											</div>
										</div>
										<div class="field-attr field-attr-small">
											<div class="field-attr-label">Required</div>
											<div class="field-attr-value">
												<div><span ng-if="!field.required" class="meta">no</span><span ng-if="field.required">yes</span></div>
											</div>
										</div>
									</div>
								</div>
							</div>

						</div>

						<div ng-show="fields_mode=='boards'">

						</div>

					</div>

<script>
	$(document).ready(function(){
		var scope = angular.element($('body')[0]).scope();
		scope.$apply(function() {
			scope.default_fields = [
				{label:'Name',default:null,desc:'contract title...',required:false,type:'text'},
				{label:'Company',default:null,desc:'company name...',required:false,type:'text'},
				{label:'Start',default:null,desc:'MM/DD/YYYY',required:false,type:'date'},
				{label:'End',default:null,desc:'MM/DD/YYYY',required:false,type:'date'},
				{label:'Value',default:null,desc:'10000...',required:false,type:'number'},
				{label:'Type',default:null,desc:'choose type...',required:false,type:'select'},
				{label:'File',default:null,desc:'MM/DD/YYYY',required:false,type:'file'},
			];
			scope.custom_fields = [
				<?php $bFirst = true; foreach ($oCustomFields as $oCustomField): if ($bFirst): $bFirst = false; else:  ?>,<?php endif; ?>
				{fieldid: <?php echo $oCustomField->custom_field_id; ?>,label:'<?php echud($oCustomField->label_text); ?>',default:'<?php echud($oCustomField->default_value); ?>',desc:'<?php echud($oCustomField->description);
					?>',type:'<?php
					switch ($oCustomField->type) {
						case CustomFieldModel::TYPE_MULTILINE:
							echo 'multiline';
							break;
						case CustomFieldModel::TYPE_TEXT:
							echo 'text';
							break;
						case CustomFieldModel::TYPE_CHECKBOX:
							echo 'checkbox';
							break;
					} ?>',required:<?php
						echo ($oCustomField->required && $oCustomField->type != CustomFieldModel::TYPE_CHECKBOX)?'true':'false';
					?>,is_checkbox:<?php
						echo ($oCustomField->type == CustomFieldModel::TYPE_CHECKBOX)?'true':'false'; ?>}
				<?php endforeach; ?>
			];
			scope.add_field = function() {
				scope.custom_fields.push({value:'',value:'',type:'text',required:false,desc: '',editing:true});
			}
			scope.remove_field = function(index) {
				thisField = scope.custom_fields[index];

				$.ajax({
					url: '/customfields/remove_field',
					type: "POST",
					data: {
						'id': thisField.fieldid
					},
					dataType: "json",
					async: false,
					success: function (data) {
						console.log('success');
						if (data.success == 1) {
							scope.custom_fields.splice(index, 1);
							$.notify({title: 'Success: ', message: data.message},{ type: 'success'});
						} else {
							console.log(data.error);
							$.notify({title: 'Error: ', message: data.error},{ type: 'danger'});
						}
					},
					error: function () {
						console.log('error');
						$.notify({title: 'Error: ', message: 'Unable to remove field.'},{ type: 'danger'});
					}
				});
			}
			scope.save_field = function(index) {
				thisField = scope.custom_fields[index];

				$.ajax({
					url: '/customfields/add_field',
					type: "POST",
					data: {
						'required': thisField.required,
						'description': thisField.desc,
						'label': thisField.label,
						'type': thisField.type,
						'id': thisField.fieldid
					},
					dataType: "json",
					async: false,
					success: function (data) {
						console.log('success');
						if (data.success == 1) {
							thisField.editing=false;
							thisField.fieldid = data.id;
							if (thisField.type == 'checkbox') {
								thisField.is_checkbox = true;
							}
							$.notify({title: 'Success: ', message: data.message},{ type: 'success'});
						} else {
							console.log(data.error);
							$.notify({title: 'Error: ', message: data.error},{ type: 'danger'});
						}
					},
					error: function () {
						console.log('error');
						$.notify({title: 'Error: ', message: 'Unable to save field.'},{ type: 'danger'});
					}
				});
			}
		});
	});
</script>
<style>
	@media (max-width: 960px) {
		.field-required { padding-top: 5px; }
	}
	.field-actions a:hover {
    	cursor: pointer;
	}
</style>
