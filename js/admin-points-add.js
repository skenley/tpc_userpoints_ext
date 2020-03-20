(function ($, Drupal) {
  Drupal.behaviors.tpcAdminPointsAdd = {
    attach: function (context, settings) {
      
      // The 'document ready' function
      $(window).once().on('load', function () {
        
        // #edit-field-userpoints-default-amount-0-value
        $('#edit-operation').change(function(){
          
          var selectedOp = $(this).val();
          console.log(selectedOp);
          
          if(selectedOp === 'userpoints_default_admin') {
            
            $('#edit-field-userpoints-default-amount-0-value')
              .removeAttr('readonly');
            
          }
          else {
            
            if($('#edit-field-userpoints-default-amount-0-value')
              .attr('readonly') === undefined) {
              
              $('#edit-field-userpoints-default-amount-0-value')
                .attr('readonly', 'readonly');
              
            }
            
          }
          
        });
        
      });
      
    }
  };
})(jQuery, Drupal);