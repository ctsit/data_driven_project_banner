$(document).ready(function() {
    var $modal = $('#external-modules-configure-modal');

    ExternalModules.Settings.prototype.resetConfigInstancesOld = ExternalModules.Settings.prototype.resetConfigInstances;
    ExternalModules.Settings.prototype.resetConfigInstances = function() {
        ExternalModules.Settings.prototype.resetConfigInstancesOld();

        // Making sure we are overriding this modules's modal only.
        if ($modal.data('module') !== DDPB.modulePrefix) {
            return;
        }

        // Add "SELECT" prefix to custom SQL query fields
        $('[name^="custom_"][name$="_sql"]').each(function() {
            console.log(this);
            if ($(this).hasClass('select-prefix-set')) {
                return;
            }

            $(this).parent().prepend('<span>SELECT</span>');
            $(this).addClass('select-prefix-set');
            $(this).attr('placeholder', "* FROM redcap_projects WHERE project_id = [project_id];");
        });
    }
});
