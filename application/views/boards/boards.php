<div class="divider">
	<div class="divider-content">
		<div class="divider-title">
			<h6><?php echo lang('Boards'); ?></h6>
			<small>([[lazy.items.length]])</small>
		</div>
		<div class="divider-separator">
			<hr/>
		</div>
		<?php if (isset($page) && $page == 'welcome'): ?>
		<div class="divider-actions" ng-show="!lazy.empty">
			<a href="/boards" class="btn btn-default btn-sm">Manage</a>
		</div>
		<?php endif; ?>
	</div>
</div>

<div class="boards boards-grid" ng-if="lazy.items.length">
	<a ng-repeat="board in lazy.items" href="/boards/view/[[board.board_id]]" class="board board-editable">
		<div class="board-content">
			<div class="board-graphic">
				<span data-icon="board"></span>
			</div>
			<div class="board-body">
				<div class="board-name">
					<h6>[[board.name]]</h6>
				</div>
				<div class="board-meta">
					<span>[[board.count]]</span> - <span>[[board.sub_folder_count]]</span>
				</div>
			</div>
		</div>
	</a>
</div>

<div class="revelator" ng-hide="lazy.loading || lazy.done">
	<a href="#" class="btn btn-default " ng-click="lazy.loading=true;lazy.done=false;lazy.empty=false;lazyLoad()">Load More</a>
</div>
<div class="loading-container" ng-show="lazy.loading">
	<div class="loading">
		<div class="loading-animation">
			<svg class="circular" viewBox="25 25 50 50">
				<circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10"/>
			</svg>
		</div>
	</div>
</div>
<div ng-if="lazy.empty">
	<div class="empty">
		<div class="empty-content">
			<h4><?php echo lang('It looks like you\'ve just started an account.'); ?></h4>
			<p class="text-large text-light text-italic"><?php echo lang('Add a board to get set up!'); ?></p>
			<a href="/boards/add" class="btn btn-primary"><?php echo lang('Add a Board'); ?></a>
			<a href="#" class="btn btn-default" ng-click="lazy.loading=true;lazy.done=false;lazy.empty=false;lazyLoad()">Reload List</a>
		</div>
	</div>
	<div class="revelator">

	</div>
</div>

<?php if (!isset($page) || $page != 'welcome'): ?>
<div ng-if="lazy.done && !lazy.empty">
	<div class="empty">
		<div class="empty-content">
			<p class="text-light text-italic"><?php echo lang('That\'s the end of your board list.') ?></p>
		</div>
	</div>
</div>
<?php endif; ?>

<script>
	$(document).ready(function(){
		var scope = angular.element($('body')[0]).scope();
		scope.$apply(function() {
			scope.lazy = new Object;
			scope.lazy.items = new Array();
			scope.lazy.offset = 0;
			scope.lazy.count = 50;
			scope.lazy.loading = true;
			scope.lazy.empty = false;
			scope.lazy.done = false;
			scope.lazyLoad = function(){
				scope.lazy.loading = true;
				scope.lazy.empty = false;
				scope.lazy.done = false;

				var buffer = setTimeout(function(){ $('.modal').scrollTop( $('.modal').prop("scrollHeight") + $('.modal').height() ); },1);
				$.ajax({
					url: '/boards/search_boards',
					type: 'GET',
					async: true,
					data: { count: scope.lazy.count, offset: scope.lazy.offset, search_phrase: $('#search-phrase').val() },
					dataType: 'json',
					error: function(){ console.log('ajax error') },
					success: function(response){
						scope.$apply(function() {
							scope.lazy.items = scope.lazy.items.concat(response.boards);
							scope.lazy.loading = false;
							scope.lazy.offset = scope.lazy.items.length;
							if ( scope.lazy.items.length == 0 ) {
								scope.lazy.empty = true;
							}
							if ( response.boards.length != scope.lazy.count ) {
								scope.lazy.done = true;
							}
						});
					}
				});
				return false;
			};
		});
		scope.lazyLoad();

		var delay = (function(){
			var timer = 0;
			return function(callback, ms){
				clearTimeout (timer);
				timer = setTimeout(callback, ms);
			};
		})();
		
		$('#search-phrase').keyup(function() {
			delay(function(){
				$('.boards .board').remove();
				scope.lazy.items = [];
				scope.lazy.offset = 0;
				scope.lazyLoad();
			}, 500 );
		});
	});
</script>