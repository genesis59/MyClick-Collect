$(document).on('change', '#shop_region, #shop_department', function(){
    let $field = $(this)
    let $regionField = $('#shop_region')
    let $nameShop = $('#shop_nameShop')
    let $picture = $('#shop_picture')
    let $presentation = $('#shop_presentation')
    let $streetNumber = $('#shop_streetNumber')
    let $street = $('#shop_street')

    let $form = $field.closest('form')
    let target = '#' + $field.attr('id').replace('department','town').replace('region','department')
    let data = {}

    data[$regionField.attr('name')] = $regionField.val()
    data[$field.attr('name')] = $field.val()
    data[$nameShop.attr('name')] = $nameShop.val()
    data[$picture.attr('name')] = $picture.val()
    data[$presentation.attr('name')] = $presentation.val()
    data[$streetNumber.attr('name')] = $streetNumber.val()
    data[$street.attr('name')] = $street.val()



    
    $.post($form.attr('action'),data).then(function(data){
        
        let $input = $(data).find(target)
        $(target).replaceWith($input)
    })
})