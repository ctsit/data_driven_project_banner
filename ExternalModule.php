<?php

namespace ProjectBanner\ExternalModule;

use ExternalModules\AbstractExternalModule;

class ExternalModule extends AbstractExternalModule {

    function redcap_every_page_top($project_id) {
        // TODO: if ( url ~= */index.php* || url ~= */ProjectSetup/*)
        if ($this->query()) {
            echo '
        <script>
            $(document).ready(function() {
                $("#sub-nav").after("<div>' . $this->bannerText() . '</div>");
            });
        </script>';
        }
    }


    function query() {
        $sql = 'SELECT project_id, invoice_id,
  concat("https://redcap.ctsi.ufl.edu/invoices/invoice-", invoice_id, ".pdf") as invoice_url
        FROM ctsi_redcap.uf_annual_project_billing_invoices
                        where datediff(now(), invoice_created_date) > 340
                        and invoice_status = "sent";"';


        // TODO
        // if (mysqli_query($sql)) { return true; }
        // return false;
        return true;
    }

    function bannerText() {
        $banner_text = "<h2>Banner header</h2> </br>";

        $banner_text .= $this->getSystemSetting('banner_text');

        $banner_text .= "</br>Banner footer";

        return $banner_text;
    }

}
