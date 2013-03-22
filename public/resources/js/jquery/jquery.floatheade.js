/*
	jQuery floating header plugin v1.1.0
	Licenced under the MIT License	
	Copyright (c) 2009 
		Erik Bystrom <erik.bystrom@gmail.com>
		Elias Bergqvist <elias@basilisk.se>		

	Contributors:
		Diego Arbelaez <diegoarbelaez@gmail.com>
		Glen Gilbert
		Vasilianskiy Sergey
		William Shostak (http://www.shostak.org/)
*/ 
(function($){
	/**
	 * Clone the table header floating and binds its to the browser scrolling
	 * so that it will be displayed when the original table header is out of sight.
	 *
	 * @param config
	 *		An optional dictionary with configuration for the plugin.
	 *		
	 *		fadeOut		The length of the fade out animation in ms. Default: 250
	 *		faceIn		The length of the face in animation in ms. Default: 250
	 *		floatClass	The class of the div that contains the floating header. The style should
	 *					contain an appropriate z-index value. Default: 'floatHeader'
	 *		cbFadeOut	A callback that is called when the floating header should be faded out.
	 *					The method is called with the wrapped header as argument.
	 *		cbFadeIn	A callback that is called when the floating header should be faded in.
	 *					The method is called with the wrapped header as argument.
<<<<<<< .mine
	 *					Default: true
=======
>>>>>>> .r12
	 *
	 * @version 1.1.0
	 * @see http://slackers.se/2009/jquery-floating-header-plugin
	 */
	$.fn.floatHeader = function(config) {
		config = $.extend({
			fadeOut: 250,
			faceIn: 250,
			floatClass: 'floatHeader'
		}, config);	
		
		return this.each(function () {
			var self = $(this);
			var table = self.clone();
			table.empty();
			
			// create the floating container
			self.floatBox = $('<div class="'+config.floatClass+'"style="display:none"></div>');
			self.floatBox.append(table);
			self.floatBoxVisible = false;
			
			// Fix for the IE resize handling
			self.IEWindowWidth = document.documentElement.clientWidth;
			self.IEWindowHeight = document.documentElement.clientHeight;
			
			// create the table header
			createHeader(table, self, config);
						
			// bind to the scroll event
			$(window).scroll(function() {
				var headerOutsideScreen = isHeaderOutsideScreen(self);
				if (self.floatBoxVisible && !headerOutsideScreen) {		
					// hide the floatbox			
					var offset = self.offset();
					self.floatBox.css('position', 'absolute');
					self.floatBox.css('top', offset.top);
					self.floatBox.css('left', offset.left);					
					
					self.floatBoxVisible = false;
					if (config.cbFadeOut) {
						config.cbFadeOut(self.floatBox);
					} else {
						self.floatBox.fadeOut(config.fadeOut);
					}					
				} else if (headerOutsideScreen) {								
					self.floatBoxVisible = true;

					// show the table header
					if (config.cbFadeIn) {
						config.cbFadeIn(self.floatBox);
					} else {
						self.floatBox.fadeIn(config.faceIn);
					}
				}
				
				// if the box is visible update the position
				if (self.floatBoxVisible) {
					if ($.browser.msie && $.browser.version == "6.0") {
						// IE6 can't handle fixed positioning; has to use absolute and additional calculation to position correctly.
						self.floatBox.css({
							'position': 'absolute',
							'top': $(window).scrollTop(),
							'left':  self.offset().left
						}); 
					} else {
						self.floatBox.css({
							'position': 'fixed',
							'top': 0,
							'left': self.offset().left-$(window).scrollLeft()
						});
					}											
				}
			});
			
			/*
			 * Unfortunately IE gets rather stroppy with the non-IE version,
			 * constantly resizing, thus cooking your CPU with 100% usage whilst
			 * the browser crashes. So, test for IE and add additional code.
			 */
			if ($.browser.msie && $.browser.version <= 7) {
				$(window).resize(function() {
					// check if the window size has changed
					if (self.IEWindowWidth != document.documentElement.clientWidth || 
						self.IEWindowHeight != document.documentElement.clientHeight) {
						// update the client width and height with the Microsoft version.
						self.IEWindowWidth = document.documentElement.clientWidth;
						self.IEWindowHeight = document.documentElement.clientHeight;
						table.empty();
						createHeader(table, self, config);
					}
				});
			} else {
				// bind to the resize event
				$(window).resize(function() {
						// recreate the table header
						table.empty();
						createHeader(table, self, config);
					});
			}			

			// append the floatBox to the dom
            $(self).after(self.floatBox);		
            
            if ($.browser.safari) {
	            // fix for Safari
	            $(window).one('scroll', function() {
		            table.empty();
		            createHeader(table, self, config);
		        });
            }
		});
	};
	
	/**
	 * Copies the template table and inserts each element into target.
	 */
	function createHeader(target, template, config) {
		target.width(template.outerWidth());
		
		if (template.children('thead').length === 0) {
			// the table contains no header
			return;
		}		
		var items = template.children('thead').eq(0).children();
		// iterate though each row that should be floating
		items.each(function() {
			var row = $(this);
			var floatRow = row.clone();
			floatRow.empty();

			// adjust the column width for each header cell
			row.children().each(function() {
				var cell = $(this);
				var floatCell = cell.clone();
		
				if ($.browser.msie) {
					floatCell.css('width', cell.outerWidth());
					floatCell.css('padding', '0 0 0 0');
				} else {
					floatCell.css('width', cell.width());
				}
				floatRow.append(floatCell);
			});

			// append the row to the table
			target.append(floatRow);
		});	
	}
	
	/**
	 * Determines if the element is outside the browser view area.
	 */
	function isHeaderOutsideScreen(element) {
		var top = $(window).scrollTop();
		var y0 = $(element).offset().top;
		return y0 <= top && top <= y0 + $(element).height();
	}
})(jQuery);
