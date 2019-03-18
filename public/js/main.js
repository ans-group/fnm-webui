$(function () {
  $('[data-toggle="tooltip"]').tooltip()
});

$(function () {
  $('[data-toggle="hoverpopover"]').popover({ trigger: "hover", html: true })
});

$(function () {
  $('[data-toggle="popover"]').popover({ html: true })
});

$('.loading').on('click', function () {
  $(this).button({loadingText: '<i class="fa fa-spinner fa-pulse"></i> &nbsp; Processing...'});
  var $btn = $(this).button('loading');
  $('.loading').addClass("disabled");
  return true;
});

$(document).on("click", ".popover .close" , function(){
    $(this).parents(".popover").popover('hide');
});

$(document).ready(function() {
    $('#datatable').DataTable();
});
