<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<div class="grid-12__container grid-12__container_width_full order">
	<div class="page__item">
		<!-- begin .panel-->
		<div class="panel">
			<div id="react_header"></div>
			<div class="panel__content">
				<!-- begin common/statistic -->
				<div id="react_statistic"></div>
				<!-- end common/statistic -->
			</div><!-- begin .tabs-->
			<div class="tabs">
				<ul class="tabs__nav">
					<? foreach($arResult["ORDER_VIEW_BLOCKS"]["INTABS"] as $code => $tabTitle){ ?>
						<li class="tabs__item">
							<a href="#<?=$code?>" class="tabs__tab js-tabs-trigger">
								<?=$tabTitle?>
							</a>
						</li>
					<? } ?>
				</ul>
				<div class="tabs__content">
					<? foreach($arResult["ORDER_VIEW_BLOCKS"]["INTABS"] as $code => $tabTitle){ ?>

						<? if( $code == "basket" ){ ?>
							<div id="_basket" class="tabs__panel tabs__panel_state_active">
								<!-- begin common/basket -->
								<div id="react_basket"></div>
								<!-- end common/basket -->

								<div class="tabs__wrapper hidden pd-group" id="ajax_basket_load_products"></div>
							</div>
						<? } ?>

						<? if( $code == "client" ){ ?>
							<div id="_client" class="tabs__panel">
								<!-- begin common/client -->
								<div id="react_client"></div>
								<!-- end common/client -->
							</div>
						<? } ?>

						<? if( $code == "delivery" ){ ?>
							<div id="_delivery" class="tabs__panel">
								<div class="tabs__head-check" id="react_delivery__flag_one_supplier"></div>
								<!-- begin common/delivery -->
								<div id="react_delivery"></div>
								<!-- end common/delivery -->

							</div>
						<? } ?>

						<? if( $code == "installation" ){ ?>
						    <div id="_installation" class="tabs__panel">
								<div class="tabs__head-check" id="react_installation__flag_one_supplier"></div>
								<!-- begin common/installation -->
								<div id="react_installation"></div>
								<!-- end common/installation -->

								<div class="tabs__wrapper hidden pd-group">
									<!-- begin common/products -->
									<?include __DIR__ . "/include/products.php"?>
									<!-- end common/products -->
								</div>
							</div>
						<? } ?>

						<? if( $code == "accounts" ){ ?>
						    <div id="_accounts" class="tabs__panel">
								<div class="tabs__head-check" id="react_accounts__flag_one_supplier"></div>
								<!-- begin common/accounts -->
								<div id="react_accounts"></div>
								<!-- end common/accounts -->
							</div>
						<? } ?>

						<? if( $code == "docs" ){ ?>
						    <div id="_docs" class="tabs__panel">
								<!-- begin common/docs -->
								<div id="react_docs"></div>
								<!-- end common/docs -->
							</div>
						<? } ?>

						<? if( $code == "comments" ){ ?>
						    <div id="_comments" class="tabs__panel">
								<!-- begin common/comments -->
								<div id="react_comments"></div>
								<!-- end common/comments -->
							</div>
						<? } ?>

						<? if( $code == "fine" ){ ?>
						    <div id="_fine" class="tabs__panel">
								<!-- begin common/fine -->
								<div id="react_fine"></div>
								<!-- end common/fine -->
							</div>
						<? } ?>

						<? if( $code == "deduction" ){ ?>
						    <div id="_deduction" class="tabs__panel">
								<!-- begin common/deduction -->
								<div id="react_deduction"></div>
								<!-- end common/deduction -->
							</div>
						<? } ?>

						<? if( $code == "profit" ){ ?>
						    <div id="_profit" class="tabs__panel">
								<!-- begin common/profit -->
								<div id="react_profit"></div>
								<!-- end common/profit -->
							</div>
						<? } ?>

						<? if( $code == "reclamation" ){ ?>
						    <div id="_reclamation" class="tabs__panel">
								<div class="panel__content">
									<!-- begin common/reclamation -->
									<div id="react_reclamation"></div>
									<!-- end common/reclamation -->
								</div>
							</div>
						<? } ?>

						<? if( $code == "history" ){ ?>
						   <div id="_history" class="tabs__panel">
								<div id="load_history"></div>
							</div>
						<? } ?>

					<? } // endforeach INTABS?>
				</div>
			</div><!-- end .tabs-->
		</div><!-- end .panel-->
	</div>

	<?include __DIR__ . "/blocks.php"?>

</div>
