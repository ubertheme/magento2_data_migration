// Javascript on this tool
(function($){

    $(document).ready(function ($){

        //hide the message function
        $.hideMessage = function(){
            if ($('#message').length){
                $('#message').slideUp('slow');
            }
        }
        //set timeout to hide the message
        setTimeout(function(){
            $.hideMessage();
        }, 10000);

        //check/un-check website & stores on it
        $('INPUT[name="website_ids[]"]').on('change', function(){
            $('.store-group-' + this.value).prop("checked", this.checked);

            $('.store-group-' + this.value).on('change', function(){
                $('.store-' + this.value).prop("checked", this.checked);
            });
            $('.store-group-' + this.value).trigger('change');
        });
        //$('INPUT[name="website_ids[]"]').prop("checked", true);
        //$('INPUT[name="website_ids[]"]').trigger('change');

        //check/un-check product types
        $('INPUT[name="select_all_type"]').on('change', function(){
            $('INPUT[name="product_type_ids[]"]').prop('checked', this.checked);
            //We always migrate the simple products
            if ($('#product_type_simple').length) {
                $('#product_type_simple').prop('checked', true);
            }
        });

        //check/un-check product types
        $('INPUT[name="select_all_customer_group"]').on('change', function(){
            $('INPUT[name="customer_group_ids[]"]').prop('checked', this.checked);
        });

        //reset event
        $("button.reset").on('click', function(){
            if ($('INPUT[name="reset"]').length){
                $('INPUT[name="reset"]').val(1);
            }
        });

        //add disabled class after click on a button
        $(".btn").on('click', function(){
            $.showProcessorBox();
            $(this).addClass("disabled");
            if ($(this).prop("tagName") == 'A'){
                $(this).attr('disabled', true)
            }
            else if ($(this).prop("tagName") == 'BUTTON') {
                $(this).prop('disabled', true);
            }
        });

        //show/hide loading mask function
        $.showProcessorBox = function(){
            if ($('#processor-box').length){
                $('#processor-box').modal('show');
            }
        }
        $.hideProcessorBox = function(){
            if ($('#processor-box').length){
                $('#processor-box').modal('hide');
            }
        }
    });

})(jQuery);