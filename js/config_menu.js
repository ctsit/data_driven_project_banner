$(document).ready(function() {
    var $modal = $('#external-modules-configure-modal');

    // check if other modules have set this to avoid infinite redefinition loop
    if (typeof ExternalModules.Settings.prototype.resetConfigInstancesOld === 'undefined') {
        ExternalModules.Settings.prototype.resetConfigInstancesOld = ExternalModules.Settings.prototype.resetConfigInstances;
    }

    // fire on clicking "configure" for any module
    $modal.on('show.bs.modal', function() {
        // Making sure we are overriding this modules's modal only.
        if ($(this).data('module') !== DDPB.modulePrefix) { return; }

        ExternalModules.Settings.prototype.resetConfigInstances = function() {
            ExternalModules.Settings.prototype.resetConfigInstancesOld();

            // Check that resetConfigInstances is not acting on a different module
            if ($modal.data('module') !== DDPB.modulePrefix) { return; }

            // Add "SELECT" prefix to custom SQL query fields
            $('[name^="custom_"][name$="_sql"]').each(function() {
                // prevent duplicate prepend, necessary if config menu is closed and reopened for this module
                if ($(this).hasClass('ddpb-select-prefix-set')) { return; }

                $(this).parent().prepend('<span>SELECT</span>');
                $(this).addClass('ddpb-select-prefix-set');
                $(this).attr('placeholder', "* FROM redcap_projects WHERE project_id = [project_id];");
            });
        }
    });
});
