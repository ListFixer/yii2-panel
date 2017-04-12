<?php namespace listfixer\panel;

use yii\bootstrap\Modal;
use yii\helpers\Html;

class PanelButton extends \yii\base\Widget
{
   /**
    * @var string Button label
    */
   public $label;
   /**
    * @var string Destination URL
    */
   public $url;
   /**
    * @var string Button color: btn-default, btn-primary, btn-success, btn-info, btn-warning, btn-danger
    */
   public $color = 'btn-primary';
   /**
    * @var string Name entity button will operate on
    */
   public $name = null;
   /**
    * @var string Holder of entity button will operate on
    */
   public $holder = null;
   /**
    * @var boolean Use modal confirm dialog
    */
   public $confirm = false;
   /**
    * @var boolean Small Button
    */
   public $small = true;

   public function run( )
   {
      ob_start( );

      if ( empty( $this->confirm ) )
         echo ' ' . Html::a( $this->label, $this->url, [ 'class' => 'btn ' . $this->color . ( $this->small ? ' btn-xs' : '' ) ] );
      else
      {
         echo ' ';

         Modal::begin( [
               'header' => $this->label,
               'size' => 'modal-sm',
               'toggleButton' => [ 'label' => $this->label, 'class' => 'btn ' . $this->color . ( $this->small ? ' btn-xs' : '' ) ],
               ] );

         echo '<p>Do you want to ' . strtolower( $this->label );

         if ( !empty( $this->name ) )
            echo ' "' . $this->name . '"';

         if ( !empty( $this->holder ) )
            echo ' from "' . html::encode( $this->holder ) . '"';

         echo '?</p>';
         echo '<div class="btn-group btn-group-justified">';
         echo '<div class="btn-group">' . Html::a( 'Yes', $this->url, [ 'class' => 'btn btn-danger' ] ). '</div>';
         echo '<div class="btn-group">' . Html::button( 'No', [ 'class' => 'btn btn-info', 'data-dismiss' => 'modal' ] ) . '</div>';
         echo '</div>';

         Modal::end( );
      }

      return ob_get_clean( );
   }
}
