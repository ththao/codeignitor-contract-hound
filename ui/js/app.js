// applies tooltips automatically
$(document).ready(function(){
	$('[data-toggle=tooltip]').tooltip({container: 'body', html: true});
});


// scroll to bottom if attribute is set
$(document).ready(function(){
	$('[data-jquery="scroll-bottom"]').each(function(){
		$(this).scrollTop($(this)[0].scrollHeight);
	});
});

function scroll_convo(convo){
	var conversation = $(convo);
	var height = conversation[0].scrollHeight;
	conversation.animate({scrollTop:height},500);
	conversation.scrollTop(height);
	console.log('scrolling');
}
function activate_datepicker(element,action){
	var date_format = locale_date_format;
	//var chars = {'d':'dd','m':'mm','Y':'yy'};
	//date_format = date_format.replace(/[dmY]/g, m => chars[m]);
	date_format = date_format.replace('d', 'dd');
	date_format = date_format.replace('m', 'mm');
	date_format = date_format.replace('Y', 'yy');
	
	element.datepicker({
		beforeShow: function (input,inst) {
			var timer = setTimeout( function(){ $('#ui-datepicker-div').addClass('ui-widget-active'); } , 1 );
			//element.datepicker($.datepicker.regional[userLocale ]);
		},
		onClose: function (input,inst) {
			$('#ui-datepicker-div').removeClass('ui-widget-active');
		},
		dateFormat:date_format
	});
	element.datepicker('show');
}

// datepicker setup
$(document).on('focus','.form-control[data-jquery=datepicker]', function(){
	activate_datepicker($(this));
});
$(document).on('click','a[data-jquery=datepicker]', function(){
	var target = $( $(this).attr('data-target') );
	activate_datepicker(target,'show');
});


// use double square brackets for angular
angular.module('ContractHoundApp', []).config(function ($interpolateProvider) {
	$interpolateProvider.startSymbol('[[').endSymbol(']]');
});

// watch for change of input value for restyled file inputs
$(document).on('change','input[type="file"]',function(){
	var split_file = escape($($(this)).val().split(/\\/).pop());
	$(this).closest('.btn').attr('data-value',((split_file)?': '+((split_file)?split_file:$(input).val()):''));
});


// track the value of a select — to style text if default
$(document).on('change','select',function(){
	$(this).attr('data-value',$(this).val());
});


// add helper classes
$(document).on('ready',function(){ $('html').addClass('ready') });
$(window).on('load',function(){ $('html').addClass('loaded') });


// start the notifier, and show any notifications that are hard-coded
$.notifyDefaults({
	allow_dismiss: true,
	placement: {
		from: 'top',
		align: 'center'
	},
	spacing: 10,
	z_index: 1060,
	offset: {
		x: 0,
		y: 58
	},
	animate: {
		enter: 'animated fadeIn',
		exit: 'animated fadeOut',
	}
});
$(document).ready(function(){
	var current_notifications = window.notifications;
	if ( current_notifications && current_notifications.length ) {
		for ( var i = 0 ; i < current_notifications.length ; i++ ) {
			$.notify( current_notifications[i][0] , current_notifications[i][1] );
		}
	}
});