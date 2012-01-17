
$.widget("ui.resource", $.extend({}, $.ui.autocomplete.prototype, {
	dependencies: {},
	_init: function()
	{
		if(!this.options.value)
			this.options.value = 'id';
		$.ui.autocomplete.prototype._init.apply(this, arguments);
		var main = this.elementMain;
		this.element.bind('autocompleteselect', function(e, o){
			main.val(o.item.id).trigger('change');
		});
		var self = this, i;
		if($.isPlainObject(this.options.depends))
		{
			var readonly = true;
			for(i in this.options.depends)
			{
				var p = $('#'+this.options.depends[i]).bind('change', function()
				{
					self.dependencies[$(this).attr('name')] = true;
					var i, all=1;
					if(i in self.options.depends)
					{
						all &= Boolean(self.dependencies[i]);
					}
					if(all)
					{
						self.element.removeAttr('disabled');
					}
					else
					{
						self.element.attr('disabled', 'disabled');
					}
				});
				readonly = /^[0-9]+$/.test(p.val()) && readonly;
			}
			if(!readonly)
				self.element.attr('disabled', 'disabled');
		}
	},
	_initSource: function()
	{
		var self = this;
		if($.isArray(this.options.source))
		{
			$.ui.autocomplete.prototype._initSource.apply(this, arguments);
		}
		else
		{
			this.source = function(req, add)
			{
				var params = {
					query: req.term
				};
				if($.isPlainObject(self.options.depends))
				{
					for(i in self.options.depends)
					{
						if(!params.filter)
							params.filter = {};
						params.filter[i] = $('#'+self.options.depends[i]).val();
						if(/^$/.test(params.filter[i]))
						{
							return false;
						}
					}
				}
				$.getJSON(
					this.options.source?this.options.source:Atras.resource.proxy,
					$.extend(params, self.options.params),
					function(data, status, request)
					{
						var r = [];
						if(data.success && $.isArray(data.result))
						{
							for(var i=0, len=data.result.length; i<len; i++)
							{
								r.push({label: data.result[i].label || data.result[i][self.options.label],  id: data.result[i][self.options.value]});
							}
							add(r);
						}
					}
				);
			};
		}
	},
	_create: function()
	{
		var name = this.element.attr('name');
		var id = this.element.attr('id');
		var val = this.element.val();
		var view = jQuery('#'+id+'_view');
		if(view.length>0)
		{
			this.elementMain = this.element;
			this.element = view;
		}
		else
		{
			if(!/^[0-9]+$/.test(val))
				val = '';
			this.element.attr('name', name+'_view');
			this.element.attr('id', id+'_view');
			this.elementMain = $('<input />').attr({
				name: name,
				type: 'hidden',
				id: id,
				value: val
			}).insertAfter(this.element);
		}
		$.ui.autocomplete.prototype._create.apply(this, arguments);
	}
}));

$.ui.resource.defaults = $.extend({}, $.ui.autocomplete.defaults);
