(function ($) {
	$(function () {
		'use strict';


		$('input[type=tel], .js-tel-mask').inputmask('+7 (999) 999 99 99');



		var $select = $('.js-select:visible');
		$select.select2({
			minimumResultsForSearch: -1,
			width: '100%',
			dropdownAutoWidth: true
		});

		function jsSelectInit() {
			var $select = $('.js-select:visible').not('.select2-hidden-accessible');
			$select.select2({
				minimumResultsForSearch: -1
			}).on('select2:opening', function (e) {
				if ($(this).hasClass('js-select_type_removed')) {
					var $options = $(this).find('option'),
						$items = $('.select2-results__options').find('li.select2-results__option');

					$options.each(function (i) {
						var removeLink = $(this).data('removeLink');
						setTimeout(function () {
							var $items = $('.select2-results__options').find('li.select2-results__option'),
								$item = $items.eq(i);
							$item.addClass('select2-results__option_type_inline').html('<div class="select2-results__text-wrapper">' + $item.text() + '</div><div class="select2-results__link-wrapper"><a href="' + removeLink + '" class="select2-results__remove js-select-remove">Удалить</a></div>');
						}, 200);
					});
				}
			});
		}



		$('body').on('click', '.js-select-remove', function () {
			console.log('remove option', $(this).href());
		});



		var $tags = $('.js-tags');
		$tags.select2({
			tags: true,
			multiple: true,
			allowclear: true,
			minimumResultsForSearch: -1,
			theme: 'tags-list',
			dropdownCssClass: 'hidden',
		});



		var $smoothScrolling = $('.js-smooth-scrolling');
		$smoothScrolling.on('click', function (e) {
			$('html, body').stop().animate({
				scrollTop: $($(this).attr('href')).offset().top
			}, 600);
			e.preventDefault();
		});



		// Календарь
		$.datetimepicker.setLocale('ru');

		function jsDate() {
			$('.js-date:visible').not('disabled').not('[readonly]').datetimepicker({
				timepicker: false,
				format: 'd.m.Y',
				scrollMonth: false
			});
		}

		function jsTime() {
			$('.js-time:visible').not('disabled').not('[readonly]').datetimepicker({
				datepicker: false,
				format: 'H:i'
			});
		}

		jsDate();
		jsTime();



		var $modal = $('.js-modal');
		$modal.fancybox({
			afterShow: function () {
				jsDate();
				jsTime();
				jsSelectInit();
				range();
			},
			afterLoad: function () {
				this.$content.trigger("modal_loaded", this.opts);
			},
			afterClose: function () {
				this.$content.trigger("modal_close", this.opts);
			}
		});



		var $tooltip = $('.js-tooltip');
		$tooltip.tooltipster({
			minWidth: 200,
			contentCloning: false,
			trigger: 'click',
			interactive: true
		});



		var $groupCheck = $('.js-group-check');
		$groupCheck.each(function () {
			var target = $(this).data('target'),
				scope = $(this).data('scope'),
				$trigger = $(this).find('input');

			if (target && scope && $trigger.length) {
				var $target = $(this).closest(scope).find(target).not($trigger);

				if ($target.length) {
					$trigger.on('change', function () {
						if ($(this).prop('checked')) {
							$target.prop('checked', true).change();
						} else {
							$target.prop('checked', false).change();
						}
					});
				}
			}
		});

		var $groupCheckTrigger = $('.js-group-check-trigger');
		$groupCheckTrigger.on('click', function () {
			var target = $(this).data('target'),
				scope = $(this).data('scope'),
				checked = $(this).attr('data-checked');

			if (target) {
				var $target = $(target);
				if (scope) $target = $(this).closest(scope).find(target);

				if (checked === 'true') {
					$(this).attr('data-checked', 'false');
					checked = true;
				} else {
					$(this).attr('data-checked', 'true');
					checked = false;
				}

				$target.prop('checked', checked);
			}

			return false;
		});



		var $groupEditingPanel = $('.js-group-editing-panel');
		$groupEditingPanel.each(function () {
			var $groupEditingPanel = $(this),
				$pill = $(this).find('.group-editing__pill'),
				value = $(this).find('.group-editing__value'),
				$selectInput = $('.js-group-editing-select');

			$selectInput.on('change', function () {
				var number = $groupEditingPanel.find('.js-group-editing-select input:checked').length;
				value.text(number);
				if (number === 0) {
					$pill.addClass('group-editing__pill_state_hidden');
				} else {
					$pill.removeClass('group-editing__pill_state_hidden');
				}
			});
		});


		function range() {
			var $range = $('.js-range:visible').not('.range-init');
			$range.each(function () {
				var $stepsSlider = $(this).find('.range__slider'),
					$inputFirst = $(this).find('.range__input_type_from'),
					$inputSecond = $(this).find('.range__input_type_to'),
					start = $(this).data('start') || 0,
					stop = $(this).data('stop') || 1,
					min = $(this).data('min') || 0,
					max = $(this).data('max') || 1;
				if (min == max)
					max += 1;

				if ($stepsSlider.length && $inputFirst.length && $inputSecond.length) {
					var inputs = [$inputFirst[0], $inputSecond[0]],
						stepsSlider = $stepsSlider[0];

					$(this).addClass('range-init');

					noUiSlider.create(stepsSlider, {
						start: [start, stop],
						connect: true,
						range: {
							min: min,
							max: max
						},
						step: 1,
						format: wNumb({
							decimals: 0,
							thousand: ' ',
						})
					});

					stepsSlider.noUiSlider.on('update', function (values, handle) {
						inputs[handle].value = values[handle];
					});

					inputs.forEach(function (input, handle) {

						input.addEventListener('change', function () {
							stepsSlider.noUiSlider.setHandle(handle, this.value);
						});

						input.addEventListener('keydown', function (e) {

							var values = stepsSlider.noUiSlider.get(),
								value = Number(values[handle]),
								steps = stepsSlider.noUiSlider.steps(),
								step = steps[handle],
								position;

							// 13 is enter,
							// 38 is key up,
							// 40 is key down.
							switch (e.which) {

								case 13:
									stepsSlider.noUiSlider.setHandle(handle, this.value);
									break;

								case 38:

									position = step[1];

									if (position === false) {
										position = 1;
									}

									if (position !== null) {
										stepsSlider.noUiSlider.setHandle(handle, value + position);
									}

									break;

								case 40:

									position = step[0];

									if (position === false) {
										position = 1;
									}

									if (position !== null) {
										stepsSlider.noUiSlider.setHandle(handle, value - position);
									}

									break;
							}
						});
					});
				}
			});
		}

		range();


		var $alphabetFilter = $('.js-alphabet-filter');
		$alphabetFilter.each(function () {
			var $alphabetFilterCheck = $(this).find('input'),
				target = $(this).data('target'),
				scope = $(this).data('scope'),
				toggleClass = $(this).data('toggleClass');

			if (target && scope && toggleClass && $alphabetFilterCheck.length) {
				var $target = $(this).closest(scope).find(target);

				$alphabetFilterCheck.on('change', function () {
					if ($(this).prop('checked')) {
						if ($(this).val() === 'reset') {
							$target.removeClass(toggleClass);
						} else {
							var searchLetter = $(this).val().toLowerCase(),
								$filteredTarget = $target;

							$filteredTarget = $(this).closest(scope).find('[data-letter="' + searchLetter + '"]');
							console.log(scope);

							if ($filteredTarget.length) {
								$target.addClass(toggleClass);
								var i;
								for (i = 0; i < $filteredTarget.length; ++i) {
									$($filteredTarget[i]).removeClass(toggleClass);
								}
							} else {
								$target.addClass(toggleClass);
							}
						}

					}
				});
			}
		});



		var $personsFinderTrigger = $('.js-persons-finder-trigger');
		$personsFinderTrigger.on('click', function () {
			$(this).closest('.persons-finder').toggleClass('persons-finder_state_open');
			$('.tooltipstered').tooltipster('hide')
			return false;
		});

		$(document).mouseup(function (e) {
			var $div = $('.persons-finder');
			if (!$div.is(e.target) &&
				$div.has(e.target).length === 0) {
				$('.persons-finder_state_open').removeClass('persons-finder_state_open');
				$('.tooltipstered').tooltipster('hide')
			}
		});



		var $resizeTable = $('.js-resize-table');
		$resizeTable.colResizable({
			liveDrag: true,
			gripInnerHtml: "<div class='table__grip'></div>",
			draggingClass: "dragging",
			resizeMode: 'overflow'
		});



		if ($('.js-wysiwyg').length) {
			var config = {
				toolbar: [
					'undo', 'redo',
					'|',
					'bold', 'italic', 'backStyle', 'link', 'bulletedList', 'numberedList'
				],
			};
			ClassicEditor.create(document.querySelector('.js-wysiwyg'), config)
				.catch(function (error) {
					console.error(error);
				});
		}



		var $blockToggle = $('.js-block-toggle input');
		$blockToggle.on('change', function () {
			var activeTarget = $(this).closest('.check-elem').data('active-target'),
				target = $(this).closest('.check-elem').data('target');

			if ($(this).prop('checked')) {
				$(activeTarget).removeClass('hidden');
				$(target).addClass('hidden');
			} else {
				$(activeTarget).addClass('hidden');
				$(target).removeClass('hidden');
			}
			$('.tooltipstered').tooltipster('hide')
			return false;
		});



		var $tab = $('.js-tabs-trigger');
		$tab.on('click', function () {
			var target = $(this).attr('href').substr(1);

			$(this).closest('.tabs').find('.tabs__tab_state_active').removeClass('tabs__tab_state_active');
			$(this).addClass('tabs__tab_state_active');

			$(this).closest('.tabs').find('.tabs__panel_state_active').removeClass('tabs__panel_state_active');
			$("#_" + target).addClass('tabs__panel_state_active');

			jsSelectInit();
			jsDate();
			jsTime();

			$('.tooltipstered').tooltipster('hide')
			document.location.hash = target;
			return false;
		});
		var activeLink = $(".js-tabs-trigger[href='" + document.location.hash + "']");
		if (activeLink.length)
			activeLink.click();


		var $catalogTab = $('.js-catalog-tab-trigger');
		$catalogTab.on('click', function () {
			var target = $(this).attr('href');

			$(this).closest('.catalog-menu').find('.catalog-menu__tab-link_state_active').removeClass('catalog-menu__tab-link_state_active');
			$(this).addClass('catalog-menu__tab-link_state_active');

			$(this).closest('.catalog-menu').find('.catalog-menu__panel_state_active').removeClass('catalog-menu__panel_state_active');
			$(target).addClass('catalog-menu__panel_state_active');

			$('.tooltipstered').tooltipster('hide')
			return false;
		});



		var $navTrigger = $('.js-nav-trigger');
		$navTrigger.on('click', function () {
			$(this).toggleClass('nav__link_state_active');
			$('.tooltipstered').tooltipster('hide')
			return false;
		});



		var $navClose = $('.js-dropdown-close');
		$navClose.on('click', function () {
			$(this).closest('.nav__item').find('.nav__link').removeClass('nav__link_state_active');
			$('.tooltipstered').tooltipster('hide')
			return false;
		});



		var $entryContent = $('.entry-snippet__content');
		$entryContent.each(function () {
			if ($(this).outerHeight() > 200) {
				$(this).addClass('entry-snippet__content_type_collapse entry-snippet__content_state_collapse');
			}
		});

		var $entryContentTrigger = $('.js-entry-content-trigger');
		$entryContentTrigger.on('click', function () {
			var $content = $(this).closest('.entry-snippet__content'),
				text = $(this).text(),
				nText = $(this).data('text');

			$content.toggleClass('entry-snippet__content_state_collapse');
			$(this).text(nText);
			$(this).data('text', text);

			$('.tooltipstered').tooltipster('hide')
			return false;
		});



		var $entryContentTrigger = $('.js-panel-trigger');
		$entryContentTrigger.on('click', function () {
			var $content = $(this).closest('.panel').find('.panel__content'),
				text = $(this).text(),
				nText = $(this).data('text');

			$content.toggleClass('panel__content_state_collapse');
			$(this).text(nText);
			$(this).data('text', text);

			$('.tooltipstered').tooltipster('hide')
			return false;
		});



		var $collapseTrigger = $('.js-collapse-trigger');
		$collapseTrigger.on('click', function () {
			var $content = $(this).closest('.collapse-panel').find('.collapse-panel__content'),
				text = $(this).text(),
				nText = $(this).data('text');

			$content.toggleClass('collapse-panel__content_state_collapse');
			$(this).text(nText);
			$(this).data('text', text);

			$('.tooltipstered').tooltipster('hide')
			return false;
		});



		$(document).on('click', '.js-quantity-widget-plus', function () {
			var $widget = $(this).closest('.quantity-widget'),
				$input = $(this).closest('.quantity-widget').find('input'),
				curVal = parseInt($input.val()),
				maxVal = parseInt($widget.data('max'));

			if (curVal >= maxVal && maxVal > 0) {
				console.log('Нельзя выбрать больше максимального значения');
				return false;
			}
			$input.val(parseInt($input.val()) + 1);
			$input.change();

			$('.tooltipstered').tooltipster('hide')
			return false;
		});

		$(document).on('click', '.js-quantity-widget-minus', function () {
			var $widget = $(this).closest('.quantity-widget'),
				$input = $(this).closest('.quantity-widget').find('input'),
				curVal = parseInt($input.val()),
				minVal = parseInt($widget.data('min')),
				count = parseInt($input.val()) - 1;

			if (curVal <= minVal && minVal > 0) {
				console.log('Нельзя выбрать меньше минимального значения');
				return false;
			}
			count = count < 1 ? 1 : count;

			$input.val(count);
			$input.change();

			$('.tooltipstered').tooltipster('hide')
			return false;
		});

		$(document).on('change', '.js-quantity-widget', function () {
			var $widget = $(this).closest('.quantity-widget'),
				$input = $(this),
				curVal = parseInt($input.val()),
				minVal = parseInt($widget.data('min')),
				maxVal = parseInt($widget.data('max')),
				count = parseInt($input.val());

			if (curVal >= maxVal && maxVal > 0) {
				count = maxVal;
				console.log('Нельзя выбрать больше максимального значения');
			}

			if (curVal <= minVal && minVal > 0) {
				count = minVal;
				console.log('Нельзя выбрать меньше минимального значения');
			}

			count = isNaN(count) ? minVal : count;
			$input.val(count);

			$('.tooltipstered').tooltipster('hide')
			return false;
		});



		var $checkToggleTrigger = $('.js-check-toggle').find('input');
		$checkToggleTrigger.on('change', function () {
			var $checkToggle = $(this).closest('.js-check-toggle'),
				target = $checkToggle.data('toggleTarget'),
				scope = $checkToggle.data('toggleScope'),
				toggleClass = $checkToggle.data('toggleClass'),
				text = $checkToggle.find('.check-elem__label').text(),
				nText = $checkToggle.data('toggleText'),
				$label = $checkToggle.find('.check-elem__label');

			if (target && toggleClass) {
				var $target = $(target);

				if (scope) {
					$target = $(this).closest(scope).find($target);
				}

				if ($(this).prop('checked')) {
					$target.addClass(toggleClass);
				} else {
					$target.removeClass(toggleClass);
				}

				$label.text(nText);
				$checkToggle.data('toggleText', text);

				$('.tooltipstered').tooltipster('hide')
			}
		});



		var $mpTriggerRollback = $('.js-mp-trigger-rollback');
		$mpTriggerRollback.on('click', function () {
			var scope = $(this).data('toggleScope');

			if (scope) {
				console.log($(this).closest(scope).find('.js-mp-trigger').length);
				$(this).closest(scope).find('.js-mp-trigger').trigger('click');
			}
			return false;
		});



		var $mpTrigger = $('.js-mp-trigger');

		function parseAtribute(str) {
			if (typeof str === 'undefined') {
				return null;
			}

			if (str) {
				var arrayItems = str.split(',');
				var clearArrayItems = arrayItems.map(function (classString) {
					return classString.trim();
				});

				return clearArrayItems;
			}
		}

		$mpTrigger.on('click', function () {
			var toggleTarget = $(this).data('toggleTarget'),
				toggleClass = $(this).data('toggleClass'),
				text = $(this).text(),
				nText = $(this).data('toggleText');

			if (toggleTarget && toggleClass) {
				var targetList = parseAtribute(toggleTarget);
				var classList = parseAtribute(toggleClass);

				if (targetList.length && classList.length) {
					targetList.forEach(function (item, i) {
						if (typeof classList[i] !== 'undefined') {

							$(item).toggleClass(classList[i]);
						}
					});
				}
			}

			if (nText) {
				$(this).text(nText);
				$(this).data('toggleText', text);
			}

			jsSelectInit();
			return false;
		});



		var $toggleTrigger = $('.js-toggle');
		$toggleTrigger.on('click', function () {
			var target = $(this).data('toggleTarget'),
				scope = $(this).data('toggleScope'),
				toggleClass = $(this).data('toggleClass'),
				selfToggleClass = $(this).data('toggleSelfClass'),
				text = $(this).text(),
				nText = $(this).data('toggleText'),
				hasClass = false,
				hasNotClass = false;

			if (target && toggleClass) {
				var $target = $(target);
				if (scope) {
					$target = $(this).closest(scope).find($target);
				}

				if (selfToggleClass) {
					$(this).toggleClass(selfToggleClass);
				}

				$target.each(function () {
					if ($(this).hasClass(toggleClass)) {
						hasClass = true;
						return false;
					}
				});

				if (hasClass) {
					$target.each(function () {
						if (!$(this).hasClass(toggleClass)) {
							hasNotClass = true;
							return false;
						}
					});
				}

				if (hasClass && hasNotClass) {
					$target.addClass(toggleClass);
				} else {
					$target.toggleClass(toggleClass);
				}

				$(this).text(nText);
				$(this).data('toggleText', text);

				jsSelectInit();
			}

			$('.tooltipstered').tooltipster('hide');
			return false;
		});



		$('body').on('click', '.js-clone-row', function () {
			var $row = $(this).closest('tr');

			if ($row.length) {
				var $newRow = $row.clone();
				$row.after($newRow);
			}

			return false;
		});



		var $addFineRow = $('.js-add-row');
		$addFineRow.on('click', function () {
			var targetTable = $(this).data('targetTable'),
				targetRow = $(this).data('targetRow'),
				$selfRow = $(this).closest('tr'),
				addClass = $(this).data('addClass'),
				addChild = $(this).data('addChild');

			if (targetTable && targetRow) {
				var $table = $(targetTable),
					$row = $(targetRow);

				if ($table.length && $row.length) {
					var $newRow = $row.clone();
					$newRow.removeClass('table__tr_state_hidden').removeAttr('id').find('input').not('.js-date, [readonly]').val('');

					if (!$selfRow.hasClass(addClass)) {
						$newRow.addClass('table__tr_type_last');
					}

					if (addClass && $selfRow) {
						$selfRow.addClass(addClass);
					}

					if (addChild) {
						$selfRow.after($newRow);
					} else {
						$row.after($newRow);
					}

					jsDate();
					jsSelectInit();
					range();
				}
			}

			return false;
		});



		$('body').on('click', '.js-remove-row', function () {
			$(this).closest('tr').remove();

			return false;
		});



		var $redirect = $('.js-redirect');
		$redirect.each(function () {
			var _that = this,
				time = $(this).data('redirectTime') || 10,
				url = $(this).data('redirectUrl');

			setInterval(function () {
				if (time === 0) {
					if (url) location = url;
				} else {
					time--;
				}
				$(_that).text(time);
			}, 1000);
		});


		var $tableSortTrigger = $('.js-table-sort-trigger');
		$tableSortTrigger.each(function () {
			var $table = $(this).closest('table');
			$table.find('th').addClass('sorter-false');
			$table.tablesorter();

			$table.on('sortEnd', function (event) {});
		});

		var $tableSort = $('.js-table-sort-trigger');
		$tableSort.on('click', function () {
			var sort = $(this).data('sort'),
				$sort = $(this).closest('.sort'),
				$table = $(this).closest('table'),
				$th = $table.find('th'),
				$td = $table.find('td'),
				$tr = $table.find('tr'),
				index = $th.index($(this).closest('th'));

			$(this).closest('table').trigger('sorton', [sort]);

			$th.removeClass('table__th_style_strict');
			$td.removeClass('table__td_style_strict');
			$th.eq(index).addClass('table__th_style_strict');

			$tr.each(function () {
				$(this).find('td').eq(index).addClass('table__td_style_strict');
			});

			$(this).closest('.table').find('.sort').removeClass('sort_asc sort_desc');

			if (sort !== 'data-sort') {
				if (sort[0][1]) {
					$sort.removeClass('sort_asc');
					$sort.addClass('sort_desc');
				} else {
					$sort.removeClass('sort_desc');
					$sort.addClass('sort_asc');
				}
			} else {
				$sort.removeClass('sort_asc sort_desc');
				$th.removeClass('table__th_style_strict');
				$td.removeClass('table__td_style_strict');
			}

			return false;
		});




		var $numberField = $('.js-number-field');
		$numberField.keyup(function () {
			if (isNaN($(this).val())) {
				var inputValue = $(this).val(),
					clearInputValue = inputValue.replace(/[^\d\s]/g, '');
				$(this).val(clearInputValue);
			};
		});



		// Все что ниже это демо
		$('.comments-list__wrapper_type_editable').on('click', function () {
			var text = $(this).text();

			$(this).closest('.panel').find('textarea').text(text);
		});

		$('.js-drop-panel-trigger').on('click', function () {
			var text = $(this).text(),
				$textContainer = $(this).closest('.drop-panel').find('.drop-panel__trigger');

			if ($textContainer.length && text) {
				$textContainer.text(text);

				$('.v-nav__link_state_active').removeClass('v-nav__link_state_active');
				$(this).addClass('v-nav__link_state_active');
			}
		});



		$('.persons-finder__link').on('click', function () {
			var name = $(this).text(),
				$textarea = $(this).closest('form').find('textarea');

			if ($textarea.length) {
				var text = $textarea.val();

				name = name.replace(/\s+/g, " ");

				$textarea.val(name + ', ' + text);

				$('.js-persons-finder-trigger').trigger('click');
			}

			return false;
		});



		$('body').on('click', '.js-pseudo-select', function () {
			$(this).addClass('pseudo-select_state_dropdown');
			return false;
		});



		$(document).mouseup(function (e) {
			var $pds = $('.pseudo-select');
			if (!$pds.is(e.target) &&
				$pds.has(e.target).length === 0) {
				$pds.removeClass('pseudo-select_state_dropdown');
			}
		});



		$(window).scroll(function () {
			var $pds = $('.pseudo-select');
			$pds.removeClass('pseudo-select_state_dropdown');
		});



		$('body').on('click', '.js-pseudo-select-item', function () {
			var $pds = $(this).closest('.pseudo-select'),
				$current = $pds.find('.pseudo-select__current'),
				$select = $pds.find('.pseudo-select__select'),
				value = $(this).data('value'),
				currentText = $(this).find('.pseudo-select__text').text();

			$pds.removeClass('pseudo-select_state_dropdown');
			$current.text(currentText);
			$select.val(value);
			return false;
		});

		window.common = {
			jsDate,
			jsTime,
			jsSelectInit,
			range
		};

		(() => {
			let value = 0
			let array = []
			let diagram = document.querySelector(".diagram .diagram__item");
			if (diagram) {

				$('.diagram__list').find('.diagram__item').each(function (index, element) {
					array.push($(element).data('param'));
					console.log(array)
				})
				let maxVal = Math.max.apply(null, array);
				value = maxVal

				let zeroCheck = array.reduce(function (prev, next) {
					return prev + next;
				});
				if (!(zeroCheck == 0 || undefined)) {
					$('.diagram__item').each(function (index, el) {
						let data1 = $(this).data('param');
						if (data1 !== 0) {
							$(this).css({
								'height': ($(this).data('param') / value * 100 + '%')
							});
						} else(
							$(this).addClass("not")
						)

					})
				}
			}
		})();

	});
})(jQuery);
