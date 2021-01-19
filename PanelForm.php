<?php namespace listfixer\panel;

use Yii;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;

use kartik\date\DatePicker;
use kartik\file\FileInput;
use kartik\typeahead\Typeahead;
use listfixer\panel\Panel;

class PanelForm extends \yii\widgets\ActiveForm
{
	public static function begin( $config = [ ] )
	{
		$defaultFormConfig['fieldConfig']['template'] = '{label}<div class="col-sm-5">{input}{hint}</div><div class="col-sm-4">{error}</div>';
		$defaultFormConfig['fieldConfig']['labelOptions'] = ['class' => 'col-sm-3 control-label'];

		if ( isset( $config['formConfig'] ) )
		{
			$formConfig = array_merge( $defaultFormConfig, $config['formConfig'] );
			unset( $config['formConfig'] );
		}
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
			'pluginOptions' => [ 'highlight' => true, 'minLength' => ( empty( $options['minLength'] ) ? 3 : $options['minLength'] ) ],
			'pluginEvents' => [
				'blur' => 'function( ) { if ( document.getElementById( "' . $name_html_id . '" ).value == "" ) document.getElementById( "' . $id_html_id . '" ).value = save_' . $field_name . ' = ""; else document.getElementById( "' . $name_html_id . '" ).value = ( typeof save_' . $field_name . ' == "undefined" ? "" : save_' . $field_name . ' ); }',
				'typeahead:select' => 'function( event, ui ) { document.getElementById( "' . $id_html_id . '" ).value = ui.id; save_' . $field_name . ' = ui.value; }'
			],
			'options' => [ 'placeholder' => 'Auto Search', 'autocomplete' => 'off' ]
		] );
	}
	
	public function checkboxField( $model, $field_name, $options = [ ] )
	{
		$options['class'] = 'checkbox-adjust';

		return $this->field( $model, $field_name )->checkbox( $options, false );
	}

	public function checkboxGroup( $model, $name, $fields, $group_label = null )
	{
		$html = '<div class="form-group' . ( empty( $model->errors[$name] ) ? '' : ' has-error' ) . '">';

		$html .= '<label class="col-sm-3 control-label">' . $group_label . '</label>';
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
			'options' => [ 'placeholder' => 'MM/DD/YYYY', 'autocomplete' => 'off' ],
			'pluginOptions' => [
				'forceParse' => true,
				'autoclose' => true
			]
		] );
	}

	public function display( $model, $field_name, $options = [ ] )
	{
		$value = ( empty( $options['value'] ) ? $model->{$field_name} : $options['value'] );

		if ( empty( $value ) ) return;

		return $this->heading( $model->getAttributeLabel( $field_name ), $value, $options );
	}

	public function fileInputField( $model, $max_file_size_mb, $url, $options )
    	{
        	echo '<script>function upload_error( data ) { if ( data.response.error === undefined ) msg = "Your import did NOT complete."; else msg = data.response.error; ' .
                	'jQuery( "#upload" ).empty( ).append( "<div class=\"panel-field\">" + msg + "</div>" ); }</script>';

        	return $this->field( $model, 'file', [ 'template' => '{label}<div id=upload class="col-sm-9">{input}</div>{error}' ] )
                	->widget( FileInput::classname( ), [
	                	'pluginLoading' => false,
        	        	'options' => $options,
                		'pluginEvents' => [
					'filebatchuploadsuccess' => 'function( ) { jQuery( "#upload" ).empty( ).append( "<div class=\"panel-field\">Upload complete.</div>" ); }',
                   			'filebatchuploaderror' => 'function( event, data ) { upload_error( data ); }',
                   			'fileerror' => 'function( event, data ) { upload_error( data );}',
                   			'filebrowse' => 'function( event ) { jQuery( "#trialresult-file" ).fileinput( "clear" ); }',
                   			'fileuploaderror' => 'function( event, data ) { upload_error( data ); }',
                		],
                		'pluginOptions' => [
					'buttonLabelClass' => '',
                   			'uploadAsync' => false,
                   			'showPreview' => false,
                   			'showRemove' => false,
                   			'minFileCount' => 1,
                   			'maxFileCount' => 1,
                   			'browseIcon' => '',
                   			'uploadIcon' => '',
                   			'uploadClass' => 'btn btn-success',
                   			'cancelIcon' => '',
                   			'cancelClass' => 'btn btn-danger',
                   			'maxFileSize' => $max_file_size_mb * 1024 * 1024,
                   			'uploadUrl' => Url::to( $url ),
                   			'layoutTemplates' => [
                      				'main1' => '<div class="kv-upload-progress"></div><div class="input-group {class}">{caption}<div class="input-group-btn">{browse}</div></div>' .
                                  			'<div class="upload-btns">{upload}{remove}{cancel}</div>',
                      				'progress' => '<div class="progress"><div class="progress-bar progress-bar-primary text-center" style="width:{percent}%;">{percent}%</div></div>'
                   			]
                		]
            		] );
    	}
	
	public function heading( $label, $value )
	{
		return '<div class="form-group"><label class="col-sm-3 control-label">' . $label . '</label>' .
				'<div class="col-sm-5"><div class="panel-field">' . $value . '</div></div></div>';
	}
}
