
jQuery(document).ready(function(){
	jQuery('input[name="s"]').irb_as_asearch();
});

(function($){
	$.fn.irb_as_asearch = function(options){
		var s = $.extend({
			'ajaxUrl': irb_as_globals.ajaxUrl,
			'minSearchChar': irb_as_globals.wordsLimit,
			'template': irb_as_globals.template
		}, options);
		
		var lastValue = '';
		var ajaxCall;
		var resultsContainer;
		var field = $(this);
		var form = field.closest('form');
		form.find('[type="submit"]').remove();
		
		init();
		field.on("keyup", searchRequest);
		field.bind("blur", hideSearchResultBox);
		field.bind("focus", showSearchResultBox);
		form.on("submit", submitFormCallback);
		
		function init(){
			resultsContainer = $('<div>').addClass('irb_asearch_container ' + s.template + ' ').hide().append(
				$('<ul>').addClass('irb_asearch_list')
			).append(
				$('<div>').addClass('irb_asearch_result')
			).insertAfter(field);
		}
		
		function submitFormCallback(e){
			e.preventDefault();
		}
		
		function hideSearchResultBox(e){
			e = e.currentTarget;
			var element = $(e).closest('form').find('.irb_asearch_container');
			if(element.is(':hover') === false){
				element.hide();
			}
		}
		
		function showSearchResultBox(e){
			e = e.currentTarget;
			if($(e).closest('form').find('.irb_asearch_container').find('li').length > 0)
				$(e).closest('form').find('.irb_asearch_container').show();
		}
		
		function generateResultsList(field, form, results){
			if(results.length > 5){
				form.find('.irb_asearch_container').addClass('two_rows');
			} else {
				form.find('.irb_asearch_container').removeClass('two_rows');
			}
			var position = calculatePopupPosition(field, form);
			form.find('.irb_asearch_container').css(position);
			
			$.each(results, function(i, e){
				$('<li>').addClass('irb-' + e.type).append(
					$('<a>').attr({'href': e.url}).css({'padding': ((e.image.length > 0) ? 0 : 5)}).append(
						((e.image.length > 0) ? $('<img>').attr({'src': e.image}) : '')
					).append(
						$('<span>').addClass('irb_as_details').append(
							$('<div>').addClass('irb_as_heading').html(e.title + ' <span class="irb_as_type">- (' + e.type + ')</span>')
						).append(
							$('<div>').addClass('irb_as_text').html(e.text)
						)
					)
				).appendTo(form.find('.irb_asearch_list'));
			});
		}
		
		function calculatePopupPosition(field, form){
			var position = field.position();
			var offset = field.offset();
			position.top += field.outerHeight();
			var boxWidth = form.find('.irb_asearch_container').outerWidth();
			if($(window).width() < (offset.left + boxWidth)){
				position.left -= (offset.left + boxWidth) - $(window).width() + 10;
			}
			return position;
		}
		
		function searchRequest(e){
			field = $(this);
			form = field.closest('form');
			var value = field.val();
			var len = value.length;
			form.find('.irb_asearch_container').removeClass('two_rows');
			if(s.minSearchChar >= len)
				form.find('.irb_asearch_container').hide().find('.irb_asearch_list, .irb_asearch_result').html('');
			if(len < s.minSearchChar || lastValue == value)
				return false;
			if(ajaxCall)
				ajaxCall.abort();
			
			var formUrl = form.attr('action');
			var formData = {'action': 'irb_as_search', 's': value};
			
			var position = calculatePopupPosition(field, form);
			form.find('.irb_asearch_container').css($.extend({'position': 'absolute'}, position)).show().find('.irb_asearch_result').html('Searching <strong>' + value + '</strong>...');
			form.find('.irb_asearch_list').children().remove();
			ajaxCall = $.ajax({
				url: s.ajaxUrl,
				method: 'POST',
				data: formData,
				error: function (xhr, status, error){
					if(status != 'abort')
						form.find('.irb_asearch_result').html('Unable to get results');
				},
				success: function (result){
					result = (result.irbIsValidJson()) ? JSON.parse(result) : {'status': 0, 'response': 'Invalid Error'};
					//preventing re-request if query is same as old one
					lastValue = value;
					if($.isArray(result.response.results)){
						var resultsCount = result.response.total;
						form.find('.irb_asearch_result').html('<a href="' + formUrl + ((formUrl.indexOf('?') > -1) ? '&' : '?') + 's=' + value + '">Total (' + resultsCount + ') records found. View All</a>');
						generateResultsList(field, form, result.response.results);
						
					} else {
						form.find('.irb_asearch_result').html(result.response);
					}
				}
			});
		}
		
		return this;
	}
}(jQuery));

String.prototype.irbIsValidJson = function(){
	try {
		var str = this.toString();
		JSON.parse(str);
	} catch (e) {
		return false;
	}
	return true;
}

