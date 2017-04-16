<?php namespace listfixer\panel;

use Yii;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;

use kartik\date\DatePicker;
use kartik\typeahead\Typeahead;
use listfixer\panel\Panel;

class PanelForm extends \yii\widgets\ActiveForm
{
	public static function begin( $config = [ ] )
	{
		$defaultFormConfig['fieldConfig']['template'] = '{label}<div class="col-sm-5">{input}{hint}</div><div class="col-sm-4">{error}</div>';
		$defaultFormConfig['fieldConfig']['labelOptions'] = ['class' => 'col-sm-3 control-label'];

		if ( isset( $config['formConfig'] ) )
			$formConfig = array_merge( $defaultFormConfig, $config['formConfig'] );
		else
			$formConfig = $defaultFormConfig;

		$form = parent::begin( $formConfig );

		$config['_hasFields'] = true;

		Panel::begin( $config );

		return $form;
	}

	public static function end( )
	{
		Panel::end( );
		parent::end( );
	}

	public function autoSearchField( $model, $field_id, $field_name, $url, $options = [ ] )
	{
		$id_html_id = Html::getInputId( $model, $field_id );
		$name_html_id = Html::getInputId( $model, $field_name );

		$config['limit'] = 15;
		$config['display'] = 'value';
		$config['remote'] = [ 'url' => Url::to( $url ) . '?q=%Q%', 'wildcard' => '%Q%' ];

		if ( !empty( $options['template'] ) )
		{
			$template = $options['template'];
			$config['templates'] = [ 'suggestion' => new JsExpression( "Handlebars.compile( '{$template}' )" ) ];
		}

		if ( $errors = $model->getErrors( $field_id ) )
         		$model->addErrors( [ $field_name => $errors ] );

		echo $this->field( $model, $field_id, [ 'template' => '{input}', 'options' => [ 'tag' => 'span', 'class' => null ] ] )->hiddenInput( )->label( false );

		return $this->field( $model, $field_name )->widget( Typeahead::classname( ), [
			'dataset' => [ $config ],
			'useHandleBars' => !empty( $options['template'] ),
			'pluginOptions' => [ 'highlight' => true, 'minLength' => 4 ],
			'pluginEvents' => [
				'blur' => 'function( ) { if ( document.getElementById( "' . $name_html_id . '" ).value == "" ) document.getElementById( "' . $id_html_id . '" ).value = save_' . $field_name . ' = ""; else document.getElementById( "' . $name_html_id . '" ).value = ( typeof save_' . $field_name . ' == "undefined" ? "" : save_' . $field_name . ' ); }',
				'typeahead:select' => 'function( event, ui ) { document.getElementById( "' . $id_html_id . '" ).value = ui.id; save_' . $field_name . ' = ui.value; }'
			],
			'options' => [ 'placeholder' => 'Auto Search' ]
		] );
	}
	
	public function checkboxField( $model, $field_name, $options = [ ] )
	{
		$options['class'] = 'checkbox-adjust';

		return $this->field( $model, $field_name )->checkbox( $options, false );
	}

	public function checkboxGroup( $model, $name, $fields )
	{
		$html = '<div class="form-group' . ( empty( $model->errors[$name] ) ? '' : ' has-error' ) . '">';

		$html .= '<label class="col-sm-3 control-label">Levels</label>';
		$html .= '<div class="col-sm-5"><div class="form-control" style="height: auto;">';

		foreach ( $fields as $field => $label )
			$html .= $this->field( $model, $field, [ 'options' => [ 'tag' => 'span' ], 'template' => '<div style="display: inline-block;">{input} ' . $label . ' </div>' ] )->checkBox( [ ], false );

		$html .= '</div></div>';

		if ( !empty( $model->errors[$name] ) )
			$html .= '<div class="col-sm-4"><div class="help-block">' . $model->errors[$name][0] . '</div></div>';

		$html .= '</div>';

		return $html;
	}

	public function datePickerField( $model, $field_name )
	{
		return $this->field( $model, $field_name )->widget( DatePicker::classname( ), [
			'type' => DatePicker::TYPE_INPUT,
			'convertFormat' => true,
			'options' => [ 'placeholder' => 'MM/DD/YYYY' ],
			'pluginOptions' => [
				'forceParse' => true,
				'autoclose' => true,
				'format' => 'php:n/j/Y'
			]
		] );
	}

	public function display( $model, $field_name, $options = [ ] )
	{
		$value = ( empty( $options['value'] ) ? $model->{$field_name} : $options['value'] );

		return $this->heading( $model->getAttributeLabel( $field_name ), $value, $options );
	}

	public function heading( $label, $value )
	{
		return '<div class="form-group"><label class="col-sm-3 control-label">' . $label . '</label>' .
				'<div class="col-sm-5"><div class="panel-field">' . $value . '</div></div></div>';
	}
}




