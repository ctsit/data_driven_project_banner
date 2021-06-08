<?php

namespace ProjectBanner\ExternalModule;

use ExternalModules\AbstractExternalModule;
use REDCap;

class ExternalModule extends AbstractExternalModule {

    function redcap_every_page_top($project_id) {
        $url = $_SERVER['REQUEST_URI'];
        $is_on_project_home = preg_match("/\/redcap_v\d+\.\d+\.\d+\/index\.php\?pid=\d+\z/", $url);
        $is_on_project_setup = preg_match("/.*ProjectSetup.*/", $url);
        $criteria = $this->getSystemSetting('criteria');

        if ($this->getSystemSetting('display_everywhere') || ($is_on_project_home || $is_on_project_setup)) {
            $sql_response = $this->queryData();
            switch ($criteria) {
                case "custom":
                    if (!$this->queryCriteria()) return;
                    $this->displayBanner($sql_response);
                    break;
                case "require_result":
                    // check if the criteria returns anything
                    if (!$sql_response) return;
                default:
                    // always display
                    $this->displayBanner($sql_response);
                    break;
            }
        }
    }


    function redcap_module_configure_button_display($project_id) {
        $this->setJsSettings(array('modulePrefix' => $this->PREFIX));
        $this->includeJs('js/config_menu.js');
        return true;
    }


    function displayBanner($sql_response) {
        echo "<style>
        #project-banner {" .
            "--bg-color: " . $this->getSystemSetting('bg_color') . ";" .
            "--border-color: " . $this->getSystemSetting('border_color') . ";" .
        "}
        </style>";

        $this->includeCss('css/banner.css');
        $this->includeJs('js/banner_inject.js');

        if ( !$banner_text = $this->getSystemSetting('banner_text')  )  {
            // Default banner text
            $banner_text = "This is the default project banner. Change this in the system level module configuration for the Data Driven Project Banner Module.</br>";
        }

        $banner_output = $this->getSystemSetting('banner_text_top');
        if ($sql_response) {
            $banner_output .= $this->replaceSmartVariables($banner_text, $sql_response);
        }
        $banner_output .= $this->getSystemSetting('banner_text_bottom');

        $banner_text = json_encode($banner_output);
        echo "<script type='text/javascript'>var data_driven_project_banner_text = $banner_text;</script>";
    }


    function replaceSmartVariables($input_text, $sql_response) {
        $replaced_vals = "";
        foreach ($sql_response as $row) {
            // "Data piping" recreation
            $smart_variables = (
                array_map(
                    function ($x) { return "[$x]"; },
                    array_keys($row)
                )
            );

            $smart_variables = array_combine($smart_variables, $row);
            $smart_variables["[project_title]"] = REDCap::getProjectTitle();

            $replaced_vals .= str_replace(
                array_keys($smart_variables),
                array_values($smart_variables),
                $input_text
            );
        }
        return $replaced_vals;
    }


    private function performPrebuiltQuery($prebuilt_sql) {
        if (!$prebuilt_sql) {
            return;
        }
        $prebuilt_sql = htmlspecialchars_decode($prebuilt_sql);

        $sql = str_replace("[project_id]", PROJECT_ID, $prebuilt_sql);

        // TODO: migrate to framework v4's query in major version change
        if ($response = db_query($sql)) {
            return ($response->fetch_all(MYSQLI_ASSOC));
        }

        return false;
    }

    function queryCriteria() {
        $sql = "SELECT " . $this->getSystemSetting('custom_criteria_sql');
        return $this->performPrebuiltQuery($sql);
    }


    function queryData() {
        $data_sql = $this->getSystemSetting('data_sql');
        if ($data_sql == "custom") {
            $data_sql = "SELECT " . $this->getSystemSetting('custom_data_sql');
        }
        return $this->performPrebuiltQuery($data_sql);
    }


    protected function includeCss($path) {
        echo '<link rel="stylesheet" href="' . $this->getUrl($path) . '">';
    }


    protected function includeJs($path) {
        echo '<script src="' . $this->getUrl($path) . '"></script>';
    }


    protected function setJsSettings($settings) {
        echo '<script>DDPB = ' . json_encode($settings) . ';</script>';
    }
}
