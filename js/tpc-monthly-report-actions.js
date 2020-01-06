(function ($, Drupal) {
  Drupal.behaviors.tpcMonthlyReportActions = {
    attach: function (context, settings) {
      
      // The 'document ready' function
      $(window).once().on('load', function () {
        
        // Observe the edit-tenants-container for changes. This is the div
        // that changes with the AJAX call to populate the tenants div
        // with tenant information.
        var target = document.querySelector('#edit-tenants-container');
        var observer = new MutationObserver(function(mutations){
          
          // Apply a listener to the global actions area that will 
          // check and uncheck actions of all tenants if the global op
          // is checked/unchecked.
          $('.global-actions .global-action input[type="checkbox"]')
            .change(function(){
            
              var opID = $(this).attr('name');
              var checked = $(this).is(":checked");
              var selector = '.tenant-wrapper .local-actions input[name="' 
                + opID + '"]';
              
              $(selector).prop('checked', checked);
          
            });
          
        });
        
        var config = { attributes: true, childList: true, characterData: true };
        observer.observe(target, config);
        
      });
      
    }
  };
})(jQuery, Drupal);