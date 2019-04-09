# Place all the behaviors and hooks related to the matching controller here.
# All this logic will automatically be available in application.js.
# You can use CoffeeScript in this file: http://coffeescript.org/
#

calculate_collaboration = () ->
  $amount = $('.js-collaboration-amount option:selected')
  $freq = $('.js-collaboration-frequency option:selected')

  $.get "/colabora/payment_types?frequency=#{$freq.val()}", (data) ->
    $('.js-collaboration-type').find('option').remove().end()
    for payment_type in data.payment_types
      $('select[name="collaboration[payment_type]"]').append('<option value="' + payment_type[1] + '">'+payment_type[0]+'</option>')

  if $freq.val() == "0"
    $('.js-collaboration-type-form-3').hide()
    $('#select2-chosen-3').text('Suscripción con Tarjeta de Crédito/Débito')
  else if $freq.val() != ""
    $('.js-collaboration-type-form-3').show()
    $('#select2-chosen-3').text('Domiciliación en cuenta bancaria (formato IBAN)')

  if $freq.val() == ""
    $('.js-collaboration-type').hide()
    $('#collaboration_payment_type_input').hide()
  else
    $('.js-collaboration-type').show()
    $('#collaboration_payment_type_input').show()

  $other_amount = $('input[id="collaboration_other_amount"]')
  if ($amount.val() == "0")
    $other_amount.show()
    $other_amount.prop('required', true)
    $other_amount.attr("placeholder", "Cant. €");
  else
    $other_amount.hide()
    $other_amount.val('')
    $other_amount.prop('required', false)

  if (($amount.index() > 0) && ($freq.index() > 0))
    if $amount.val() != "0"
      total = $amount.val() / 100.0
    else
      total = $other_amount.val()

    switch $freq.val()
      when "0"
        message = total + " € en total pago único"
      when "1"
        message = total + " € cada mes, en total " + total * 12 + " € al año"
      when "3"
        message = total + " € cada 3 meses, en total " + total * 4 + " € al año"
      when "12"
        message = total + " € cada año en un pago único anual"
    $('.js-collaboration-alert').show()
    $('#js-collaboration-alert-amount').text(message)
  else
    $('.js-collaboration-alert').hide()

change_payment_type = (type) ->
  switch type
    when "2"
      $('.js-collaboration-type-form-3').hide()
      $('.js-collaboration-type-form-2').show('slide')
    when "3"
      $('.js-collaboration-type-form-2').hide()
      $('.js-collaboration-type-form-3').show('slide')
    else
      $('.js-collaboration-type-form-2').hide()
      $('.js-collaboration-type-form-3').hide()


init_collaborations = () ->

  must_reload = $('#js-must-reload')
  
  if (must_reload)
    if (must_reload.val()!="1")
      $("form").on 'submit', (event) ->
        must_reload.val("1")
        $("#js-confirm-button").hide()
    else
      must_reload.val("0")
      $("#js-confirm-button").hide()
      location.reload()
  
  change_payment_type($('.js-collaboration-type').val() || $('.js-collaboration-type').select2('val'))

  $('.js-collaboration-type').on 'change', (event) ->
    type = $(this).val()
    change_payment_type(type)

  calculate_collaboration()
  $('.js-collaboration-amount, .js-collaboration-frequency, .js-collaboration-other-amount').on 'change', () ->
    calculate_collaboration()

$(window).bind 'page:change', ->
  init_collaborations()

$ ->
  init_collaborations()

