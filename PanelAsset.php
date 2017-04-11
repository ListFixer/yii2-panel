<?php namespace listfixer\panel;

use yii\web\AssetBundle;

class PanelAsset extends AssetBundle
{
	public $sourcePath = ( __DIR__ . '/dist' );
	public $css = [ 'css/panel.css' ];
	public $js = [ 'js/panel.js' ];
	public $depends = [ 'yii\bootstrap\BootstrapAsset', 'yii\web\JqueryAsset' ];
}
