<?php
/* template head */
/* end template head */ ob_start(); /* template body */ ?>

<script type='text/javascript'>
    $(window).load(function(){

        console.log('sss');

        $('#lazy_view :input').each(function(){
            $(this).prop('disabled', true);
            $(this).select2('disable');
        });
        $('.lazy_view :input').each(function(){
            $(this).prop('disabled', true);
            $(this).select2('disable');
        });

        $('#modulo').removeAttr('action');

        $('.lazy_view_remove').each(function(){
            $(this).remove();
        });

        $('.botao_voltar').each(function(){
            $(this).prop('disabled', false);
        });
        $('.botao_voltar').each(function(){
            $(this).html('Voltar');
        });


        $('.botao_enviar').each(function(){
            $(this).remove();
        });
    });
</script>

<?php  /* end template body */
return $this->buffer . ob_get_clean();
?>