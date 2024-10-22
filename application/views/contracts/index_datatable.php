<!DOCTYPE html>
<html dir="ltr" lang="en-US">
	<!-- application layout -->
	<head>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8">
		<title>Contracts | Contract Hound</title>

		<link rel="shortcut icon" href="/ui/img/logos/contracthound-favicon.png" />
		<meta name="viewport" content="width=device-width, maximum-scale=1.0, minimal-ui" />

		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
		<script src="/ui/jqueryui/jquery-ui-1.11.4/jquery-ui.min.js"></script>
		<script src="/ui/modernizr/modernizr.js"></script>
		<script src="/ui/bootstrap/js/bootstrap.min.js"></script>
		<script src="/ui/suggest/js/bootstrap-suggest.js"></script>
		<script src="/ui/dropzone/dropzone.js"></script>
		<script src="/ui/tokenfield/dist/bootstrap-tokenfield.min.js"></script>
		<script src="/ui/bootstrap-notify/bootstrap-notify.min.js"></script>
		<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.4.5/angular.min.js"></script>
		<script src="//cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js"></script>
		<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
		<script src="//d2wy8f7a9ursnm.cloudfront.net/v7/bugsnag.min.js"></script>
		<script>Bugsnag.start({apiKey: '<?= $_ENV['BUGSNAG_API_KEY'] ?>', releaseStage: '<?= ENVIRONMENT ?>'});</script>
		<script src="/ui/js/app.js"></script>

		<link rel="stylesheet" type="text/css" href="/ui/jqueryui/jquery-ui-1.11.4/jquery-ui.structure.min.css" />
		<link rel="stylesheet" type="text/css" href="/ui/suggest/css/bootstrap-suggest.css" />
		<link rel="stylesheet" type="text/css" href="/ui/tokenfield/dist/css/bootstrap-tokenfield.min.css" />

		<link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.22/css/jquery.dataTables.min.css" />
		<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />
		<link rel="stylesheet" type="text/css" href="/ui/css/app.css" />
		
		<style>
		    .column-item {
		      padding: 3px 10px;
		      text-transform: uppercase;
		      color: #909fae;
		    }
    		tr.success th, tr.success td {
    		  background-color: #e7f6e9;
    		}
            #contract-table_wrapper {
              margin-right: 15px;
              margin-left: 15px;
            }
            #contract-table {
              table-layout: fixed !important;
            }
    		#contract-table thead th {
    		  background-position: top right;
    		  padding: 0;
    		  border: 1px solid #ddd;
    		  border-right: none;
    		  padding: 0.5rem;
    		  color: #909fae;
    		  font-size: 12px;
    		  word-wrap:break-word;
    		  overflow: hidden;
    		}
    		#contract-table tr td {
    		  word-wrap: break-word;
              white-space: -moz-pre-wrap;
              white-space: pre-wrap;
              cursor: pointer;
    		}
    		#contract-table thead th span.column-header {
              text-transform: uppercase;
    		}
    		#contract-table thead th:last-child {
    		  border-right: 1px solid #ddd;
    		}
    		#contract-table thead th .select2.select2-container {
    		  border: 1px solid #ddd;
    		  height: 32px;
    		  border-radius: 3px;
              margin-top: 5px;
              max-width: 100%;
    		}
    		#contract-table thead th .select2-container--default .select2-selection--single {
    		  border: none !important;
    		  color: #909fae !important;
    		}
    		#contract-table thead th .select2-selection__rendered {
    		  color: #909fae !important;
    		}
    		#contract-table thead th .select2:focus.select2-container {
    		  box-shadow: 0 0 0 .2rem rgba(0,123,255,.25);
    		}
    		#contract-table thead th.sorting .datatable-search {
    		  background: #fff;
    		  border: 1px solid #ddd;
              font-size: 12px;
              margin-top: 5px;
    		}
		</style>
	</head>
	<body data-ng-app="ContractHoundApp">
		<div class="modal fade" id="browse-contracts">
			<div class="modal-container modal-container-large">
				<div class="modal-dialog">

					<div class="modal-content" ng-hide="upload_step">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span data-icon="close">Close</span></button>
							<div class="modal-header-actions">
								<a full href="/contracts/upload" class="btn btn-primary"><span data-icon="add-small"></span> <span>Add Contracts<span></a>
								<?php if (can_access_feature('new_upload_version',$iCurrentlyLoggedInMemberId)): ?>
								<a full href="/contracts/bulk" class="btn btn-primary"><span>Bulk Upload<span></a>
								<?php endif; ?>
								<a full href="/export/export" class="btn btn-primary btn-export"><span>Export<span></a>
								<a mobile href="/contracts/upload" class="btn btn-primary btn-lg"><span data-icon="add"></span></a>
								<a mobile href="/contracts/upload" class="btn btn-primary btn-lg"><span data-icon="contract"></span></a>
							</div>
							<h2 class="modal-title">Browse Contracts</h2>
							<p>These are all the contracts you manage in Contract Hound.</p>
							<div class="modal-header-form hide">
								<input type="text" class="form-control input-rounded input-lg" id="search-phrase" placeholder="Search Contracts..." />
							</div>
						</div>
						<div class="modal-body">
							<?php if ($contractCount): ?>
    							<div class="divider">
    								<div class="divider-content">
    									<div class="divider-title" style="vertical-align: middle;">
    										<h6>Contracts</h6>
    									</div>
    									<div class="divider-separator">
    										<hr/>
    									</div>
    									<?php if (isset($oCustomFields) && $oCustomFields->count): ?>
    									<div class="divider-actions">
    										<div class="btn-group pull-right">
                        						<div class="dropdown">
                        							<a href="#" class="btn btn-default" data-toggle="dropdown">
                        								<span>Columns</span>
                        								<i class="caret"></i>
                        							</a>
                        							<ul class="dropdown-menu dropdown-menu-right dropdown-menu-large">
                        							<?php $colInd = 7; ?>
                        							<?php foreach ($oCustomFields as $ind => $oCustomField): ?>
                        								<li class="column-item">
                            								<label>
                                								<input type="checkbox" class="column-checkbox" <?php echo $ind <= 1 ? 'checked' : ''; ?> value="<?php echo $colInd; ?>">&nbsp;
                                								<?php echo str_replace("\n",'<br/>',word_wrap(retud($oCustomField->label_text),35,'<br/>',true)); ?>
                            								</label>
                        								</li>
                        								<?php $colInd ++; ?>
                        							<?php endforeach; ?>
                        							</ul>
                        						</div>
                        					</div>
    									</div>
    									<?php endif; ?>
    								</div>
    							</div>
    							
    							<div class="table-responsive table-alignment">
    								<table class="display nowrap dataTable dtr-inline collapsed" id="contract-table" width="100%">
    									<thead>
    										<tr>
    											<th style="background: none;">
        											<span class="column-header">NAME</span><br/>
        											<input class="form-control datatable-search" type="text" placeholder="Search Contracts" />
    											</th>
    											<th style="background: none;">
                            						<span class="column-header">Vendor</span><br/>
    												<select class="form-control datatable-search vendor-filter">
        												<option value="">Select</option>
    												</select>
    											</th>
    											<th>
        											<span class="column-header">AMOUNT</span><br/>
    												<input class="form-control" type="text" style="visibility: hidden;"/>
    											</th>
    											<th style="background: none;">
                            						<span class="column-header">Type</span><br/>
    												<select class="form-control datatable-search">
    													<option value="">All Types</option>
    													<option value="<?php tle('buy-side'); ?>"><?php tle('buy-side'); ?></option>
    													<option value="<?php tle('sell-side'); ?>"><?php tle('sell-side'); ?></option>
    												</select>
    											</th>
    											<th>
        											<span class="column-header">START</span><br/>
        											<input class="form-control" type="text" style="visibility: hidden;"/>
    											</th>
    											<th>
        											<span class="column-header">END</span><br/>
    												<input class="form-control" type="text" style="visibility: hidden;"/>
    											</th>
    											<th style="background: none;">
                            						<span class="column-header">Owner</span><br/>
        											<select class="form-control datatable-search owner-filter">
        												<option value="">Select</option>
        											</select>
    											</th>
    											<?php if (isset($oCustomFields) && $oCustomFields->count): ?>
    											<?php foreach ($oCustomFields as $oCustomField): ?>
    											<th style="background: none;">
                            						<span class="column-header" title="<?php echo $oCustomField->label_text; ?>"><?php echo str_replace("\n",'<br/>',word_wrap(retud($oCustomField->label_text),15,'<br/>',true)); ?></span>
        											<input class="form-control datatable-search" type="text" placeholder="Search" />
    											</th>
    											<?php endforeach; ?>
    											<?php endif; ?>
    											<th class="cell-small" width="5%"></th>
    										</tr>
    									</thead>
    									<tbody id="contract-rows">
    									</tbody>
    								</table>
    							</div>
							<?php else: ?>
    							<div class="no-contracts-found">
    								<div class="empty">
    									<div class="empty-content">
    										<p class="text-large text-light text-italic">Add a Contract to get set up! Your most recently active contracts will appear here. </p>
    										<a href="/contracts/upload" class="btn btn-primary">Add a Contract</a>
    									</div>
    								</div>
    								<div class="revelator">
    
    								</div>
    							</div>
							<?php endif; ?>
							<div class="no-more-contracts" style="display: none;">
								<div class="empty">
									<div class="empty-content">
										<p class="text-light text-italic">That's the end of your contract list.</p>
									</div>
								</div>
							</div>
						</div>
					</div>

				</div>
			</div>
		</div>

		<script>
			$('#browse-contracts').modal('show');
			$('#browse-contracts').on('hide.bs.modal', function (e) {
				window.location.href = "<?php echo site_url('welcome'); ?>";
			});
			var datatable = null;

			$(document).ready(function() {
				$('.btn-export').click(function(e) {
					e.preventDefault();
					var rows = datatable.rows({search:'applied'}).nodes();
					var ids = '';
					
					for (var i = 0; i < rows.length; i++) {
						var row = rows[i];
						var attributes = row.attributes;
						
						if (typeof attributes.contract_id !== 'undefined') {
							ids += (ids ? ',' : '') + parseInt(attributes.contract_id.nodeValue);
						} 
					}
					
					if (ids != '') {
						window.location.href = $(this).attr('href') + "?ids=" + window.btoa(ids);
					}
				});
				
				$(document).on('click', '#contract-table tr td', function() {
					if ($(this).find('a').length == 0) {
						var contract_id = $(this).parents('tr').attr('contract_id');
						if ($.isNumeric(contract_id) && contract_id > 0) {
    						var url = "<?php echo site_url("contracts/view"); ?>/" + contract_id;
    						window.location = url;
						}
					}
				});
				
				$(document).on('click', '.column-checkbox', function(e) {
					e.stopPropagation();
					
					var column = datatable.column($(this).val());
 
    				// Toggle the visibility
    				column.visible(!column.visible());
				});
				
				$(document).on('click', '.column-item', function(e) {
					e.preventDefault();
					e.stopPropagation();
					
					$(this).find('.column-checkbox').trigger('click');
				});
				
				var columns = [
                    {data: 'name', name: 'name'},
                    {data: 'vendor', name: 'vendor'},
                    {data: 'amount', name: 'amount'},
                    {data: 'type', name: 'type'},
                    {data: 'start', name: 'start'},
                    {data: 'end', name: 'end'},
                    {data: 'owner_name', name: 'owner_name'}
                ];
                var columnDefs = [
					{ asSorting: [ "asc", "desc" ], aTargets: [ 0 ] },
					{ asSorting: [ "asc", "desc" ], aTargets: [ 1 ] },
					{ asSorting: [ "asc", "desc" ], aTargets: [ 2 ] },
					{ asSorting: [ "asc", "desc" ], aTargets: [ 3 ] },
					{ asSorting: [ "asc", "desc" ], aTargets: [ 4 ], type: "date" },
					{ asSorting: [ "asc", "desc" ], aTargets: [ 5 ], type: "date" },
					{ asSorting: [ "asc", "desc" ], aTargets: [ 6 ] }
                ];
                
                var colIndex = 7;
                <?php if (isset($oCustomFields) && $oCustomFields->count): ?>
				<?php foreach ($oCustomFields as $ind => $oCustomField): ?>
					columns.push({data: '<?php echo $oCustomField->label_field; ?>', name: '<?php echo $oCustomField->label_field; ?>', visible: <?php echo $ind > 1 ? 'false' : 'true'; ?>});
					columnDefs.push({ asSorting: [ "asc", "desc" ], aTargets: [ colIndex ] });
					colIndex ++;
				<?php endforeach; ?>
				<?php endif; ?>
				
				columns.push({data: 'owner_avatar', name: 'owner_avatar'});
				columnDefs.push({ asSorting: [ ], aTargets: [ colIndex ] });
				
				datatable = $('#contract-table').DataTable({
                    "ajax": {
                        url : "<?php echo site_url("contracts/search_contracts") . '?count=' . $contractCount; ?>",
                        type : 'GET'
                    },
                    info: true,
                    searching: true,
					ordering: true,
					paging: false,
					order: [],
					bAutoWidth: false,
					columns: columns,
					aoColumnDefs: columnDefs,
                    createdRow: function( row, data, dataIndex ) {
                        $(row).attr('contract_id', data.contract_id );
                        // Set the data-status attribute, and add a class
                        if (data.is_new) {
                            $(row).addClass('success');
                        }
                        if ($('.vendor-filter').find('option[value="' + data.vendor_filter + '"]').length == 0) {
                        	$('.vendor-filter').append('<option value="' + data.vendor_filter + '">' + data.vendor_filter + '</option>');
                        }
                        if ($('.owner-filter').find('option[value="' + data.owner_filter + '"]').length == 0) {
                        	$('.owner-filter').append('<option value="' + data.owner_filter + '">' + data.owner_filter + '</option>');
                        }
                    },
					initComplete: function () {
						$('.dataTables_filter').hide();
						$('select.datatable-search').select2({
							sorter: data => data.sort((a, b) => a.text != 'Select' && b.text != 'Select' && a.text.localeCompare(b.text))
						});
						
                        // Apply the search
                        this.api().columns().every( function () {
                            var that = this;
             
                            $( 'input, select', that.header() ).on( 'keyup change clear', function () {
                                if ( that.search() !== this.value ) {
                                	that.search( this.value ).draw();
                                }
                            });
             
                            $( 'input, select, .select2', that.header() ).on( 'click', function (e) {
                                e.stopPropagation();
                                this.focus();
                            });
                        });
                    }
                });
			});
		</script>

		<?php $this->load->view('layout/analytics_snippets', ['intercom' => 1, 'google' => 1]); ?>

	</body>
</html>
