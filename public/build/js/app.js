jQuery(function ($) {
    /**
     * Inputmask
     */
    $('#client_birthday').inputmask("99/99/9999");
    $('#product_price').maskMoney({thousands: '.', decimal: ',', allowZero: true});
    $('#product_price').on('focus', function () {
        $(this).maskMoney('mask');
    });
    $('#product_price').on('blur', function () {
        var that = $(this);
        that.val(parseFloat(that.maskMoney('unmasked')[0]).toFixed(2));
    });

    /**
     * Datepicker
     */
    $('#client_birthday').datepicker({
        format: 'dd/mm/yyyy',
        language: 'pt-BR',
        autoclose: true,
        todayHighlight: true,
    });

    $('#client_search').on('click', function () {
        $('#client_container_search').toggle();
    });

    $('#product_search').on('click', function () {
        $('#product_container_search').toggle();
    });

    $('#order_search').on('click', function () {
        $('#order_container_search').toggle();
    });

    /**
     * Add Item
     */

    var $newItem = $('<tr></tr>');

    jQuery(document).ready(function () {
        var $collectionHolder = $('tbody.items');
        $collectionHolder.append($newItem);
        $collectionHolder.data('index', $collectionHolder.find(':input').length);
        $('.add_order_item').on('click', function (e) {
            e.preventDefault();
            addTagForm($collectionHolder, $newItem);
        });
    });

    function addTagForm($collectionHolder, $newItem) {
        var prototype = $collectionHolder.data('prototype');
        var index = $collectionHolder.data('index');
        var newForm = prototype.replace(/__name__/g, index);
        var discount = $(newForm).find('input').eq(1).attr('id');
        $collectionHolder.data('index', index + 1);
        var $newFormLi = $('<tr></tr>').append(newForm);
        $newFormLi.append('<td><a href="#" class="remove-item btn btn-danger"><i class="fa fa-times"></i></a></td>');
        $newItem.before($newFormLi);
        $('#' + discount).maskMoney({thousands: '', decimal: '.', allowZero: true});
        // handle the removal, just for this example
        $('.remove-item').click(function (e) {
            e.preventDefault();
            $(this).closest('tr').remove();
            return false;
        });
    }

});