// bonne fonction d'envoie
$('.sendForm').submit(function (e) {
    e.preventDefault();

    var action = $(this).attr('action');
    var formData = new FormData(this);
    $.ajax({

        url: action,
        type: 'POST',
        data: formData,
        //async: false,
        //dataType: 'json',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        beforeSend: function () { // if form submit
            loader();
        },
        success: function (data) {
            loader('hide');
            if (data.success) {//if formData forme is very good
                sendSuccess(data.message, data.urlback);
            } else {
                loader('hide');
                SendError(data.message);
            }
        },
        error: function (data) {
            loader('hide');
            SendError(data.responseJSON.message ?? 'Une erreur est survenue');
        },
        cache: false,
        contentType: false,
        processData: false
    });
});

$('.sendConfirmForm').submit(function (e) {
    e.preventDefault();

    Swal.fire({
        icon: 'warning',
        title: 'Confirmation !',
        text: "Vous êtes sur le point d\'ouvrir un nouveau ticket pour signaler un problème. Voulez-vous continuer ?",
        showDenyButton: true,
        showCancelButton: false,
        confirmButtonText: "Oui, ouvrir le ticket",
        denyButtonText: `Non, annuler`
        }).then((result) => {
        if (result.isConfirmed) {

            var action = $(this).attr('action');
            var formData = new FormData(this);
            $.ajax({

                url: action,
                type: 'POST',
                data: formData,
                //async: false,
                //dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                beforeSend: function () { // if form submit
                    loader();
                },
                success: function (data) {
                    loader('hide');
                    if (data.success) {//if formData forme is very good
                        sendSuccess(data.message, data.urlback);
                    } else {
                        loader('hide');
                        SendError(data.message);
                    }
                },
                error: function (data) {
                    loader('hide');
                    SendError(data.responseJSON.message ?? 'Une erreur est survenue');
                },
                cache: false,
                contentType: false,
                processData: false
            });
        } else if (result.isDenied) {
            Swal.fire({
                icon: 'success',
                title: "Eh bien tout va bien alors 😉",
                showConfirmButton: true,
            });
        }
    });
});

$(".sendLink").on('click', function (e) {

    e.preventDefault();
    var action = $(this).attr('href');
    var caption = $(this).attr('caption');

    Swal.fire({
        icon : 'warning',
        title: 'Attention !',
        text: caption ? caption : 'Vous êtes sur le point d\'effectuer un changement',
        showDenyButton: true,
        showCancelButton: false,
        confirmButtonText: `OUI, CONTINUER`,
        denyButtonText: `NON, FERMER`,
    }).then((result) => {
        /* Read more about isConfirmed, isDenied below */
        if (result.isConfirmed) {

            $.ajax({

                url: action,
                type: 'GET',
                // data: formData,
                dataType: 'json',
                // headers: {
                //     'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                // },
                beforeSend: function () { // if form submit
                    loader();
                },
                success: function (data) {
                    if (data.success) {//if formData forme is very good

                        sendSuccess(data.message, data.urlback);
                    } else {
                        SendError(data.message);
                    }
                },
                error: function (data) {
                    if (data.type == "error") {// if error occured
                        SendError(data.responseJSON.message ?? 'Une erreur est survenue');
                    }
                },
                cache: false,
                contentType: false,
                processData: false
            });

        } else if (result.isDenied) {
            Swal.close()
        }
    })

});

$('.role-permission-input').on('change', function() {
    let permission = $(this).val();

    $.ajax({
        url: '/panel/settings/permissions/' + permission + '/change',
        type: 'POST',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: {
            permission: permission,
        },
        beforeSend: function() {
            loader();
        },
        success: function(data) {
            Swal.close();
            // if (data.success) {
            //     sendSuccess(data.message);
            // } else {
            //     SendError(data.message);
            // }
        },
        error: function(data) {
            SendError(data.responseJSON.message ?? 'Une erreur est survenue');
        }
    });
});

$('#ticketCategoryUuid').on('change', function() {
    let category_uuid = $(this).val();

    $.ajax({
        url: '/api/category/' + category_uuid + '/sub-category/find-all',
        type: 'GET',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: {
            category_uuid: category_uuid,
        },
        beforeSend: function() {
            // $('#ticketSubCategory').addClass('d-none');
            loader();
        },
        success: function(data) {
            Swal.close();
            if (data.success) {
                // Mettre à jour les options du select
                if(data.data.length > 0) {
                    let options = '';
                    data.data.forEach(function(subCategory) {
                        options += '<option value="' + subCategory.uuid + '">' + subCategory.name + '</option>';
                    });
                    $('#ticketSubCategory').html(options);
                    // $('#ticketSubCategory').removeClass('d-none');
                }
            } else {
                SendError(data.message);
            }
        },
        error: function(data) {
            SendError(data.responseJSON.message ?? 'Une erreur est survenue');
        }
    });
});

function loader(state = "show") {
    switch (state) {
        case "show":
            /*JsLoadingOverlay.show({
                'overlayBackgroundColor': '#666666',
                'overlayOpacity': 0.4,
                'spinnerIcon': 'ball-spin',
                'spinnerColor': '#01a358',
                'spinnerSize': '1x',
                'overlayIDName': 'overlay',
                'spinnerIDName': 'spinner',
                'spinnerZIndex': 99999,
                'overlayZIndex': 99998,
                'lockScroll': true,
            });*/

            Swal.fire({
                title: 'Chargement en cours...',
                showConfirmButton: false,
                allowOutsideClick: true,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            break;
        default:
            Swal.close()
           // JsLoadingOverlay.hide();
            break;
    }

}

function sendSuccess(message, urlback=''){ // retour en cas de success d'envoi de formulaire
    if (urlback !== '') {
        if(urlback === 'back'){
            Swal.fire({
                icon: 'success',
                title: 'Succès ...',
                text: message,
                showConfirmButton: false,
            });
            //Si url de retour exist
            setTimeout(() => {
                location.reload();
            }, 2000);
        }else {
            //Si url de retour exist
            Swal.fire({
                icon: 'success',
                title: 'Succès ...',
                text: message,
                showConfirmButton: false,
            });
            setTimeout(() => {
                window.location.href = urlback;
            }, 2000);
        }
    }
    else {
        //Si url de retour exist pas dans le retour du formulaire
        Swal.fire({
            icon: 'success',
            title: 'Succès ...',
            text: message,
        });
    }

}

// charger un fichier

function SendError(messageError){ //fonction pour envoi de formulaire chargement loading
    Swal.fire({
        icon: 'error',
        title: 'Erreur ...',
        text: messageError,
    });
} //fin de la focntion SendError


$('#ticketSubject').on('click', function() {

    $.ajax({
        url: '/api/subjects/find-all',
        type: 'GET',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        beforeSend: function() {
            $('#exampleModalFullscreen').modal('show');
            $('#subjects').html(`
                <tr>
                    <td colspan="2">Chargement en cours...</td>
                </tr>
            `);
        },
        success: function(data) {
            Swal.close();
            $('#subjects').html('');
            if (data.success) {
                // Mettre à jour les options du select
                if(data.data.length > 0) {
                    let options = '';
                    data.data.forEach(function(subject) {
                        options += `
                            <tr>
                                <th>${subject.name}</th>
                                <td>
                                    <button type="button" class="btn btn-icon btn-outline-success select-subject" data-slug="${subject.slug}" data-uuid="${subject.uuid}" data-name="${subject.name}">
                                        <i class="ti ti-hand-finger"></i>
                                    </button>
                                </td>
                            </tr>`
                    });
                    $('#subjects').html(options);

                    $('#subjectsTable').DataTable();
                }
            } else {
                SendError(data.message);
            }
        },
        error: function(data) {
            SendError(data.responseJSON.message ?? 'Une erreur est survenue');
        }
    });

    $('#exampleModalFullscreen').modal('show');
});

$('#subjects').on('click', '.select-subject', function() {
    let subjectSlug = $(this).data('slug');
    let subjectUuid = $(this).data('uuid');
    let subjectName = $(this).data('name');


    $('#exampleModalFullscreen').modal('hide');
    $('#create-ticket-name').val(subjectName);
    $('#ticketCategoryUuid').val(subjectUuid);

    $('#subject-uuid').val(subjectUuid);


    if(subjectSlug === 'autre') {
        $('#other-subject-field').removeClass('d-none');
    }else{
        $('#ticketOtherSubject').val('');
        $('#create-ticket-name').val(subjectName);
        $('#other-subject-field').addClass('d-none');
    }
});

//############### CAMPAIGNS ###############


