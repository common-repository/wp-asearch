var chatActive = false;
if(typeof chattingMsgsJson === 'undefined')
	chattingMsgsJson = [];

jQuery(document).ready(function(){
	jQuery('.slimScrollNav').slimScroll();
	jQuery('.chatMessagesArea').slimScroll({start: 'bottom'});
	jQuery('#irbScInstallationForm, #loginForm, #forgotPwdForm, #irbScChattingForm, #irbScAddOperatorForm, #irbSupportForm, #irb_sc_contactReplyForm').irbSubmitForms();
	jQuery('#irb_sc_settingsForm').irbSubmitForms({'callback': 'irb_sc_settingsFormCallback'});
	jQuery('#irb_sc_chattingForm').irbSubmitForms({'submitOnEnter': jQuery(this).find('textarea#chatMessage'), 'callback': 'irb_sc_chattingFormCallback'});
	jQuery('#chatFileUploader').irbAjaxUploader({
		callback: 'irb_sc_fileUploaderCallback', 
		multiple: true, 
		accept : 'jpg,png,jpeg,gif,zip', 
		displayMessage: jQuery('.chatMessagesArea'),
		appendProgressBar: jQuery('.irbScFileProgressBarContainer'),
		progressBar: jQuery('.irbScFileProgressBar')
	});
	jQuery('.updateUserOnlineStatus').irbAddAjaxToUrl('irb_sc_changeOnlineStatus');
	jQuery('.irbScDeleteUser').irbAddAjaxToUrl('irb_sc_removeUserRow');
	jQuery('.deleteChatSessionBtn').irbAddAjaxToUrl('irb_sc_deleteChatSession');
	
	irb_sc_ping();
	irb_sc_manageChatting();
});

function irb_sc_fileUploaderCallback(type, field, formData, response) {
	if(typeof response.response.files.chats === 'object'){
		jQuery.each(response.response.files.chats, function(i, e) {
			irb_sc_appendingChatMsg([e.response]);
		});
	}
}

function irb_sc_settingsFormCallback(form, status, formData, response) {
	response = response.split('|');
	var msgClass = (response[0] == 1) ? 'success' : 'error';
	jQuery(response[1]).text().irb_alertPopup(msgClass);
}

//Other functions
function irb_sc_changeOnlineStatus( btn, data ) {
	if(data == '1'){
		var newStatus = btn.find('.onlineStatus:not(.hide)').addClass('hide').data('newstatus');
		btn.find('.'+newStatus).removeClass('hide');
		var newHref = btn.attr('href').slice(0, -1) + ((newStatus == 'online') ? 0: 1);
		btn.attr('href', newHref);
	}
}

function irb_sc_dataTableLangs(){
	jQuery.extend( true, jQuery.fn.dataTable.defaults, {
		"oLanguage": {
			oAria: {
				sSortAscending: irb_globals.messages.userManagementActiveAscTableLabel,
				sSortDescending: irb_globals.messages.userManagementActiveDescTableLabel
			},
			oPaginate: {
				sFirst: irb_globals.messages.userManagementFirstTableLabel,
				sLast: irb_globals.messages.userManagementLastTableLabel,
				sNext: irb_globals.messages.userManagementNextTableLabel,
				sPrevious: irb_globals.messages.userManagementPrevTableLabel
			},
			sEmptyTable: irb_globals.messages.userManagementEmptyTableLabel,
			sInfo: irb_globals.messages.userManagementInfoTableLabel,
			sInfoEmpty: irb_globals.messages.userManagementInfoEmptyTableLabel,
			sInfoFiltered: irb_globals.messages.userManagementInfoFilteredTableLabel,
			sInfoPostFix: "",
			sDecimal: "",
			sThousands: ",",
			sLengthMenu: irb_globals.messages.userManagementPerPageLabel,
			sLoadingRecords: irb_globals.messages.userManagementLoadingTableLabel,
			sProcessing: irb_globals.messages.userManagementProcessingTableLabel,
			sSearch: irb_globals.messages.userManagementSearchLabel,
			sSearchPlaceholder: irb_globals.messages.userManagementSearchPhTableLabel,
			sUrl: irb_globals.messages.userManagementUrlTableLabel,
			sZeroRecords: irb_globals.messages.userManagementZeroRecordsTableLabel
		}
	} );	
}

function irb_sc_removeUserRow(btn, response) {
	if(response == 1)
		btn.closest('tr').slideUp();
}

function irb_sc_deleteChatSession(btn, response) {
	response = response.irbConvertResponseToJson();
	if(response.status == 1)
		btn.find('i').removeClass('fa-times-circle-o text-warning').addClass('fa-check text-success');
	response.response.message.irb_alertPopup(response.response.msgClass);
}

// dashboard supporting function
function appendLabelsToChart(data, listUl, type) {
	type = type || 'pie';
	var data = (type == 'pie') ? data : data.datasets;
	jQuery.each(data, function(i, e) {
		var label = (type == 'pie') ? e.value + ' ' + e.label : e.label;
		var color = (type == 'pie') ? e.color : e.pointColor;
		listUl.append(
			jQuery('<li>').append(
				jQuery('<i>').addClass('fa fa-circle').css('color', color)
			).append(
				jQuery('<span>').html(' &nbsp;' + label)
			)
		);
	});
}

//Pages functions
function irb_sc_dashboard_func(){
	var today = irb_globals.messages.dashboardDateToday;
	var yesterday = irb_globals.messages.dashboardDateYesterday;
	var lastSevenDays = irb_globals.messages.dashboardDateLastDays.irbFormatString('7');
	var lastThirtyDays = irb_globals.messages.dashboardDateLastDays.irbFormatString('30');
	var thisMonth = irb_globals.messages.dashboardDateMonth;
	var lastMonth = irb_globals.messages.dashboardDateLastMonth;
	var form = jQuery('#irb_sc_dashboard');
	
	var pickerRange = {};
	pickerRange[today] = [moment(), moment()],
	pickerRange[yesterday] = [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
	pickerRange[lastSevenDays] = [moment().subtract(6, 'days'), moment()],
	pickerRange[lastThirtyDays] = [moment().subtract(29, 'days'), moment()],
	pickerRange[thisMonth] = [moment().startOf('month'), moment().endOf('month')],
	pickerRange[lastMonth] = [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]

	jQuery('#updateDateRange').daterangepicker({
		format: 'YYYY-MM-DD',
		ranges: pickerRange,
		startDate: moment().subtract(30, 'days'),
		endDate: moment(),
		locale: {
            applyLabel: irb_globals.messages.dashboardDateSubmit,
            cancelLabel: irb_globals.messages.dashboardDateCancel,
            fromLabel: irb_globals.messages.dashboardDateFrom,
            toLabel: irb_globals.messages.dashboardDateTo,
            customRangeLabel: irb_globals.messages.dashboardDateCustom,
            daysOfWeek: irb_globals.messages.dashboardDateDaysOfWeek.split(' | '),
            monthNames: irb_globals.messages.dashboardDateShortMonths.split(' | '),
            firstDay: 1
        },
	},
	function (start, end) {
		jQuery('#updateDateRange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
		form.find('input[name="from"]').val(start.format('YYYY-MM-DD'));
		form.find('input[name="to"]').val(end.format('YYYY-MM-DD'));
		form.submit();
	});
	jQuery('#updateDateRange').data('daterangepicker').setStartDate(form.find('[name="from"]').val());
	jQuery('#updateDateRange').data('daterangepicker').setEndDate(form.find('[name="to"]').val());
	jQuery('#updateDateRange span').html(moment(form.find('input[name="from"]').val(), 'YYYY-MM-DD').format('MMMM D, YYYY') + ' - ' + moment(form.find('input[name="to"]').val(), 'YYYY-MM-DD').format('MMMM D, YYYY'));
	jQuery('#updateDateRange').on('apply.daterangepicker', function(ev, picker) {
		form.find('input[name="from"]').val(picker.startDate.format('YYYY-MM-DD'));
		form.find('input[name="to"]').val(picker.endDate.format('YYYY-MM-DD'));
		form.submit();
	});
	
	
	var pieOptions = {
		segmentShowStroke: true,
		segmentStrokeColor: "#fff",
		segmentStrokeWidth: 1,
		percentageInnerCutout: 50, // This is 0 for Pie charts
		animationSteps: 100,
		animationEasing: "easeOutBounce",
		animateRotate: true,
		animateScale: false,
		responsive: true,
		maintainAspectRatio: false,
		legendTemplate: "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<segments.length; i++){%><li><span style=\"background-color:<%=segments[i].fillColor%>\"></span><%if(segments[i].label){%><%=segments[i].label%><%}%></li><%}%></ul>",
		tooltipTemplate: "<%=value %> <%=label%>"
	};
	
	// Pie Charts Settings
	var canvasId = jQuery("#browserChart");
	new Chart(canvasId.get(0).getContext("2d")).Doughnut(browserData, pieOptions);
	appendLabelsToChart(browserData, canvasId.closest('.row').find('ul.chart-legend'));

	canvasId = jQuery("#osPieChart");
	new Chart(canvasId.get(0).getContext("2d")).Doughnut(osData, pieOptions);
	appendLabelsToChart(osData, canvasId.closest('.row').find('ul.chart-legend'));

	canvasId = jQuery("#localePieChart");
	new Chart(canvasId.get(0).getContext("2d")).Doughnut(localeData, pieOptions);
	appendLabelsToChart(localeData, canvasId.closest('.row').find('ul.chart-legend'));

	canvasId = jQuery("#chatSessionsChart");
	new Chart(canvasId.get(0).getContext("2d")).Doughnut(chatSessionsData, pieOptions);
	appendLabelsToChart(chatSessionsData, canvasId.closest('.row').find('ul.chart-legend'));
	
	// Graph Settings
	var graphOptions = {
		animation: true,
		animationSteps: 100,
		animationEasing: 'easeOutQuart',
		scaleShowGridLines : true,
		scaleGridLineColor : "rgba(0,0,0,.05)",
		scaleGridLineWidth : 1,
		scaleShowHorizontalLines: true,
		scaleShowVerticalLines: true,
		bezierCurve : true,
		bezierCurveTension : 0.4,
		pointDot : true,
		pointDotRadius : 4,
		pointDotStrokeWidth : 1,
		pointHitDetectionRadius : 20,
		datasetStroke : true,
		datasetStrokeWidth : 2,
		datasetFill : true,
		responsive: true,
		maintainAspectRatio: false,
		scaleLabel: "<%=value%>",
		legendTemplate : "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<datasets.length; i++){%><li><span style=\"background-color:<%=datasets[i].strokeColor%>\"></span><%if(datasets[i].label){%><%=datasets[i].label%><%}%></li><%}%></ul>"
	};
	
	canvasId = jQuery("#chatSessionGraph");
	new Chart(canvasId.get(0).getContext("2d")).Line(chatSessionsGraphData, graphOptions);
	appendLabelsToChart(chatSessionsGraphData, canvasId.closest('.row').find('ul.chart-legend'), 'graph');
	
	canvasId = jQuery("#visitorsGraph");
	new Chart(canvasId.get(0).getContext("2d")).Line(visitorsGraphData, graphOptions);
	
	// Tables settings
	var tables = ['#newVisitorsTable', '#mostVisitedPages', '#mostVisitedIpTable', '#mostRatedOperator'];
	jQuery.each(tables, function(i, table) {
		if(jQuery(table + ' tbody').find('tr td').length > 1) {
			irb_sc_dataTableLangs();
			var table = jQuery(table);
			table.dataTable({
				'iDisplayStart': 15,
				"aaSorting": [],
				'bFilter': false,
				'bSearch': false,
				'bInfo': false
			});
			table.parent().find('.dataTables_length').closest('.row').remove();
			table.parent().find('.dataTables_paginate').closest('.row').removeClass('row');
			table.parent().find('.dataTables_paginate').closest('.col-xs-6').prev().remove();
			table.parent().find('.dataTables_paginate').closest('.col-xs-6').removeClass('col-xs-6').addClass('col-xs-12');
		}
	});

}

function irb_sc_manage_users_func(){
	if(jQuery('#usersManagementTable tbody').find('tr td').length > 1) {
		irb_sc_dataTableLangs();
		jQuery('#usersManagementTable').dataTable({"aaSorting": []});
	}
}

function irb_sc_manage_visitors_func(){
	if(jQuery('#usersManagementTable tbody').find('tr td').length > 1) {
		irb_sc_dataTableLangs();
		jQuery('#usersManagementTable').dataTable({"aaSorting": []});
	}
}

function irb_sc_chat_history_func(){
	if(jQuery('#chatHistoryTable tbody').find('tr td').length > 1) {
		irb_sc_dataTableLangs();
		jQuery('#chatHistoryTable').dataTable({"aaSorting": []});
	}
}

function irb_sc_contact_msgs_func(){
	if(jQuery('#messagesTable tbody').find('tr td').length > 1) {
		irb_sc_dataTableLangs();
		jQuery('#messagesTable').dataTable({"aaSorting": []});
	}
}

function irb_sc_chatroom_func(){
	//var msgObj = jQuery('.irb_sc_selectChatMsg p');
	/*jQuery('.visitorLastVisit, .time').each(function(){
		jQuery(this).timeago().html(jQuery.timeago(jQuery(this)));
	});*/
	
	// IE8 uploader handling
	if ( navigator.userAgent.toLowerCase().indexOf( "msie" ) > -1 && parseInt(navigator.userAgent.toLowerCase().split('msie')[1]) < 9 )
		jQuery('#irb_sc_uploaderForm').remove();
	
	irb_sc_beep();
	irb_sc_getActiveChatsList();
	
}

function irb_sc_chat_func(){
	/*jQuery('.visitorLastVisit').each(function(){
		jQuery(this).timeago().html(jQuery.timeago(jQuery(this)));
	});*/
}

function irb_sc_settings_func(){
	jQuery('#irb_chatbox_bgColor').colorPicker({
		color: '#0f0'
	});
	
	// Bootstrap switch issue
	jQuery('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
		jQuery('#irb_chatbox_slideBox').bootstrapToggle('destroy').bootstrapToggle();
	});

	jQuery('.validateApi').irbAddAjaxToUrl('irb_sc_mcApiCallback', 'irb_sc_mcApiBeforeSubmitCallback');
	jQuery('#irb_enable_mailchimp_subs').change(function(){
		var btn = jQuery(this);
		if(btn.is(':checked')){
			jQuery('#irb_mailchimp_api_key, #irb_mailchimp_list_id').attr('required', true);
		} else {
			jQuery('#irb_mailchimp_api_key, #irb_mailchimp_list_id').removeAttr('required');
		}
	});
	
}

function irb_sc_contact_form_func(){
	jQuery( "#formDropableArea" ).sortable({
		revert: true
    });
    jQuery( "#formElementsContainer" ).draggable({
		connectToSortable: "#formDropableArea",
		helper: "clone",
		revert: "invalid"
    });
    //jQuery( "ul, li" ).disableSelection();
}

function irb_sc_mcApiBeforeSubmitCallback(btn) {
	btn.attr({'href': btn.data('href') + '&apiKey=' + jQuery('#irb_mailchimp_api_key').val()});
}

function irb_sc_mcApiCallback(btn, data) {
	var response = data.irbConvertResponseToJson();
	if(response.status == 1) {
		jQuery('#irb_mailchimp_list_id').html('');
		var list = JSON.parse(response.response);
		jQuery.each(list, function(i, e) {
			jQuery('<option>').attr({'value': i}).html(e).appendTo('#irb_mailchimp_list_id');
		});
		''.irb_alertPopup('success');
	} else {
		response.response.irb_alertPopup('error');
	}
}

//Chatting functions
function irb_sc_totalChatsCounter(){
	jQuery('ul.treeview-menu').each(function(i, ul) {
		var totalNew = 0;
		ul = jQuery(ul);
		totalNew = ul.find('li').length;
/*		ul.find('li').find('span').each(function(j, e) {
			var elem = jQuery(e);
			if(elem.css('display') !== 'none')
				totalNew++;
		});*/
		if(totalNew > 0) {
			ul.closest('li.treeview').children('a').find('span.label').html(totalNew).show();
		} else {
			ul.closest('li.treeview').children('a').find('span.label').html(totalNew).hide();
		}
	});
}

function irb_sc_getActiveChatsList(){
	jQuery.ajax({
		url : irb_globals.ajaxUrl,
		data : 'action=irb_sc_backendFetchActiveChatsList',
		success: function(response) {
			var response = response.irbConvertResponseToJson();
			if(response.status == 1) {
				var chatSessions = response.response;
				var chatSessionLis = [];
				jQuery.each(chatSessions, function(type, list) {
					var parentLi;
					switch(type) {
						case "active":
							parentLi = jQuery('.irb_sc_activeChats').closest('li').find('ul');
							break;
						case "queue":
							parentLi = jQuery('.irb_sc_inQueueChats').closest('li').find('ul');
							break;
						case "offline":
							parentLi = jQuery('.irb_sc_offlineChats').closest('li').find('ul');
							break;
					}
					jQuery.each(list, function(i, session) {
						if(typeof parentLi !== 'undefined'){
							var message = session.message;
							var maxChar = 30;
							if(parentLi.closest('ul').find('li[data-sessionid="' + session.session_id + '"]').length == 0) {
								var chatSessionLi = jQuery('<li>').addClass('sessionList').attr({'title': message, 'data-sessionid': session.session_id, 'data-datetimestarted': Date.parse(Date.irbConvertFromMysql(session.datetime_started)), 'data-type': type}).append(
									jQuery('<a>').attr({'href': 'javascript:void(0)'}).text((message.length > maxChar) ? message.slice(0, maxChar) + '...' : message).append(
										jQuery('<span>').addClass('label label-success pull-right').html(irb_globals.messages.chatroomNewMessageIconText)
									)
								);
								chatSessionLi.prependTo(parentLi);
								// For removing labels of new msg
								chatSessionLis.push(chatSessionLi);
							}
						}
					});
				});
				//showing total new chats
				irb_sc_totalChatsCounter();
				irb_sc_initializeChatSession();
			}
			timeOutObj['updateChatsList'] = setTimeout(function(){
				irb_sc_getActiveChatsList();
			}, irb_globals.operatorChatListPing);
			
			//Removing new labels on old chats
			setTimeout(function(){
				jQuery.each(chatSessionLis, function(i, li) {
					li.find('span').fadeOut();
				});
			}, 15000);
		}
	});
}

function irb_sc_initializeChatSession(){
	jQuery('ul.sidebar-menu li[data-sessionid][data-type]').unbind('click').on('click', function(){
		var sessionBtn = jQuery(this);
		var msgObj = jQuery('.irb_sc_selectChatMsg p');
		var operatorId = irbGetCookie(irb_globals.cookies.userCookie);
		sessionBtn.find('span').fadeOut();
		
		msgObj.html(irb_globals.messages.chatRoomConnectingMsg);
		jQuery.ajax({
			url : irb_globals.ajaxUrl,
			data : 'action=irb_sc_backendConnectSession&status=1&operator_id=' + operatorId + '&session_id=' + sessionBtn.data('sessionid'),
			method : 'POST',
			success : function(response) {
				var response = response.irbConvertResponseToJson();
				if(response.status == 1) {
					//shifting li to active one
					jQuery('.irb_sc_activeChats').closest('ul').find('.active').removeClass('active');
					
					var newActiveLi = sessionBtn.clone();
					newActiveLi.addClass('active').attr('data-type', 'active').prependTo(jQuery('.irb_sc_activeChats').closest('li').find('ul'));
					sessionBtn.remove();
					
					//Initializing and creating chat page
					msgObj.html(irb_globals.messages.chatRoomLoadingMsg);
					irb_sc_createChatroomPage(response.response);
					chatActive = true;
					
					// Settings for offline and online chat
					var closeBtn = jQuery('.irb_sc_closeChatBtn');
					closeBtn.attr('data-chatstatus', response.response.chatStatus);
					if(response.response.chatStatus == 'offline'){
						closeBtn.find('.closeChatText').hide();
						closeBtn.find('.sendMailText').show();
						jQuery('.onlineConnectionHeading').hide();
						jQuery('.offlineConnectionHeading').show();
					} else {
						closeBtn.find('.closeChatText').show();
						closeBtn.find('.sendMailText').hide();
						jQuery('.onlineConnectionHeading').show();
						jQuery('.offlineConnectionHeading').hide();
					}
					
				} else {
					if(response.response.status == 2)
						sessionBtn.remove();
					
					response = (typeof response.response.sessionData !== 'undefined') ? response.response.sessionData : ((typeof response.response.chatData !== 'undefined') ? response.response.chatData : ((typeof response.response.message !== 'undefined') ? response.response.message : irb_globals.messages.chatRoomErrorConnectingMsg));
					msgObj.html(response);
					//console.log(response);
					if(response.length > 0)
						response.irb_alertPopup('warning');
				}
			}
		});
	});
}

function irb_sc_switchChat(){
	// clearing ajax timeout for getting messages.
	if(typeof timeOutObj['getChatMessages'] !== 'undefined'){
		jQuery.each(timeOutObj['getChatMessages'], function(session_id, interval) {
			clearTimeout(interval);
			/* setTimeout(function(){
				irb_sc_getChatMsgs(session_id);
			}, irb_globals.chatInterval);
			*/
		});
	}
}

function irb_sc_closeChat(){
	jQuery('.irb_sc_closeChatBtn').unbind('click').click(function(){
		var btn = jQuery(this);
		var confirmClose = confirm(irb_globals.messages.chatroomCloseChatMessage);
		if(confirmClose === true) {
			irb_sc_closeChatActions(jQuery(this).data('sessionid'));
		}
	});
}

function irb_sc_closeChatActions(session_id, author) {
	author = author || 'operator';
	var sessionLi = jQuery('li[data-sessionid="' + session_id + '"]');
	var duration = Date.irbDiffTime(new Date(), new Date(sessionLi.data('datetimestarted')));
	var closeBtn = jQuery('.irb_sc_closeChatBtn');
	var chatStatus = closeBtn.data('chatstatus');
	closeBtn.attr('disabled', 'disabled');
	var textarea = jQuery('textarea#chatMessage');
	textarea.attr('disabled', 'disabled');
	
	jQuery.ajax({
		url: irb_globals.ajaxUrl,
		method: 'POST',
		data: 'action=irb_sc_closeSession&status=2&from=operator&chatStatus=' + chatStatus + '&author=' + author + '&session_id=' + session_id.irbEncodeString() + '&duration=' + duration,
		success: function(response) {
			response = response.irbConvertResponseToJson();
			closeBtn.removeAttr('disabled');
			textarea.removeAttr('disabled');
			
			if(response.status == 1) {
				sessionLi.remove();
				irb_sc_totalChatsCounter();
				var msgObj = jQuery('.irb_sc_selectChatMsg p');
				msgObj.html(irb_globals.messages.chatRoomSelectChatSession).parent().show();
				// clearing ajax timeout for getting messages.
				if(typeof timeOutObj['getChatMessages'] !== 'undefined' && typeof timeOutObj['getChatMessages'][session_id] !== 'undefined')
					clearTimeout(timeOutObj['getChatMessages'][session_id]);
			}
			var msgClass = (typeof response.response.msgClass == 'undefined') ? '' : response.response.msgClass;
			response.response.message.irb_alertPopup(msgClass);
		}
	});
}

function irb_sc_createChatroomPage(args) {
	var msgObj = jQuery('.irb_sc_selectChatMsg p');
	var chatRoomContainer = jQuery('.irbAdminChatroom');
	var visitorDetailsArea = jQuery('.visitorDetails');
	var chatForm = jQuery('#irb_sc_chattingForm');
	var sessionData = args.sessionData;
	var chatData = args.chatData.response;
		
	msgObj.parent().hide();
	sessionData = sessionData[0];
	var browserDetails = JSON.parse(sessionData.browser);
	var details = jQuery.extend(sessionData, browserDetails);
	if(typeof args.gravatar == 'undefined')
		args.gravatar = [];
	chatRoomContainer.data('gravatar', JSON.stringify(args.gravatar));
	
	// Checking and managing switched chats
	if(typeof timeOutObj['getChatMessages'] !== 'undefined'){
		//timeOutObj['getChatMessages'][details.session_id] = 0;
		irb_sc_switchChat();
	}
	
	var dateStarted = Date.irbConvertFromMysql(details.datetimeStarted);
	startTime = dateStarted.getTime();
	jQuery('.irb_sc_chattime').irb_chatTimeElapsed(startTime, true);
	
	jQuery('.connectedWith').html((details.name.length > 0) ? details.name : '');
	jQuery('.irb_sc_closeChatBtn').data({'sessionid': details.session_id, 'datetimeStarted': Date.parse(dateStarted)});
	chatForm.find('[name="session_id"]').val(details.session_id);
	jQuery.each(details, function(element, value) {
		var elementClass = '.visitor' + element[0].toUpperCase() + element.slice(1, element.length);
		if(jQuery(elementClass).length > 0) {
			if(!jQuery.isNumeric(value) && value.indexOf(':') !== -1) {
				var timeTest = value.split(' ');
				if(new RegExp(/^\d{2,}:\d{2}:\d{2}$/).test(timeTest[1]) === true) {
					value = new Date(value.split(' ').join('T')).format('l, M j, Y H:m:s A');
				}
			}
			jQuery(elementClass).html(value);
		}
	});
	
	// Initializing chats
	jQuery('.chatMessagesArea').html('');
	irb_sc_clearChatJson();
	irb_sc_appendingChatMsg(chatData);
	//irb_sc_manageChatting();
	if(args.chatStatus == 'online'){
		irb_sc_getChatMsgs();
		irb_sc_typingIndicator();
	}
	irb_sc_closeChat();
}

function irb_sc_typingIndicator(){
	var typingCounter = 0;
	var value = -1;
	var session_id = jQuery('.irb_sc_closeChatBtn').data('sessionid');
	jQuery('#irb_sc_chattingForm').find('#chatMessage').on('keyup', function(){
		var field = jQuery(this);
		if(field.val().length > 10 && typingCounter == 0) {
			value = 1;
			typingCounter = 2;
		} else if(field.val().length == 0 && typingCounter == 2) {
			value = 0;
		}
		
		if(typingCounter == 0 || value == -1) {
			return;
		}
		
		var typingValue = value;
		if(value == 1)
			typingCounter = 2;
		if(value == 0)
			typingCounter = 0;
		value = -1;

		jQuery.ajax({
			url: irb_globals.ajaxUrl,
			method: 'POST',
			data: 'action=irb_sc_typingIndicator&status=5&author=operator&value=' + typingValue + '&session_id=' + session_id,
			success: function(response) {
				response = response.irbConvertResponseToJson();
				if(response.status == 1) {
					//success on submitting typing status
				}
			}
		});
	});
}

function irb_sc_chattingFormCallback(form, status, formData, response) {
	var chatBox = form.closest('.irb_sc_chatBox');
	var chatSessionMsgs = chatBox.find('.irbChatSessionMessages');
	var chatSessionRoom = chatBox.find('.irbChatSessionBox');
	var chattingRoom = chatBox.find('.irbChatRoomMessages');
	var chatMessagesArea = chattingRoom.find('.chatMessagesArea');
	var chattingForm = chatBox.find('#irb_sc_chattingForm');
	if(status === true) {
		form.find('textarea#chatMessage').val('');
		irb_sc_appendingChatMsg([response.response]);
		// To set typing indicator to null
		jQuery('#irb_sc_chattingForm').find('#chatMessage').keyup();
	}
}

function irb_sc_addChatToJson(newChatJson) {
	/*if(jQuery.isArray(newChatJson)){
		chattingMsgsJson = jQuery.extend(newChatJson, chattingMsgsJson);
	} else { */
		if(typeof irb_sc_getLastChat() === 'undefined' || newChatJson.chat_id > irb_sc_getLastChat().chat_id) {
			chattingMsgsJson.push(newChatJson);
			return true;
		}
		return false;
	/*} */
}

function irb_sc_clearChatJson(){
	chattingMsgsJson = [];
}

function irb_sc_getLastChat(){
	return chattingMsgsJson[chattingMsgsJson.length -1];
}

function irb_sc_manageChatting(){
	if(chattingMsgsJson.length == 0)
		return;
	var chatMessagesArea = jQuery('.irb_sc_chatBox').find('.chatMessagesArea');
	irb_sc_appendingChatMsg(chattingMsgsJson, true);
}

function irb_sc_getChatMsgs(session_id) {
	session_id = session_id || false;
	var chatRoom = jQuery('.irbChatRoomMessages');
	var msgObj = jQuery('.irb_sc_selectChatMsg p');
	var chatMessagesArea = chatRoom.find('.chatMessagesArea');
	var lastChatId = irb_sc_getLastChat().chat_id;
	session_id = (session_id === false) ?  jQuery('.irb_sc_closeChatBtn').data('sessionid') : session_id;
	if(chatMessagesArea.is(':visible') === true && typeof lastChatId !== 'undefined'){
		jQuery.ajax({
			url : irb_globals.ajaxUrl + '?action=irb_sc_getNewMessage' + '&author=operator&session_id=' + session_id + '&lc=' + lastChatId.irbEncodeString(),
			method: 'GET',
			success: function(response) {
				response = JSON.parse(response);
				if(response.status == 1) {
					var chats = response.response.chat;
					var session = response.response.session[0];
					if(chats.length > 0) {
						var beepPlayCheck = false;
						jQuery.each(chats, function(i, e) { if(e.author == 'visitor'){ beepPlayCheck = true; } });
						if(beepPlayCheck == true) {
							irb_sc_beep(true);
						}
						jQuery.titleAlert(irb_globals.messages.chatNewMessageTitleAlert.irbFormatString(chats.length), {
							requireBlur:true,
							stopOnFocus:true,
							duration:(10*60*1000),
							interval:800
						});
						var activeChatSpan = jQuery('li[data-sessionid="' + session_id + '"]').find('span')
						activeChatSpan.html(chats.length).show();
						setTimeout(function(){
							activeChatSpan.fadeOut();
						}, 9000);
						
						irb_sc_appendingChatMsg(chats);
					}
					
					//typing indicator
					if(session.visitor_indicator == 1) {
						chatRoom.find('.irbScVisitorIndicator').show();
					} else {
						chatRoom.find('.irbScVisitorIndicator').hide();
					}
					if(session.status == 2) {
						irb_sc_closeChatActions(session_id, 'visitor');
						msgObj.html(session.message);
					}
				}
			}
		});
	}
	if(typeof timeOutObj.getChatMessages == 'undefined')
		timeOutObj['getChatMessages'] = {};
	timeOutObj['getChatMessages'][session_id] = setTimeout(function(){
		irb_sc_getChatMsgs(session_id);
	}, irb_globals.chatInterval);
}

function irb_sc_appendingChatMsg(chatDataArr, disableScroll) {
	disableScroll = disableScroll || true;
	if(chatDataArr.length == 0)
		return;
	var chatMessagesArea = jQuery('.irbChatRoomMessages').find('.chatMessagesArea');
	var lastElement;
	jQuery.each(chatDataArr, function(i, j) {
		if(typeof j !== 'undefined'){
			var chatAdded = irb_sc_addChatToJson(j);
			if(chatAdded === true) {
				var chatTemplate = irb_sc_addNewChatMsgTemplate(j);
				chatMessagesArea.append(chatTemplate);
				//TODO
				//jQuery(chatTemplate).find('.time').timeago().html(jQuery.timeago(jQuery(chatTemplate).find('.time')));
				lastElement = chatTemplate;
			}
		}
	});
	if(disableScroll === true && typeof lastElement !== 'undefined')
		lastElement.irbScrollToElement();
}

function irb_sc_addNewChatMsgTemplate(chatData) {
	if(chatData.length == 0)
		return;
	var authorName = (chatData.author == 'visitor') ? ((jQuery('.connectedWith').text().length > 0) ? jQuery('.connectedWith').text() : irb_globals.messages.chatroomVisitorTitle) : ((chatData.author == 'operator') ? irb_globals.messages.chatroomYouTitle : chatData.author);
	var chatTime = (typeof chatData.datetime === 'undefined') ? new Date() : Date.irbConvertFromMysql(chatData.datetime);
	var template = jQuery('.irbScchattingTemplate.hide:first').clone();
	template.removeClass('irbScchattingTemplate hide').addClass(chatData.author);
	var gravatars = JSON.parse(jQuery('.irbAdminChatroom').data('gravatar'));
	
	if(typeof gravatars[chatData.author] != 'undefined')
		template.find('.authorImage').removeClass('hide').find('img').attr('src', gravatars[chatData.author]);
	template.find('.authorName').text(authorName);
	template.find('.chatText').html(chatData.message.replace(/\n/g, '<br />'));
	template.find('.time').text(chatTime.irbConvertToMysql());
	return template;
}

