<?php

namespace ProjectBanner\ExternalModule;

use ExternalModules\AbstractExternalModule;

class ExternalModule extends AbstractExternalModule {

    function redcap_every_page_top($project_id) {
        // TODO: if ( url ~= */index.php* || url ~= */ProjectSetup/*)
        if ($this->query()) {
            $this->includeCss('css/banner.css');
            echo '
        <script>
            $(document).ready(function() {
                $("#sub-nav").before("<div id=\"project-banner\">' . $this->bannerText() . '</div>");
            });
        </script>';
        }
    }


    function query() {
        if (!$sql = $this->getSystemSetting('custom_sql')) {
        $sql = 'SELECT project_id, invoice_id,
  concat("https://redcap.ctsi.ufl.edu/invoices/invoice-", invoice_id, ".pdf") as invoice_url
        FROM ctsi_redcap.uf_annual_project_billing_invoices
                        where datediff(now(), invoice_created_date) > 340
                        and invoice_status = "sent";"';
        } else {
            // TODO: if ( $this->sanitizeUserSQL($sql) ) { ... } else { warn and revert to default SQL }
        }

        // TODO
        // if (mysqli_query($sql)) { return true; }
        // return false;
        return true;
    }

    function bannerText() {
        $banner_text = "<h2>Banner header</h2> </br>";

        if (!$admin_banner_txt = $this->getSystemSetting('banner_text') ) {
            /* set some default */
        }
        $banner_text .= $admin_banner_txt;

        $banner_text .= "</br>Banner footer";

        return $banner_text;
    }

    protected function includeCss($path) {
        echo '<link rel="stylesheet" href="' . $this->getUrl($path) . '">';
    }

}
