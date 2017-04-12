<?php namespace listfixer\panel;

use Yii;
use yii\bootstrap\Modal;
use yii\helpers\Html;

class Panel extends \yii\base\Widget
{
    /**
     * @var string Panel color: panel-default, panel-primary, panel-success, panel-info, panel-warning, panel-danger
	 */
	public $color = 'panel-default';
	/**
	 * @var string Panel title
	 */
	public $title = null;
	/**
	 * @var array Buttons: [ label => url ]
	 */
	public $buttonGroup = null;
	public $pills = null;
	/**
	 * @var string If empty then route is used to determine active pill.  If not empty then "p" URL parameter is used.
	 */
	public $defaultPill = null;
	/**
	 * @var boolean Used internally to trigger correct CSS class
	 */
	public $_hasFields = false;
	/**
	 * @var string Raw HTML to be included in heading
	 */
	public $headingHtml = null;
	/**
	 * @var string Submit button label
	 */
	public $submitLabel = null;
    /**
     * @var string Submit button color: btn-default, btn-primary, btn-success, btn-info, btn-warning, btn-danger
	 */
	public $submitClass = 'btn-primary';
	/**
	 * @var array Array of butttons with these keys:
	 * @var string label - Button label
	 * @var string url - Destination URL
	 * @var string class - Button color: btn-default, btn-primary, btn-success, btn-info, btn-warning, btn-danger
	 * @var string name - Name entity button will operate on
	 * @var string holder - Holder of entity button will operate on
	 * @var boolean confirm - Use modal confirm dialog
	 */
	public $rightHeaderButtons = null;
	public $leftFooterButtons = null;
	public $rightFooterButtons = null;
	/**
	 * @var boolean Used internally to trigger correct CSS class
	 */

	public function init( )
	{
		parent::init( );
		ob_start( );
	}
	
	public function run( )
	{
		PanelAsset::register( $this->view );

		$content = ob_get_clean( );

		if ( $this->_hasFields )
			echo '<div class="form-horizontal">';

		echo '<div class="panel ' . $this->color . '">';

		// Heading
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
				$this->button( $button, false );
			echo '</div>';
		}

		if ( isset( $this->buttonGroup ) )
		{
			echo '<div class="btn-group pull-right">';
			foreach ( $this->buttonGroup as $label => $destination )
				echo Html::a( $label, $destination, [ 'class' => 'btn btn-default btn-xs' ] );
			echo '</div>';
		}

		if ( isset( $this->headingHtml ) )
			echo $this->headingHtml;
	
		echo '</div></div></div>';

		// Body
		if ( $content )
			echo '<div class="panel-body">' . $content . '</div>';

		// Footer
		if ( $this->submitLabel || $this->leftFooterButtons || $this->rightFooterButtons )
		{
			echo '<div class="panel-footer"><div class="row"><div class="' . ( $this->_hasFields ? 'col-sm-9 col-sm-offset-3' : 'col-sm-12' ) . '">';

			if ( $this->submitLabel )
				echo Html::submitButton( $this->submitLabel, [ 'class' => 'btn ' . $this->submitClass ] );

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

		if ( $this->_hasFields )
			echo '</div>';
	}

	public function button( $button, $small = false )
	{
		if ( empty( $button['confirm'] ) )
			echo ' ' . Html::a( $button['label'], $button['url'], [ 'class' => 'btn ' . ( isset( $button['class'] ) ? $button['class'] : 'btn-primary' ) . ( $small ? ' btn-xs' : '' ) ] );
		else
		{
			echo ' ';

			Modal::begin( [
					'header' => $button['label'],
					'size' => 'modal-sm',
					'toggleButton' => [ 'label' => $button['label'], 'class' => 'btn ' . ( empty( $button['class'] ) ? 'btn-danger' : $button['class'] ) . ( $small ? ' btn-xs' : '' ) ],
					] );

			echo '<p>Do you want to ' . strtolower( $button['label'] );

			if ( !empty( $button['name'] ) )
				echo ' "' . $button['name'] . '"';

			if ( !empty( $button['holder'] ) )
				echo ' from "' . html::encode( $button['holder'] ) . '"';

			echo '?</p>';
			echo '<div class="btn-group btn-group-justified">';
			echo '<div class="btn-group">' . Html::a( 'Yes', $button['url'], [ 'class' => 'btn btn-danger' ] ). '</div>';
			echo '<div class="btn-group">' . Html::button( 'No', [ 'class' => 'btn btn-info', 'data-dismiss' => 'modal' ] ) . '</div>';
			echo '</div>';

			Modal::end( );
		}
	}
}
