<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<div class="page__login">

	<div class="login-group">
		<div class="login-group__header">
			<div class="login-group__logo">
				<a href="index.html" class="logo"><img src="<?=T50Html::getAssets("/images/logo-simple.svg")?>" alt="T-50" class="logo__image"
					 title="" /></a>
			</div>
			<div class="login-group__title">Авторизация</div>
			<?//ShowMessage($arParams["~AUTH_RESULT"]);?>
		</div>
		<?//ShowMessage($arParams["~AUTH_RESULT"]);?>
				<? if( !empty($arParams["~AUTH_RESULT"]) ){ ?>
					<div class="login-group__nav">
						<?ShowMessage($arParams["~AUTH_RESULT"]);?>
					</div>
				<? } ?>
		<div class="login-group__content">

			<form class="form" method="post" action="">


				<label class="form__line">
					<span class="form__label">Логин</span>
					<input type="text" class="form__input" name="USER_LOGIN" maxlength="255" value="<?=$arResult["LAST_LOGIN"]?>"/>
				</label>

				<label class="form__line">
					<span class="form__label">Пароль</span>
					<input type="password" placeholder="*************" class="form__input" name="USER_PASSWORD" maxlength="255" autocomplete="off"/>
				</label>

				<button class="button button_width_full login-group__control" type="submit" name="Login">Войти</button>


				<input type="hidden" name="AUTH_FORM" value="Y" />
				<input type="hidden" name="TYPE" value="AUTH" />
				<?if (strlen($arResult["BACKURL"]) > 0){?>
					<input type="hidden" name="backurl" value="<?=$arResult["BACKURL"]?>" />
				<? } ?>
				<?foreach ($arResult["POST"] as $key => $value){?>
					<input type="hidden" name="<?=$key?>" value="<?=$value?>" />
				<? } ?>
				<?=bitrix_sessid_post()?>

				<div class="login-group__nav">
					<div class="dual-panel">
						<div class="dual-panel__row">
							<div class="dual-panel__col dual-panel__col_align_left">
								<div class="check-elem check-elem_type_inline">
									<input type="checkbox" name="USER_REMEMBER" id="USER_REMEMBER" value="Y" class="check-elem__input">
									<label for="USER_REMEMBER" class="check-elem__label">Запомнить меня на этом компьютере</label>
								</div>
							</div>
						</div>
					</div>
				</div>

			</form>
		</div>
	</div>
</div>
