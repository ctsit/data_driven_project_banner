<?php

namespace ProjectBanner\ExternalModule;

use ExternalModules\AbstractExternalModule;
use REDCap;

class ExternalModule extends AbstractExternalModule {

    function redcap_every_page_top($project_id) {
        $url = $_SERVER['REQUEST_URI'];
        $is_on_project_home = preg_match("/\/redcap_v\d\.\d\.\d\/index\.php\?pid=\d+\z/", $url);
        $is_on_project_setup = preg_match("/.*ProjectSetup.*/", $url);

        if ( $is_on_project_home || $is_on_project_setup) {
            if ($sql_response = $this->queryInvoices() ) {
                print_r("<pre>");
                var_dump($sql_response);
                print_r("</pre>");
                $this->displayBanner($sql_response);
            }
        }
    }


    function displayBanner($sql_response) {
        $this->includeCss('css/banner.css');
        $this->includeJs('js/banner_inject.js');

        if ( !$banner_text = $this->getSystemSetting('banner_text')  )  {
            // Default banner text
            $banner_text = "Hello " . USERID . ",</br>You have project support fees due, see the link below:</br>";

            foreach( $sql_response as $row => $value) {
                $banner_text .= "<a href=\"{$value['invoice_url']}\">Project {$value['project_id']}</a></br>";
            }
        }

        $banner_text = $this->replaceSmartVariables($banner_text, $sql_response);

        $banner_text = json_encode($banner_text);
        echo "<script type='text/javascript'>var banner_text = $banner_text;</script>";
    }


    function replaceSmartVariables($input_text, $sql_response) {
        // "Data piping" recreation
        $smart_variables = (
            array_map(
                function($x) { return "[$x]"; },
                array_keys($sql_response[0])
            )
        );

        $smart_variables = array_combine($smart_variables, $sql_response[0]);
        $smart_variables["[project_title]"] = REDCap::getProjectTitle();

        return str_replace(
            array_keys($smart_variables),
            array_values($smart_variables),
            $input_text
        );
    }


    function queryInvoices() {
        $project_id = PROJECT_ID;

        if (!$sql = $this->getSystemSetting('prebuilt_sql')) {
            return;
        }

        $sql = str_replace("[project_id]", PROJECT_ID, $sql);

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
