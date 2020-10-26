$(function () {
    $('.js-use').on('click', function () {
        let $this = $(this),
            id = $this.data('id'),
            st = $this.data('st');

        $.post('/categories/used', {id: id, st: st}, function () {
            location.reload();
        })
    });
});