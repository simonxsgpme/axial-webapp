function loadCampaigns() {
    $.ajax({
        url: '/panel/campaigns/find-all',
        type: 'GET',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        beforeSend: function() {
            $('#campaigns').html(`
                <div class="col-12 text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Chargement des campagnes ...</span>
                    </div>
                </div>
            `);
        },
        success: function(data) {
            Swal.close();
            $('#campaigns').html('');
            if (data.success) {
                // Mettre à jour les options du select
                if(data.data.length > 0) {
                    let options = '';
                    data.data.forEach(function(campaign) {
                        options += `
                        <div class="col-md-4">
                            <a href="/panel/campaigns/${campaign.uuid}/show">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="d-flex align-items-start justify-content-between mb-4">
                                            <div class="align-items-center gap-3">
                                                <div class="avatar avatar-lg text-white mb-3" style="width: 100px; height: 100px;">
                                                    <img src="/assets/images/icons/folder.svg" alt="">
                                                </div>
                                                <div>
                                                    <h6 class="mb-0">
                                                        ${campaign.name}
                                                    </h6>
                                                    <small class="text-body mb-0">
                                                        ${campaign.about}
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-between small text-body">
                                            <span>Du ${new Date(campaign.mission_start_at).toLocaleDateString('fr-FR', { year: 'numeric', month: 'short', day: 'numeric' })}</span>
                                            <span>au ${new Date(campaign.evaluation_stop_at).toLocaleDateString('fr-FR', { year: 'numeric', month: 'short', day: 'numeric' })}</span>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                        `
                    });
                    $('#campaigns').html(options);
                }
            } else {
                SendError(data.message);
            }
        },
        error: function(data) {
            SendError(data.responseJSON.message ?? 'Une erreur est survenue');
        }
    });
}

loadCampaigns();
