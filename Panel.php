<?php namespace listfixer\panel;

use Yii;
use yii\helpers\Html;

class Panel extends \yii\base\Widget
{
	/**
	 * @var string Color: panel-default, panel-primary, panel-success, panel-info, panel-warning, panel-danger
	 */
	public $color = 'panel-default';
	/**
	 * @var string Title
	 */
	public $title = null;
	/**
	 * @var array Array of PanelButtton
	 */
	public $rightHeaderButtons = null;
	/**
	 * @var array Buttons: [ label => url ]
	 */
	public $buttonGroup = null;
	public $leftFooterButtons = null;
	public $rightFooterButtons = null;
	public $pills = null;
	/**
	 * @var string If empty then route is used to determine active pill.  If not empty then "p" URL parameter is used.
	 */
	public $defaultPill = null;
	/**
	 * @var string Raw HTML to be included in header
	 */
	public $headerHtml = null;
	/**
	 * @var string Submit button label
	 */
	public $submitLabel = null;
    	/**
     	 * @var string Color: btn-default, btn-primary, btn-success, btn-info, btn-warning, btn-danger
	 */
	public $submitColor = 'btn-primary';
	/**
	 * @var array Buttons: [ label => value ]
	 */
	public $submitButtons = null;
	/**
	 * @var string Body class
	 */
	public $bodyClass = 'panel-body';
	/**
	 * @var string Form class
	 */
	public $formClass = 'form-horizontal';
	/**
	 * @var boolean Used internally to trigger correct CSS class
	 */
	public $_hasFields = false;

	public function init( )
	{
		parent::init( );
		ob_start( );
	}
	
	public function run( )
	{
		PanelAsset::register( $this->view );

		$content = ob_get_clean( );

		if ( $this->_hasFields && !empty( $this->formClass ) )
			echo '<div class="' . $this->formClass . '">';

		echo '<div class="panel ' . $this->color . '">';

		// Heading
		if ( $this->title || $this->pills || $this->rightHeaderButtons || $this->buttonGroup || $this->headerHtml )
			echo '<div class="panel-heading"><div class="row"><div class="col-sm-12">';

		if ( $this->title )
			echo '<div class="panel-title">' . $this->title . '</div>';
		elseif ( $this->pills )
		{
			if ( !empty( $this->defaultPill ) )
			{ 
				$current_pill = yii::$app->request->get( 'p', $this->defaultPill );
				foreach ( $this->pills as $pill )
					echo Html::a( $pill['label'], $pill['url'], [ 'class' => ( $current_pill == $pill['url']['p'] ? 'btn btn-primary' : 'btn btn-default' ) ] ) . ' ';
			}
			else
			{
				$here = '/' . Yii::$app->controller->module->requestedRoute;
				foreach ( $this->pills as $pill )
					echo Html::a( $pill['label'], $pill['url'], [ 'class' => ( $pill['url'][0] == $here ? 'btn btn-primary' : 'btn btn-default' ) ] ) . ' ';
			}
		}

		if ( $this->rightHeaderButtons )
		{
			echo '<div class="pull-right" style="margin-left: 8px;">';
			foreach ( $this->rightHeaderButtons as $button )
			{
				$button['small'] = false;
				echo PanelButton::widget( $button );
			}
			echo '</div>';
		}

		if ( $this->buttonGroup )
		{
			echo '<div class="btn-group pull-right">';
			foreach ( $this->buttonGroup as $label => $destination )
				echo Html::a( $label, $destination, [ 'class' => 'btn btn-default btn-xs' ] );
			echo '</div>';
		}

		if ( $this->headerHtml )
			echo $this->headerHtml;
	
		if ( $this->title || $this->pills || $this->rightHeaderButtons || $this->buttonGroup || $this->headerHtml )
			echo '</div></div></div>';

		// Body
		if ( $content )
			echo '<div class="' . $this->bodyClass . ( $this->_hasFields ? ' panel-fields' : '' ) . '">' . $content . '</div>';

		// Footer
		if ( $this->submitLabel || $this->submitButtons || $this->leftFooterButtons || $this->rightFooterButtons )
		{
			echo '<div class="panel-footer"><div class="row"><div class="' . ( $this->_hasFields ? 'col-sm-9 col-sm-offset-3' : 'col-sm-12' ) . '">';

			if ( $this->submitLabel )
				echo Html::submitButton( $this->submitLabel, [ 'class' => 'btn ' . $this->submitColor ] );
			
			if ( $this->submitButtons )
				foreach( $this->submitButtons as $label => $value )
					echo Html::submitButton( $label, [ 'class' => 'btn btn-primary', 'name' => 'button', 'value' => $value ] ) . ' ';

			if ( $this->leftFooterButtons )
				foreach ( $this->leftFooterButtons as $label => $destination )
					echo Html::a( $label, $destination, [ 'class' => 'btn btn-primary' ] ) . ' ';

			if ( $this->rightFooterButtons )
			{
				echo '<div class="btn-group pull-right">';
				foreach ( $this->rightFooterButtons as $label => $destination )
					echo Html::a( $label, $destination, [ 'class' => 'btn btn-default' ] );
				echo '</div>';
			}

			echo '</div></div></div>';
		}

		echo '</div>';

		if ( $this->_hasFields && !empty( $this->formClass )  )
			echo '</div>';
	}
}
