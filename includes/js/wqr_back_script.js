jQuery(document).ready(function(){
    jQuery("body").on('click', '.wqrremove', function(){
        jQuery(this).parent().parent().remove();
    });
});


