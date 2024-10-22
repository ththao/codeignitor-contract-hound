<div class="layout-panel">
	<div class="layout-panel-body">
		<div class="layout-panel-main">
			<div class="layout-section">
				<div class="divider divider-gap">
					<div class="divider-content">
						<div class="divider-title">
							<h6>Upload a CSV with your requested contract changes.</h6>
						</div>
						<div class="divider-separator">
							<hr/>
						</div>
					</div>
				</div>

				<div class="form-grid">
					<form action="/import" method="post" enctype="multipart/form-data">
					<table>
						<tr>
							<td class="form-label"><label>Contract Updates</label></td>
							<td colspan="3" class="form-response">
								<input class="form-control" type="file" name="update_file" />
							</td>
						</tr>
						<tr>
							<td colspan="4"><input type="submit" class="btn btn-primary" value="Save Changes"></td>
						</tr>
					</table>
					</form>
				</div>

				<?php if (!empty($aErrors)):
					foreach ($aErrors as $iRow=>$aRowErrors): ?>
				<?php endforeach; endif; ?>
			</div>
		</div>
	</div>
</div>