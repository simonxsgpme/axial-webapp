$('.sendMissionForm').submit(function (e) {
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
                $('.sendMissionForm').trigger('reset');
                $('.modal').modal('hide');
                findAllMissions();
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

function findAllMissions(url) {
    $.ajax({
        url: url,
        type: 'GET',
        beforeSend: function() {
            $('#missions').html(`
                <div class="text-center mt-5 mb-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Chargement des missions ...</span>
                    </div>
                </div>
            `);
        },
        success: function (data) {
            let options = '';
            data.data.forEach(function(mission) {
                options += `
                <li class="mail-list-item checkable-item ${mission.status == 'validated' ? 'mail-unread' : ''}">
                    <div class="form-check my-0 me-2">
                        ${mission.status === 'to_validate' ? '<span class="badge bg-primary text-white me-1"><i class="fa fa-spinner fa-spin"></i></span>' : ''}
                        ${mission.status === 'validated' ? '<span class="badge bg-success text-white me-1"><i class="fa fa-check"></i></span>' : ''}
                        ${mission.status === 'failed' ? '<span class="badge bg-danger text-white me-1"><i class="fa fa-times"></i></span>' : ''}
                    </div>
                    <a href="/panel/campaigns/${mission.uuid}/missions/find-one" class="mail-item-content ms-2 ms-sm-0 me-2">
                        <span class="mail-item-username me-2">${mission.percent === 0 ? 'N/D' : mission.percent + '%'}</span>
                        <span class="mail-item-subject">${mission.name}</span>
                    </a>
                    <div class="mail-item-meta ms-auto">
                        <div class="mail-item-actions">
                            <button class="btn btn-white btn-sm text-danger btn-shadow btn-icon waves-effect"><i class="fi fi-rr-trash"></i></button>
                        </div>
                    </div>
                </li>
                `;
            });

            if(data.data.length === 0) {
                options = `
                    <div class="text-center p-5 mt-5 mb-5">
                        <div class="alert alert-info">
                            Aucun objectif trouvé pour le moment
                        </div>
                    </div>
                `;
            }

            $('#missions-title').html(`
                <h5>${data.page_title}</h5>
            `);

            $('#missions').html(options);

            if(data.user_campaign) {
                $('.breadcrumb-extra-button').html(`
                    <a href="/panel/campaigns/${data.user_campaign.uuid}/send-to-validation" class="btn btn-success waves-effect waves-light">Envoyer pour validation <i class="fi fi-rs-paper-plane"></i></a>
                `);
            }
        },
        error: function (data) {
            console.log(data);
        }
    });
}
findAllMissions('/panel/campaigns/' + userCampaignUuid + '/missions/find-all?category=default');


function findAllCategoryMissions() {
    $.ajax({
        url: '/panel/settings/category-mission/find-all',
        type: 'GET',
        beforeSend: function() {
            $('#category-missions').html(`
                <div class="text-center mt-5 mb-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Chargement ...</span>
                    </div>
                </div>
            `);
        },
        success: function (data) {
            let options = '';
            data.data.forEach(function(mission, index) {
                options += `
                <a href="/panel/campaigns/${data.campaign ? data.campaign : ''}/missions/find-all?category=${mission.slug}" class="mail-nav-item ${index === 0 ? 'active' : ''}">
                    <i class="icon-folder-open me-2"></i>
                    ${mission.name}
                    <!--<span class="badge badge-sm bg-primary text-white ms-auto">247</span>-->
                </a>
                `;
            });
            $('#category-missions').html(options);
        },
        error: function (data) {
            console.log(data);
        }
    });
}
findAllCategoryMissions();

$('#missions-container').on('click', '.mail-nav-item', function(e) {
    e.preventDefault();

    $('.mail-nav-item').removeClass('active');
    $(this).addClass('active');

    const url = $(this).attr('href');
    findAllMissions(url);
});

$('#missions-container').on('click', '.return-list-missions', function(e) {
    e.preventDefault();

    // const url = $(this).attr('href');
    $('#mission-single').addClass('d-none');
    $('#mission-listing').removeClass('d-none');

});

$('#missions').on('click', '.mail-item-content', function(e) {
    e.preventDefault();
    let url = $(this).attr('href');

    $.ajax({
        url: url,
        type: 'GET',
        success: function (data) {
            if(data.success) {
                $('#mission-single').removeClass('d-none');
                $('#mission-listing').addClass('d-none');
                $('#mission-single').html(data.data);
                loadComments(data.mission_uuid);
            }
        },
        error: function (data) {
            console.log(data);
        }
    });
});

$('#mission-single').on('click', '.note-button', function(e) {
    e.preventDefault();
    $(this).parent().find('.note-form').submit();
});

$('#mission-single').on('submit', '.note-form', function(e) {
    e.preventDefault();
    let url = $(this).attr('action');
    let data = $(this).serialize();

    $.ajax({
        url: url,
        type: 'POST',
        data: data,
        beforeSend: function() {
            loader();
        },
        success: function (data) {
            loader('hide');
            if(data.success) {
                $('#mission-single .single-mission-note').trigger('reset');
                $('#mission-single .single-mission-note').html(`<h4>Note: ${data.note}%</h4>`);
            }else{
                SendError(data.message);
            }
        },
        error: function (data) {
            SendError('Une erreur est survenue');
        }
    });
});

$('#mission-single').on('submit', '.single-mission-comment-form', function(e) {
    e.preventDefault();
    let url = $(this).attr('action');
    let data = $(this).serialize();
    let button = $(this).find('button[type="submit"]');

    $.ajax({
        url: url,
        type: 'POST',
        data: data,
        beforeSend: function() {
            button.html('<i class="fas fa-spinner fa-spin"></i>');
            button.prop('disabled', true);
        },
        success: function (data) {
            loader('hide');
            button.html('Envoyer');
            button.prop('disabled', false);
            if(data.success) {
                $('#mission-single .single-mission-comment-form').trigger('reset');

                $('#mission-single .single-mission-comment-block').append(`
                    <div class="border rounded-2 p-3 mb-3">
                        <div class="d-flex align-items-center justify-content-between border-bottom pb-3 mb-3">
                            <div class="d-flex align-items-center">
                                <div class="avatar avatar-s rounded-circle me-2">
                                    <img src="/storage/${data.comment.sender.avatar}" alt="${data.comment.sender.name}">
                                </div>
                                <div>
                                    <h6 class="mb-0">
                                        ${data.comment.sender.name}
                                    </h6>
                                    <p class="mb-0">
                                        <small>${new Date(data.comment.created_at).toLocaleDateString('fr-FR', { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' })}</small>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <p class="mb-0">
                            ${data.comment.about}
                        </p>
                    </div>
                `);
            }else{
                SendError(data.message);
            }
        },
        error: function (data) {
            loader('hide');
            button.html('Envoyer');
            button.prop('disabled', false);
            SendError('Une erreur est survenue');
        }
    });
});

function loadComments(uuid) {
    $.ajax({
        url: '/panel/campaigns/' + uuid + '/missions/comments',
        type: 'GET',
        beforeSend: function() {
            $('#mission-single .single-mission-comment-block').html(`
                <div class="text-center mt-2 mb-2">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Chargement ...</span>
                    </div>
                </div>
            `);
        },
        success: function (data) {
            if(data.success) {
                $('#mission-single .single-mission-comment-block').html('');
                data.data.forEach(function (comment) {
                    $('#mission-single .single-mission-comment-block').append(`
                        <div class="border rounded-2 p-3 mb-3">
                            <div class="d-flex align-items-center justify-content-between border-bottom pb-3 mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-s rounded-circle me-2">
                                        <img src="/storage/${comment.sender.avatar}" alt="${comment.sender.name}">
                                    </div>
                                    <div>
                                        <h6 class="mb-0">
                                            ${comment.sender.name}
                                        </h6>
                                        <p class="mb-0">
                                            <small>${new Date(comment.created_at).toLocaleDateString('fr-FR', { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' })}</small>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <p class="mb-0">
                                ${comment.about}
                            </p>
                        </div>
                    `);
                });
            }
        },
        error: function (data) {
            SendError('Une erreur est survenue');
        }
    });
}


$('.breadcrumb-extra-button').on('click', 'a', function(e) {
    e.preventDefault();
    let url = $(this).attr('href');

    $.ajax({
        url: url,
        type: 'GET',
        data: {
            _token: $('meta[name="csrf-token"]').attr('content')
        },
        beforeSend: function() {
            loader();
        },
        success: function (data) {
            loader('hide');
            if(data.success) {
                SendSuccess(data.message);
            }
        },
        error: function (data) {
            SendError('Une erreur est survenue');
        }
    });
});
