function myFunction(e) {   
    
    // Check if Elementor plugin is installed
    jQuery.post(ajaxurl, { action: 'check_elementor_plugin' }, function(response) {        
         if ((response && response.elementor && e == "Elementor") || (response && response.divi && e == "Divi") || (response && response.bricks && e == "Bricks") || (response && e == "Gutenberg")) {                       
            // Proceed with your AJAX action         
            var ajax_nonce = fluxtore_script_vars.ajax_nonce;
            jQuery.ajax({
                type: 'POST',
                url: fluxtore_script_vars.ajax_url,
                data: {
                    action: 'my_ajax_action',
                    option: e,
                    security: ajax_nonce,
                },
                success: function(response) {
                    console.log(response);
                    // Add code to handle the success response here
                },
                error: function(xhr, status, error) {
                    console.error(error);
                    // Add code to handle the error response here
                }
            });
        } else {    
            jQuery('#builder-selection-error').show();        
            if(e =='Elementor'){               
                // Show alert to inform the user
                //alert('You need to install Elementor before choosing this option.'); 
                // let dialog = document.getElementById('error-dialog-ctm');
                // dialog.innerHTML = '<div class="inline-block align-bottom bg-white text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full opacity-100 translate-y-0 sm:scale-100"><div>You need to install Elementor before choosing this option.</div><form method="dialog"><button class="custom-dialog-button w-full inline-flex justify-center border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-600 sm:ml-3 sm:w-auto sm:text-sm">OK</button></form></div>';
                // dialog.showModal();
                
                showToast(
                    { 
                      eleWrapper: '#builder-selection-error',
                      msg: 'You need to install Elementor before choosing this option.',
                      theme: 'error'
                    } 
                  );               
            }  
            if(e == 'Divi'){
                //alert('You need to install Divi before choosing this option.');
                showToast(
                    { 
                      eleWrapper: '#builder-selection-error',
                      msg: 'You need to install Divi Theme before choosing this option.',
                      theme: 'error'
                    } 
                  );
            }        
            if(e == 'Bricks'){
                //alert('You need to install Bricks before choosing this option.');
                showToast(
                    { 
                      eleWrapper: '#builder-selection-error',
                      msg: 'You need to install Bricks Theme before choosing this option.',
                      theme: 'error'
                    } 
                  );
            } 
        }
    });
}


 function redirectToNewFunnel(a) {
    window.location.href = a;
    location.reload();
}

const toggleActive = (editor) => {   
    const buttons = document.querySelectorAll('.edit-link');
    buttons.forEach(button => {
      if (button.textContent.includes(editor)) {
        button.classList.add('active');
      } else {
        button.classList.remove('active');
      }
    });
  };


  //Show Toast Function
  function showToast(option) {
    var wrapper = jQuery(option.eleWrapper);
    var toast = createToast(option);
    toast = jQuery(toast).hide().fadeIn(750);
    var watch;
  
    if (option.autoClose) {
      var outTime = option.autoCloseTime || 3500;
      if (outTime < 1000) {
        outTime = 1000;
      }
      watch = setTimeout(function() {
        toast.animate({ 'margin-top': '-50px', 'opacity': '0' }, 500, function() {
          jQuery(this).remove();
          if (option.afterClose) {
            option.afterClose();
          }
        });
      }, outTime);
    }
  
    jQuery(wrapper).on('click', '.hs-close', function() {
      clearTimeout(watch);
      var toastElement = jQuery(this).closest('.hs-toast');
      toastElement.fadeOut(750, function() {
        jQuery(this).remove();
        if (option.afterClose) {
          option.afterClose();
        }
      });
    });
  
    jQuery(wrapper).html(toast);
    if (option.afterShow) {
      option.afterShow();
    }
  
    setTimeout(function() {
      toast.fadeOut(750, function() {
        jQuery(wrapper).html("");
      });
    }, 5000);
  }
  
  function createToast(option) {
    var final = toastCaseValidation(option);
    var html = `
      <div class="hs-toast hs-theme-` + (option.theme).toLowerCase() + `">
        <div class="hs-toast-inner">
          <div class="hs-toast-msg">
            ` + final.msg + `
          </div>
          <div class="hs-toast-action">
            ` + final.icon + `
          </div>
        </div>
      </div>`;
    return html;
  }
  
  function toastCaseValidation(option) {
    var finalOption = {};
    var toastmsg;
    var themeIco;
    var closeBtn = '<button type="button" class="hs-close">&#10006;</button>';
  
    themeIco = '<svg aria-hidden="true" focusable="false"  xmlns="http://www.w3.org/2000/svg" width="1.875em" height="1.875em" viewBox="0 0 30 30"> <circle fill="none" stroke="#fff" stroke-width="2"  cx="50%" cy="50%" r="13" stroke-dasharray="100"> <animate attributeName="stroke-dashoffset" from="100" to="0" dur="0.9s" /> </circle> <line fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round"  x1="10.5" y1="10.5" x2="19.5" y2="19.5" stroke-dasharray="100"> <animate attributeName="stroke-dashoffset"  from="100" to="0" dur="4s" /> </line> <line fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round"  x1="19.5" y1="10.5" x2="10.5" y2="19.5" stroke-dasharray="100"> <animate attributeName="stroke-dashoffset"  from="100" to="0" dur="4s" /> </line> </svg>';
  
    if (option.closeButton == false) {
      closeBtn = '';
    }
  
    if (option.msg == undefined) {
      toastmsg = 'No Message';
    } else {
      if (option.msg.length != 1 && typeof option.msg === "object") {
        toastmsg = '<ul>';
        option.msg.forEach(function(val, index) {
          toastmsg = toastmsg + '<li>' + val + '</li>';
        });
        toastmsg = toastmsg + '</ul>';
      } else {
        toastmsg = option.msg;
      }
    }
  
    finalOption.icon = themeIco;
    finalOption.close = closeBtn;
    finalOption.msg = toastmsg;
    return finalOption;
  }
  