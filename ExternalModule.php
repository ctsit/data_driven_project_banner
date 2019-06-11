<?php

namespace ProjectBanner\ExternalModule;

use ExternalModules\AbstractExternalModule;
use REDCap;

class ExternalModule extends AbstractExternalModule {

    function redcap_every_page_top($project_id) {
        $url = $_SERVER['REQUEST_URI'];
        $is_on_project_home = preg_match("/^\/redcap\/redcap_v\d\.\d\.\d\/index\.php\?pid=\d+\z/", $url);
        $is_on_project_setup = preg_match("/.*ProjectSetup.*/", $url);

        if ( $is_on_project_home || $is_on_project_setup) {
            if ($sql_response = $this->queryInvoices() ) {
                $this->displayBanner($sql_response);
            }
        }
    }


    function displayBanner($sql_response) {
        $this->includeCss('css/banner.css');
        $this->includeJs('js/banner_inject.js');

        if ( ( !$banner_text = $this->getSystemSetting('banner_text') ) && ( !$this->getSystemSetting('custom_sql') ) )  {
            // Default banner text
            $banner_text = "Hello " . USERID . ",</br>You have project support fees due, see the link below:</br>";

            foreach( $sql_response as $row => $value) {
                $banner_text .= "<a href=\"{$value['invoice_url']}\">Project {$value['project_id']}</a></br>";
            }
        } else {
            // "Data piping" recreation
            $replace_with = [
                '[project_id]' => $sql_response[0]['project_id'],
                '[invoice_id]' => $sql_response[0]['invoice_id'],
                '[invoice_url]' => $sql_response[0]['invoice_url'],
                '[project_title]' => REDCap::getProjectTitle()
            ];
            $banner_text = str_replace(
                array_keys($replace_with),
                array_values($replace_with),
                $banner_text
            );
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
WHERE project_id = "' . $project_id . '"
AND invoice_status= "sent"
AND datediff(now(), invoice_created_date) > 340
;';
        } else {
            // TODO: if ( $this->sanitizeUserSQL($sql) ) { ... } else { warn and revert to default SQL }
            // Pass user sql as function arg and recurse if it fails
        }

        if ($response = db_query($sql)) {
            return ($response->fetch_all(MYSQLI_ASSOC));
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
