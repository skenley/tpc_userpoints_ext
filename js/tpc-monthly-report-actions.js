(function ($, Drupal) {
  Drupal.behaviors.tpcMonthlyReportActions = {
    attach: function (context, settings) {
      
      // The 'document ready' function
      $(window).once().on('load', function () {
        
        // Apply a listener to the global actions area that will 
        // check and uncheck actions of all tenants if the global op
        // is checked/unchecked.
        $('#edit-global-actions input[type="checkbox"]')
          .change(function(){
          
            var opID = $(this).attr('value');
            var checked = $(this).is(":checked");
            var selector = '#edit-tenants-container  input[value="' 
              + opID + '"]';
            
            $(selector).prop('checked', checked);
        
          });
        
      });
      
    }
  };
})(jQuery, Drupal);