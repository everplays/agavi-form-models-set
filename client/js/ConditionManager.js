
(function($){
	var methods = {
		hide: function(container)
		{
			container.hide().each(function(){
				$(this).trigger('hide');
			});
			if(container[0].tagName.toLowerCase()=='fieldset')
			{
				container.find('> div.clearfix').hide().trigger('hide').each(function(){
					var el = $('#'+this.id+' input');
					el.trigger('hide');
				});
			}
		},
		show: function(container)
		{
			container.show('fast');
			if(container[0].tagName.toLowerCase()=='fieldset')
			{
				container.find('> div.clearfix').show();
			}
		},
		check: function(val, term)
		{
			switch(term.opration)
			{
				case '==':
					if(val==term.condition)
					{
						return true;
					}
					break;
				case '>':
					if(Number(val)>Number(term.condition))
					{
						return true;
					}
					break;
				case '>=':
					if(Number(val)>=Number(term.condition))
					{
						return true;
					}
					break;
				case '<':
					if(Number(val)<Number(term.condition))
					{
						return true;
					}
					break;
				case '<=':
					if(Number(val)<=Number(term.condition))
					{
						return true;
					}
					break;
				case '*':
					if(val.indexOf(term.condition)>-1)
					{
						return true;
					}
					break;
				case '^':
					if(val.indexOf(term.condition)===0)
					{
						return true;
					}
					break;
				case '$':
					if(val.indexOf(term.condition)===term.condition.length-1)
					{
						return true;
					}
					break;
				default:
					break;
			}
			return false;
		}
	};
	$.fn.ConditionManager = function(parent, term)
	{
		var currentValue;
		var dataKey = 'ConditionManager.children';
		var container = $(this);
		// don't depend on parent only because #parent may don't exist
		var $parent = $(parent);
		if(!$parent[0])
		{
			$parent = $(parent+'_container input');
			$parent.each(function()
			{
				var el = $(this);
				if(el.attr('checked')=='checked')
				{
					currentValue = el.val();
					return false;
				}
			});
		}
		else
			currentValue = $parent.val();

		// show element if default value matches the conditions
		if(methods.check(currentValue, term))
			methods.show(container);
		else
			methods.hide(container);

		$parent.change(function(){
			var $r = methods.check($(this).val(), term);
			if($r)
				methods.show(container);
			else
				methods.hide(container);
		});
		var $children = $parent.data(dataKey) || [];
		$children.push(container);
		$parent.data(dataKey, $children);
		$parent.bind('hide', function()
		{
			var $children = $(this).data(dataKey) || [], i, len=$children.length;
			for(i=0; i<len; i++)
			{
				methods.hide($children[i]);
			}
		});
	};
})(jQuery);

