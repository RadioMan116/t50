<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<? foreach($arResult["ORDER_VIEW_BLOCKS"]["NOTABS"] as $code => $tabTitle){ ?>
	<? if( $code == "basket" ){ ?>
	<form class="page__item">
		<div class="panel">
			<div id="react_header"></div>
			<div class="page__scroll">
				<div class="panel__content panel__content_size_big">
					<div id="react_statistic"></div>
					<a name="basket"></a>
					<div id="react_basket"></div>
				</div>
			</div>
			<div class="hidden pd-group" id="ajax_basket_load_products"></div>
		</div>
	</form>
	<? } ?>

	<? if( $code == "client" ){ ?>
	<form class="page__item">
		<a name="client"></a>
		<div class="panel">
			<div class="panel__head">
				<div class="panel__head-main">
					<h2 class="panel__title panel__title_type_subtitle">Данные клиента</h2>
				</div>
				<div class="panel__head-controls">
					<div class="link link_type_trigger link_style_trigger js-panel-trigger" data-text="Развернуть">Свернуть</div>
				</div>
			</div>
			<div class="page__scroll">
				<div class="panel__content panel__content_size_big panel__content_type_clipped">
					<div id="react_client"></div>
				</div>
			</div>
		</div>
	</form>
	<? } ?>

	<? if( $code == "delivery" ){ ?>
	<form class="page__item">
		<a name="delivery"></a>
		<div class="panel">
			<div class="panel__head">
				<div class="panel__head-main">
					<h2 class="panel__title panel__title_type_subtitle">Условия доставки
						<span class="panel__head-check" id="react_delivery__flag_one_supplier"></span>
					</h2>
				</div>
				<div class="panel__head-controls">
					<div class="link link_type_trigger link_style_trigger js-panel-trigger" data-text="Развернуть">Свернуть</div>
				</div>
			</div>
			<div class="panel__content">
				<div id="react_delivery"></div>
			</div>
		</div>
	</form>
	<? } ?>

	<? if( $code == "installation" ){ ?>
	<form class="page__item">
		<a name="installation"></a>
		<div class="panel">
			<div class="panel__head">
				<div class="panel__head-main">
					<h2 class="panel__title panel__title_type_subtitle">Установка
						<span class="panel__head-check" id="react_installation__flag_one_supplier"></span>
					</h2>
				</div>
				<div class="panel__head-controls">
					<div class="link link_type_trigger link_style_trigger js-panel-trigger" data-text="Развернуть">Свернуть</div>
				</div>
			</div>
			<div class="panel__content">
				<div id="react_installation"></div>
			</div>
		</div>
	</form>
	<? } ?>

	<? if( $code == "accounts" ){ ?>
	<form class="page__item">
		<a name="accounts"></a>
		<div class="panel">
			<div class="panel__head">
				<div class="panel__head-main">
					<h2 class="panel__title panel__title_type_subtitle">Номера счетов и заказов
						<span class="panel__head-check"  id="react_accounts__flag_one_supplier"></span>
					</h2>
				</div>
				<div class="panel__head-controls">
					<div class="link link_type_trigger link_style_trigger js-panel-trigger" data-text="Развернуть">Свернуть</div>
				</div>
			</div>
			<div class="panel__content">
				<div id="react_accounts"></div>
			</div>
		</div>
	</form>
	<? } ?>

	<? if( $code == "docs" ){ ?>
	<form class="page__item">
		<a name="docs"></a>
		<div class="panel">
			<div class="panel__head">
				<div class="panel__head-main">
					<h2 class="panel__title panel__title_type_subtitle">Документы</h2>
				</div>
				<div class="panel__head-controls">
					<div class="link link_type_trigger link_style_trigger js-panel-trigger" data-text="Развернуть">Свернуть</div>
				</div>
			</div>
			<div class="panel__content">
				<div id="react_docs"></div>
			</div>
		</div>
	</form>
	<? } ?>

	<? if( $code == "comments" ){ ?>
	<form class="page__item">
		<a name="comments"></a>
		<div class="panel">
			<div class="panel__head">
				<div class="panel__head-main">
					<h2 class="panel__title panel__title_type_subtitle">Комментарии</h2>
				</div>
				<div class="panel__head-controls">
					<div class="link link_type_trigger link_style_trigger js-panel-trigger" data-text="Развернуть">Свернуть</div>
				</div>
			</div>
			<div class="panel__content">
				<div id="react_comments"></div>
			</div>
		</div>
	</form>
	<? } ?>

	<? if( $code == "fine" ){ ?>
	<form class="page__item">
		<a name="fine"></a>
		<div class="panel">
			<div class="panel__head">
				<div class="panel__head-main">
					<h2 class="panel__title panel__title_type_subtitle">Штрафы</h2>
				</div>
				<div class="panel__head-controls">
					<div class="link link_type_trigger link_style_trigger js-panel-trigger" data-text="Свернуть">Развернуть</div>
				</div>
			</div>
			<div class="panel__content panel__content_state_collapse">
				<div id="react_fine"></div>
			</div>
		</div>
	</form>
	<? } ?>

	<? if( $code == "deduction" ){ ?>
	<form class="page__item">
		<a name="deduction"></a>
		<div class="panel">
			<div class="panel__head">
				<div class="panel__head-main">
					<h2 class="panel__title panel__title_type_subtitle">Вычеты</h2>
				</div>
				<div class="panel__head-controls">
					<div class="link link_type_trigger link_style_trigger js-panel-trigger" data-text="Свернуть">Развернуть</div>
				</div>
			</div>
			<div class="panel__content panel__content_state_collapse">
				<div id="react_deduction"></div>
			</div>
		</div>
	</form>
	<? } ?>

	<? if( $code == "profit" ){ ?>
	<form class="page__item">
		<a name="profit"></a>
		<div class="panel">
			<div class="panel__head">
				<div class="panel__head-main">
					<h2 class="panel__title panel__title_type_subtitle">Прибыль по заказу</h2>
				</div>
				<div class="panel__head-controls">
					<div class="link link_type_trigger link_style_trigger js-panel-trigger" data-text="Свернуть">Развернуть</div>
				</div>
			</div>
			<div class="panel__content panel__content_state_collapse">
				<div id="react_profit"></div>
			</div>
		</div>
	</form>
	<? } ?>

	<? if( $code == "reclamation" ){ ?>
	<form class="page__item">
		<a name="reclamation"></a>
		<div class="panel">
			<div class="panel__head">
				<div class="panel__head-main">
					<h2 class="panel__title panel__title_type_subtitle">Рекламации</h2>
				</div>
				<div class="panel__head-controls">
					<div class="link link_type_trigger link_style_trigger js-panel-trigger" data-text="Свернуть">Развернуть</div>
				</div>
			</div>
			<div class="panel__content panel__content_state_collapse">
				<div id="react_reclamation"></div>
			</div>
		</div>
	</form>
	<? } ?>

	<? if( $code == "history" ){ ?>
	<div class="page__item">
		<a name="history"></a>
		<div class="panel">
			<div class="panel__head">
				<div class="panel__head-main">
					<h2 class="panel__title panel__type_subtitle">Логи</h2>
				</div>
				<div class="panel__head-controls">
					<div class="link link_type_trigger link_style_trigger js-panel-trigger" data-text="Свернуть">Развернуть</div>
				</div>
			</div>
			<div class="panel__content panel__content_state_collapse" id="load_history"></div>
		</div>
	</div>
	<? } ?>

<? } ?>