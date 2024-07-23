$(document).ready(function() {
    if (typeof DDPB !== 'undefined' && Array.isArray(DDPB.DDPBs)) {
        for (var i = 0; i < DDPB.DDPBs.length; ++i) {
            if (DDPB.DDPBs[i] !== "" && DDPB.DDPBs[i] !== "''") {
                $("#subheader").before(`<div class='project-banner' id='project-banner-${i}'>${DDPB.DDPBs[i]}</div>`);
            }
        }
    }
});