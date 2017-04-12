<?php namespace listfixer\panel;

use Yii;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

class PanelDisplay extends \listfixer\panel\Panel
{
	public function field( $model, $field_name, $options = [ ] )
	{
		$this->_hasFields = true;

		$buttons = ( empty( $options['buttons'] ) ? [ ] : $options['buttons'] );
		$format = ( empty( $options['format'] ) ? 'text' : $options['format'] );
		$url = ( empty( $options['url'] ) ? '' : $options['url'] );
		$value = html::encode( ( empty( $options['value'] ) ? $model->{$field_name} : $options['value'] ) );

		if ( empty( $value ) && $format != 'boolean' ) return;

		echo '<div class="form-group">';	
		echo '<label class="col-sm-3 control-label">' . $model->getAttributeLabel( $field_name ) . '</label>';
		echo '<div class="col-sm-9"><div class="panel-field">';

		$info = Yii::$app->formatter->format( $value, $format );

		echo ( empty( $url ) ? $info : Html::a( $info, $url ) );

		foreach ( $buttons as $button )
			$this->button( $button, true );
		
		echo '</div></div></div>';
	}

	public function grid( $dataProvider, $columns, $options = [ ] )
	{
		if ( empty( $options['action'] ) )
			$tableOptions = [ 'class' => 'table table-bordered' ];
		elseif ( empty( $options['parm'] ) )
			$tableOptions = [ 'class' => 'table table-bordered table-hover lf-links', 'data-action' => $options['action'] ];
		else
			$tableOptions = [ 'class' => 'table table-bordered table-hover lf-links', 'data-action' => $action, 'data-parm' => $options['parm'] ];

		echo GridView::widget( [
			'dataProvider' => $dataProvider,
			'showFooter' => !empty( $options['showFooter'] ),
			'tableOptions' => $tableOptions,
			'rowOptions' => ( empty( $options['rowOptions'] ) ? false : $options['rowOptions'] ),
			'pager' => [ 'maxButtonCount' => 6 ],
			'layout' => '{pager}{items}',
			'columns' => $columns,
			'summary' => ''
		] );
	}

	public function heading( $label, $value )
	{
		$this->_hasFields = true;

		echo '<div class="form-group"><label class="col-sm-3 control-label">' . $label . '</label>';
		echo '<div class="col-sm-9"><div class="panel-field">';
		echo $value . '</div></div></div>';
	}

	public function search( $search )
	{
		$form = ActiveForm::begin( [
			'method' => 'get',
			'action' => [ null, 'p' => Yii::$app->request->get( 'p' ) ],
			'options' => [ 'class' => 'navbar-form navbar-right index-search-form' ]
		] );

		echo $form->field( $search, 'search', [
			'options' => [ 'class' => 'input-group' ],
			'template' => '{input}<span class="input-group-btn">' . Html::submitButton( 'Go!', [ 'class' => 'btn btn-default' ] ) . '</span>',
			'inputOptions' => [ 'class' => 'form-control', 'placeholder' => 'Search' ]
		] )->label( false );

		ActiveForm::end( );
	}

	public function notes( $model = null, $notes )
	{
		echo '<ul class="list-group">';
		echo '<li class="list-group-item"><strong>Notes:</strong></li>';

		foreach ( $notes as $note )
		{
			echo '<li class="list-group-item">';
			echo Yii::$app->formatter->format( $note->created, 'datetime' );
			echo ' <span class="label label-info">' . Html::encode( $note->name ) . '</span> ' . Html::encode( $note->note );
			echo '</li>';
		}

		if ( $model )
		{
			echo '<li class="list-group-item">';

			$form = ActiveForm::begin( );
			echo $form->field( $model, 'note', [
					'options' => [ 'class' => 'input-group' ],
					'template' => '{input}<span class="input-group-btn">' . Html::submitButton( 'Add', [ 'class' => 'btn btn-default' ] ) . '</span>',
					'inputOptions' => [ 'class' => 'form-control', 'placeholder' => 'New Note' ]
					] )->label( false );
			ActiveForm::end( );

			echo '</li>';
		}

		echo '</ul>';
	}
}