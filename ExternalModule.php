<?php

namespace ProjectBanner\ExternalModule;

use ExternalModules\AbstractExternalModule;

class ExternalModule extends AbstractExternalModule {

    function redcap_every_page_top($project_id) {
        $url = $_SERVER['REQUEST_URI'];
        $is_on_project_home = preg_match("/^\/redcap\/redcap_v\d\.\d\.\d\/index\.php\?pid=\d+\z/", $url);
        $is_on_project_setup = preg_match("/.*ProjectSetup.*/", $url);

        if ( $is_on_project_home || $is_on_project_setup) {
            if ( $this->queryInvoices() ) {
                $this->displayBanner();
            }
        }
    }


    function displayBanner() {
        $this->includeCss('css/banner.css');
        $this->includeJs('js/banner_inject.js');

        if (!$banner_text = $this->getSystemSetting('banner_text') ) {
            /* set some default */
        }

        $banner_text = json_encode($banner_text);
        echo "<script type='text/javascript'>var banner_text = $banner_text;</script>";
    }


    function queryInvoices() {
        $project_id = PROJECT_ID;

        if (!$sql = $this->getSystemSetting('custom_sql')) {
            $sql = 'SELECT project_id, invoice_id,
  concat("https://redcap.ctsi.ufl.edu/invoices/invoice-", invoice_id, ".pdf") as invoice_url
FROM uf_annual_project_billing_invoices
WHERE datediff(now(), invoice_created_date) > 340
AND invoice_status= "sent"
;';
        } else {
            // TODO: if ( $this->sanitizeUserSQL($sql) ) { ... } else { warn and revert to default SQL }
            // Pass user sql as function arg and recurse if it fails
        }

        if ($response = db_query($sql)) {
            return true;
        }

        return false;
    }


    protected function includeCss($path) {
        echo '<link rel="stylesheet" href="' . $this->getUrl($path) . '">';
    }


    protected function includeJs($path) {
        echo '<script src="' . $this->getUrl($path) . '"></script>';
    }


}
