<?
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
	die();

T50Html::includeAssets(T50Html::T_CSS, [
    "/components/select2/dist/css/select2.min.css",
    "/components/fancybox-master/dist/jquery.fancybox.css",
    "/components/datetimepicker/build/jquery.datetimepicker.min.css",
    "/components/tooltipster-master/dist/css/tooltipster.bundle.min.css",
    "/components/noUiSlider-master/distribute/nouislider.min.css",
    "/styles/app.min.css",
    "/styles/new-style.css",
    "/css/styles.css",
]);

T50Html::includeAssets(T50Html::T_JS, [
	"/components/jquery/dist/jquery.min.js",
	"/components/select2/dist/js/select2.full.min.js",
	"/components/ckeditor5-build-classic/ckeditor.js",
	"/components/fancybox-master/dist/jquery.fancybox.min.js",
	"/components/colResizable-master/colResizable-1.6.min.js",
	"/components/datetimepicker/build/jquery.datetimepicker.full.min.js",
	"/components/tooltipster-master/dist/js/tooltipster.bundle.min.js",
	"/components/wnumb-1.1.0/wNumb.js",
	"/components/noUiSlider-master/distribute/nouislider.min.js",
	"/components/tablesorter-master/dist/js/jquery.tablesorter.min.js",
	"/components/Inputmask-5.x/dist/jquery.inputmask.min.js",
	"/scripts/preact.min.js",
	"/scripts/common.js",
	"/scripts/common_details.js",
]);



?>
<!DOCTYPE html>

<html lang="ru" class="page<?=( T50Html::isHtmlNeedClassResize() ? " resize" : "" )?>">

	<head>
		<?$APPLICATION->ShowHead();?>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="imagetoolbar" content="no">
		<meta name="msthemecompatible" content="no">
		<meta name="cleartype" content="on">
		<meta name="HandheldFriendly" content="True">
		<meta name="format-detection" content="telephone=no">
		<meta name="format-detection" content="address=no">
		<meta name="google" value="notranslate">
		<meta name="theme-color" content="#ffffff">
		<meta name="apple-mobile-web-app-capable" content="yes">
		<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
		<title><?$APPLICATION->ShowTitle();?></title>
		<meta name="description" content="">
		<meta name="keywords" content="">
		<meta name="sessid" content="<?=bitrix_sessid()?>">
	</head>

	<body class="page__body">
		<div id="panel">
			<?$APPLICATION->ShowPanel();?>
		</div>
		<div class="page__header">
			<!-- begin .header-->
			<div class="header">
				<div class="grid-12__container grid-12__container_width_full">
					<div class="header__wrapper">
						<div class="header__logo">
							<!-- begin .logo-->
							<a href="/" class="logo"><img src="<?=T50Html::getAssets("/images/logo.svg")?>" alt="T-50" class="logo__image"
								 title="" /></a>
							<!-- end .logo-->
						</div>
						<nav class="header__nav">
							<!-- begin .nav-->
							<nav class="nav">
								<div class="nav__list">
									<div class="nav__item"><a href="#" class="nav__link js-nav-trigger">Каталог</a>
										<div class="nav__dropdown">
											<!-- begin .catalog-menu-->
											<?$APPLICATION->IncludeComponent("t50:catalog.menu");?>
											<!-- end .catalog-menu-->
										</div>
									</div>
									<div class="nav__item"><a href="/orders/" class="nav__link nav__link_undefined">Заказы</a></div>
									<div class="nav__item"><a href="#" class="nav__link nav__link_undefined">Отчеты</a></div>
									<div class="nav__item"><a href="/orders/claims/" class="nav__link nav__link_undefined">Контроль качества</a></div>
									<div class="nav__item"><a href="/news/" class="nav__link nav__link_undefined">Новости</a></div>
								</div>
							</nav><!-- end .nav-->
						</nav>
						<div class="header__user">
							<!-- begin .user-nav-->
							<div class="user-nav">
								<div class="user-nav__wrapper">
									<!-- .user-nav__status_online-->
									<!-- .user-nav__status_offline-->
									<!-- .user-nav__status_away-->
									<div class="user-nav__status user-nav__status_online"><svg class="user-nav__notification-icon"><use xlink:href="<?=T50Html::getAssets("/images/icon.svg#icon_notification")?>"></use></svg>
										<div class="user-nav__dropdown">
											<div class="user-nav__panel">
												<!-- begin .notifications-list-->
												<div class="notifications-list">
													<div class="notifications-list__title">Уведомления:</div>
													<ul class="notifications-list__list">
														<li class="notifications-list__item"><svg class="notifications-list__icon"><use xlink:href="<?=T50Html::getAssets("/images/icon.svg#icon_notification")?>"></use></svg>
															<div class="notifications-list__label">Вас упомянули в новости</div><a href="#" class="notifications-list__link">ЦВТ
																открыло отгрузки по брендам</a>
														</li>
														<li class="notifications-list__item"><svg class="notifications-list__icon"><use xlink:href="<?=T50Html::getAssets("/images/icon.svg#icon_notification")?>"></use></svg>
															<div class="notifications-list__label">Изменение в вашем заказе</div><a href="#" class="notifications-list__link">#864727</a>
															изменена <span class="notifications-list__marked">комиссия поставщика</span>
														</li>
														<li class="notifications-list__item"><svg class="notifications-list__icon"><use xlink:href="<?=T50Html::getAssets("/images/icon.svg#icon_notification")?>"></use></svg>
															<div class="notifications-list__label">Изменение в вашем заказе</div><a href="#" class="notifications-list__link">#864335</a>
															менеджер упомянул вас <span class="notifications-list__marked">в комментарии к заказу</span>
														</li>
													</ul><a href="#" class="notifications-list__more">Смотреть все уведомления</a>
												</div><!-- end .notifications-list-->
											</div>
										</div>
									</div>
									<div class="user-nav__content"><a href="#" class="user-nav__illustration"><img src="<?=T50Html::getAssets("/images/content/user-form/2.png")?>"
											 alt="user" class="user-nav__image" title="" /></a>
										<div class="user-nav__fields">
											<div class="user-nav__field user-nav__field_style_highlighted">
												<a href="#" class="user-nav__link">
													<?=$GLOBALS["USER"]->GetFullName()?>
												</a></div>
											<div class="user-nav__field">Менеджер</div>
										</div>
									</div>
									<div class="user-nav__controls"><a href="/?logout=yes" class="user-nav__button user-nav__link">Выйти</a></div>
								</div>
							</div><!-- end .user-nav-->
						</div>
					</div>
				</div>
			</div><!-- end .header-->
		</div>
		<div class="page__content">
			<!-- end header -->
