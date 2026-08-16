<?php namespace listfixer\panel;

use yii\web\AssetBundle;

class PanelAsset extends AssetBundle
{
	public $sourcePath = ( __DIR__ . '/dist' );
	public $css = [ 'css/panel.css?v=2' ];
	public $js = [ 'js/panel.js?v=2' ];
	public $depends = [ 'yii\bootstrap\BootstrapAsset', 'yii\web\JqueryAsset' ];
}
