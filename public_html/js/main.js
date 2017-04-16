$(document).ready(function () {

	$('#Content.admin a.setPredefinedAvailability').on("click", function () {
		var $button = $(this);
		$button.closest('.form').find('.availabilityInput').val($button.data('value'));
	});

	$('#Content.admin div.e_item .form').hover(
		function () { $('.subButtons', this).show(); },
		function () { $('.subButtons', this).hide(); }
	);

	$('.showMe').each(function () {
		var $container = $(this);
		$container.find('.shower').on('click', function () {
			$('.toShow', $container).toggle();
		});
	});

	/**
	 * Edit buttons
	 */
	$('body.showEditLink .editObject').each(function() {
		var $container = $(this),
			action = $container.data('editAction'),
			id = $container.data('editId')
			text = 'Editovat',
			addClasses = '';

		if ($container.width() < 120 || $container.height() < 60) {
			text = 'e';
			addClasses = 'btn-xs';
		}

		$container.append(
			$('<a>', {
				class: 'editButton btn btn-info ' + addClasses,
				text: text,
				target: '_blank',
				title: 'Editovat v adminu',
				href: '/?p=admin&action=' + action + '&id=' + id
			})
		);
	});

	/**
	 * Menu s výběrem akcí v adminu
	 */
	$('#adminCrossroadsToggle').on('click', function() {
		$('#adminCrossroads').fadeToggle(200);
	});

	/**
	 * Doprava v košíku
	 */
	$('#cartDelivery').on('change', function () {
		if ($(this).val() === 'zasilkovna') {
			$('#zasilkovnaList').show();
		}
		else {
			$('#zasilkovnaList').hide();
		}
	});


	/**
	 * Rozbalování menu kategorií
	 */
	(function() {

		var $menus = $('div.catMenu');

		$menus.each(function (index, el) {

			var $menu = $(el),
				activeId = $menu.data('activeId');

			function showSubs($clicked) {
				$clicked.toggleClass('open');
				$('> ul', $clicked.closest('li')).toggle();
			}

			function onClick() {
				var $clicked = $(this);

				if ($clicked.data('dataLoaded')) {
					return showSubs($clicked);
				}

				$.ajax({
					type: 'POST',
					url: '/?p=categoryTree',
					data: {
						page: $clicked.data('page'),
						cat: $clicked.data('id'),
						lineContent: $menu.data('line-content')
					},
					success: function (response) {
						$clicked.closest('li').append(response);
						$clicked.data('dataLoaded', true);
						showSubs($clicked);

						var callback = $clicked.data('callback');
						$clicked.data('callback', false);
						if (callback) {
							callback($clicked);
						}

					}
				});
			}

			$menu.on('click', 'li span.toggle.hasSubs', onClick);

		});

	})();

	/**
	 * Aktivní kategorie v menu - nalezení
	 */
	(function () {
		var activeCat = $('#catMenu').data('active-id') || $.cookie('catMenu');
		$.cookie('catMenu', activeCat);

		function delve($parent) {
			var $found = $('#catToggleId' + activeCat);
			if ($found.length) {
				return $found.closest('li').toggleClass('inactive active').find('.toggle').click();
			}

			$('span.toggle.hasSubs', $parent).each(function () {
				$cat = $(this);

				if ($cat.data('children').match(new RegExp('\\b' + activeCat + '\\b'))) {
					$cat.data('callback', function ($cat) {
						delve($cat.closest('li').find('ul'));
					});

					$cat.click();

					return false;
				}
			});
		}

		delve($('#catMenu'));

	})();


	/**
	 * Výběr reference v adminu
	 */
	$('div.referenceSelector').on('click', ' a.button', function () {
		var selected = $(this).closest('div.referenceContainer').find('div.referenceSelected');
		$('input', selected).val( $(this).data('id') );
		$('span.name', selected).text( $(this).data('name') );
	});

	/**
	 * Photo viewer
	 * @argument {string} selector activate photo viewer at these elements
	 */
	function photoViewer(selector) {

		this.viewer = $('#photoViewer');
		$(selector).on('click', $.proxy(showPhoto, this));
		this.viewer.on('click', $.proxy(closePhoto, this));
		$(document).on('keyup', function(e) {
			if (e.which === 27) {
				closePhoto(e);
			}
		});

		function closePhoto(e) {
			e.stopPropagation();
			this.viewer.hide();
		}

		function showPhoto(e) {
			var link = $(e.currentTarget);
			e.preventDefault();
			var img = $("<img src='"+ link.attr('href') +"'>");
			img.on('click', $.proxy(closePhoto, this));
			img.css('max-width', $(window).width() - 50 + 'px');
			img.css('max-height', $(window).height() - 100 + 'px');
			this.viewer
					.show()
					.width($(window).width())
					.height($(document).height())
					.css({
						'padding-top': $(window).scrollTop() + 50 + 'px'
					})
					.html(img);
		}

	}

	photoViewer('a.viewPhoto');

	$('a[rel=external]').attr('target', '_blank');

	$(':input.autosubmit').on('change', function () {
		$(this).closest('form').submit();
	});
	$('.autosubmitForm').on('change', ':input', function (e) {
		if (e.isTrigger) {
			return;
		}
		$('#overlay')
			.height($(document).height())
			.css(
				'background-position',
				'50% ' + ($(window).height() / 2 + $(window).scrollTop()) + 'px'
			)
			.show();
		$(this).closest('form').submit();
	});

	$('#cartContinue').on('click', function (e) {

		if ( $('#cartDelivery').val() == '' || $('#cartPayment').val() == ''  ) {
			e.preventDefault();
			alert('Zvolte prosím dopravu a způsob platby.');
		}

	});

	$('input.autoselectValue').on('click', function() {
		this.select();
	});

	$('button.closeWindow').click(function() {
		window.close();
	});

	$('.openUploader').on('click', function () {
		var uploaderWin = window.open(
				'/?p=admin&action=uploader#images',
				'_blank',
				'height=600,width=1080,location=0,top=100,left=100'
			),
			$urlTarget = $('#' + $(this).data('urlTarget'));

		$(window).on('message', function (e) {
			$urlTarget.val(e.originalEvent.data.src);
			$urlTarget.nextAll('img').prop('src', e.originalEvent.data.src);
		});
	});

	$('.messageOk, .messageInfo, .messageWarning, .alert').on('dblclick', function () {
		$(this).slideUp();
	});

	$('#cartPlaceholder').load('/?p=ajaxSideCart', function() {
		$('#sideCart').fadeIn(500);
	});

	(function () {

		var $addLink = $('a.addToFavorites');

		function getStoredList() {
			return JSON.parse(localStorage.getItem('favoriteProducts')) || {};
		}

		function handleAddRemove() {
			var $button = $(this);
			var list = JSON.parse(localStorage.getItem('favoriteProducts')) || {};
			if (list.hasOwnProperty($button.data('productId'))) {
				delete list[$button.data('productId')];
				$button.find('.text').text('Přidat do oblíbených');
			} else {
				list[$button.data('productId')] = $button.data('productName');
				$button.find('.text').text('Odstranit z oblíbených');
			}
			localStorage.setItem('favoriteProducts', JSON.stringify(list));
			$button.toggleClass('active');
			updateListWidget();
			initializeAddLink();
		};

		function initializeAddLink() {
			var list = getStoredList();
			if (list.hasOwnProperty($addLink.data('productId'))) {
				$addLink.addClass('active')
						.find('.text')
						.text('Odstranit z oblíbených');
			} else {
				$addLink.removeClass('active')
						.find('.text')
						.text('Přidat do oblíbených');
			}
		}

		function toggleWidget() {
			var $list = $('#favoritesList'),
				$ul = $list.find('ul');
			if ($ul.is(':visible')) {
				$ul.slideUp(400);
				localStorage.setItem('favoriteShown', '0');
			} else {
				$ul.slideDown(400);
				localStorage.setItem('favoriteShown', '1');
			}
			initWidget();
		}

		function initWidget() {
			var $list = $('#favoritesList'),
				$heading = $list.find('h4'),
				count = $list.find('li').length,
				$ul = $list.find('ul');
			if (localStorage.getItem('favoriteShown') === '0') {
				$heading.find('.minimize')
					.removeClass('glyphicon-menu-down')
					.addClass('glyphicon-menu-up');
				$ul.hide();
			} else {
				$heading.find('.minimize')
					.removeClass('glyphicon-menu-up')
					.addClass('glyphicon-menu-down');
			}
			$heading.find('.count').text(count);
		}

		function updateListWidget() {
			$('#favoritesList').remove();
			var list = getStoredList();

			if (jQuery.isEmptyObject(list)) {
				return;
			}

			$('body').append('<div id="favoritesList"><h4>Oblíbené položky&nbsp;<span class="count badge"></span>&nbsp;<span class="pull-right glyphicon glyphicon-menu-down minimize"></span></h4><ul></ul></div>');
			var $list = $('#favoritesList');

			$list.on('mouseenter', function() {
				$list.stop().fadeTo(400, 1);
			}).on('mouseleave', function () {
				$list.fadeTo(1000, .6);
			});

			$list.find('h4').on('click', toggleWidget);

			for (var id in list) {
				if (list.hasOwnProperty(id)) {
					$list.find('ul').append(
						'<li>'
						+ '<a class="remove glyphicon glyphicon-remove text-danger" data-product-id="'+ id +'"></a>'
						+ '<a href="/?p=e-shop&amp;id='+ id +'">'
							+ list[id] + '</a></li>'
					).find('li a.remove').on('click', handleAddRemove);
				}
			}
			$list.fadeIn(1800);
		}

		$addLink.on('click', handleAddRemove).each(initializeAddLink);
		updateListWidget();
		initWidget();
	})();

	(function () {

		var $toggle = $('#rememberOrderFormData'),
			$form = $toggle.closest('form'),
			fields = ['name', 'street', 'town', 'zip', 'email', 'phone', 'remember'];

		if (!$toggle.length) {
			return;
		}

		/**
		 * @param {String} fieldName
		 * @returns {String}
		 */
		function getStoreKeyName(fieldName) {
			return 'orderForm' + fieldName.charAt(0).toUpperCase() + fieldName.slice(1);
		}

		function rememberData() {
			fields.forEach(function (field) {
				localStorage.setItem(getStoreKeyName(field), $form.find('input[name='+ field +']').val());
			});
		}

		function forgetData() {
			fields.forEach(function (field) {
				localStorage.removeItem(getStoreKeyName(field));
			});
		}

		function loadData() {
			fields.forEach(function (field) {
				$form.find('input[name='+ field +']')
						.val(localStorage.getItem(getStoreKeyName(field)))
						.prop('checked', true);
			});
		}

		$toggle.on('change', function () {
			if ($toggle.prop('checked')) {
				rememberData();
			} else {
				forgetData();
			}
		});

		$form.on('submit', function () {
			if ($toggle.prop('checked')) {
				rememberData();
			}
		});

		if (localStorage.getItem(getStoreKeyName('remember'))) {
			loadData();
		}

	})();

	if ('serviceWorker' in navigator) {
		//navigator.serviceWorker.register('/service-worker.js');
	}

});