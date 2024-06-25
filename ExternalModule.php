<?php

namespace ProjectBanner\ExternalModule;

use ExternalModules\AbstractExternalModule;
use REDCap;

class ExternalModule extends AbstractExternalModule {

    public $display_banners_arr;

    function redcap_every_page_top($project_id) {
        $url = $_SERVER['REQUEST_URI'];
        $is_on_project_home = preg_match("/\/redcap_v\d+\.\d+\.\d+\/index\.php\?pid=\d+\z/", $url);
        $is_on_project_setup = preg_match("/.*ProjectSetup.*/", $url);
        $is_on_mod_manager = preg_match("/.*ExternalModules.*/", $url);
        $banners = $this->framework->getSubSettings("banner_settings");

        if ($is_on_mod_manager) {
            $this->setJsSettings(array('modulePrefix' => $this->PREFIX));
            $this->includeJs('js/config_menu.js');

            // $this->framework->initializeJavascriptModuleObject();
        }

        $this->setJsSettings(array('DDPBs' => []));
        // echo "<script type='text/javascript'>var DDPBs = [];</script>";

        $i = 0;
        foreach ($banners as $banner) {
            $banner['num'] = $i++;

            if ($banner['display_everywhere'] || ($is_on_project_home || $is_on_project_setup)) {
                // $sql_response = $this->queryData();
                $sql_response = false;
                $sql_response = $this->performQuery($banner['custom_data_sql']);
                switch ($banner['criteria']) {
                    case "custom":
                        // if (!$this->queryCriteria()) return;
                        if (!$this->performQuery($banner['custom_criteria_sql'])) continue;
                        $this->displayBanner($banner, $sql_response);
                        break;
                    case "require_result":
                        // check if the criteria returns anything
                        if (!$sql_response) continue;
                    default:
                        // always display
                        $this->displayBanner($banner, $sql_response);
                        break;
                }
            }

        }

        $this->includeCss('css/banner.css');
        $this->includeJs('js/banner_inject.js');
    }


    function displayBanner($banner, $sql_response) {
        echo "<style>
        #project-banner-" . $banner['num'] . " {" .
            "--bg-color: " . $banner['bg_color'] . ";" .
            "--border-color: " . $banner['border_color'] . ";" .
        "}
        </style>";


        if ( !$banner_text = $banner['banner_text']  )  {
            // Default banner text
            $banner_text = "This is the default project banner. Change this in the system level module configuration for the Data Driven Project Banner Module.</br>";
        }

        $banner_output = $banner['banner_text_top'] ?? "";
        if ($sql_response) {
            $banner_output .= $this->replaceSmartVariables($banner_text, $sql_response);
        } else {
            $banner_output .= $banner_text;
        }
        $banner_output .= $banner['banner_text_bottom'] ?? "";

        $banner_text = json_encode($banner_output);
        // echo "<script type='text/javascript'>data_driven_project_banner_text_$i = $banner_text;</script>";
        $banner_str = "<script type='text/javascript'>DDPB.DDPBs[" . $banner['num'] . "] = $banner_text;</script>";
        echo $banner_str;
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
        if (!$prebuilt_sql) { return; }

        $prebuilt_sql = htmlspecialchars_decode($prebuilt_sql);

        // PROJECT_ID constant absence causes system error
        if (str_contains($prebuilt_sql, "[project_id]")) {
            if (!defined('PROJECT_ID')) { return false; }
            $prebuilt_sql = str_replace("[project_id]", PROJECT_ID, $prebuilt_sql);
        }

        $result = $this->framework->query($prebuilt_sql, []);
        if ($result) {
            return ($result->fetch_all(MYSQLI_ASSOC));
        }

        return false;
    }

    function performQuery($sql_str) {
        if (is_null($sql_str)) return false;
        $data_sql = "SELECT " . $sql_str;
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
