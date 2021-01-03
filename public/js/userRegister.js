$(document).on('change','#user_region, #user_department', function () {
    let $field = $(this)
    let $regionField = $('#user_region')
    let $userEmail = $('#user_email')
    let $password = $('#user_password')
    let $firstName = $('#user_firstName')
    let $lastName = $('#user_lastName')
    let $phoneNumber = $('#user_phoneNumber')
    let $streetNumber = $('#user_streetNumber')
    let $street = $('#user_street')

    let $form = $field.closest('form')
    let target = '#' + $field.attr('id').replace('department', 'town').replace('region', 'department')
    let data = {}

    data[$regionField.attr('name')] = $regionField.val()
    data[$field.attr('name')] = $field.val()
    data[$userEmail.attr('name')] = $userEmail.val() 
    data[$password.attr('name')] = $password.val()
    data[$firstName.attr('name')] = $firstName.val()
    data[$lastName.attr('name')] = $lastName.val()
    data[$phoneNumber.attr('name')] = $phoneNumber.val()
    data[$streetNumber.attr('name')] = $streetNumber.val()
    data[$street.attr('name')] = $street.val()




    $.post($form.attr('action'), data).then(function (data) {
            let $input = $(data).find(target)
            $(target).replaceWith($input)

    })
})