$(document).ready(function() {

  for (var i = 0; i < DDPB.DDPBs.length; ++i) {
    $("#subheader").before(`<div class='project-banner' id='project-banner-${i}'> ${DDPB.DDPBs[i]}</div>`);
  }
});
