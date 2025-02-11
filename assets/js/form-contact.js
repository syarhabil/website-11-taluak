// JavaScript contact form Document
$(document).ready(function() {
	$('form#main-form').submit(function() {
	$('form#main-form .error').remove();
	var hasError = false;
	$('.requiredField').each(function() {
	if(jQuery.trim($(this).val()) == '') {
    var labelText = $(this).prev('label').text();
    $(this).parent().append('<span class="error">Anda lupa memasukkan ' + labelText + '</span>');
    $(this).addClass('inputError');
    hasError = true;
    } else if($(this).hasClass('email')) {
    var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
    if(!emailReg.test(jQuery.trim($(this).val()))) {
    var labelText = $(this).prev('label').text();
    $(this).parent().append('<span class="error">Anda memasukkan ' + labelText + ' yang tidak valid</span>');
    $(this).addClass('inputError');
    hasError = true;
    }
    }
    });
    if(!hasError) {
    $('form#main-form input.submit').fadeOut('normal', function() {
    $(this).parent().append('');
    });

     $("#loader").show();
        $.ajax({
            url: "form-process.php",
            type: "POST",
            data:  new FormData(this),
            contentType: false,
            cache: false,
            processData:false,
            success: function(data){
			  $('form#main-form').slideUp("fast", function() { 
			      $(this).before('<div class="success-box success shadow-sm text-center bg-gray p-4"><img class="img-fluid" src="assets/img/support-1.png" alt=""><span>Terima kasih. Email Anda telah berhasil dikirim. <br> <i class="icofont-checked"></i></span></div>');
			      $("#loader").hide();
			  })
            }           
       });
	   
	   return false;
    }
 
   });
});
