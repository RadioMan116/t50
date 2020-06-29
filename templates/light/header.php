<?
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
    die();

T50Html::includeAssets(T50Html::T_CSS, ["/styles/app.min.css"]);

?>
<!DOCTYPE html>
<html lang="ru" class="page page_valign_center">

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
    </head>

    <body class="page__body page__body_style_light">
        <div class="page__content">
