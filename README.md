# Data Driven Project Banner
A REDCap Module designed to display a banner at the top of select project pages. Supports the data piping of query results into the banner text.

## Prerequisites
- REDCap >= 8.0.3

## Easy Installation
- Obtain this module from the Consortium [REDCap Repo] (https://redcap.vanderbilt.edu/consortium/modules/index.php) from the REDCap Control Center.

## Manual Installation
- Clone this repo into `<redcap-root>/modules/data_driven_project_banner_v0.0.0`.

## Global Configuration

Typically, this module should be enabled on all projects after it has been configured.  All configuration options are at the system-level.

### Banner Text

Provide the text you would like displayed in the project banner. This field supports HTML formatted text. If you do not configure this value, you will get the default project banner:

    This is the default project banner. Change this in the system level module configuration for the Data Driven Project Banner module.

The banner text field also supports data piping akin to REDCap Smart Variables, (i.e. `[project_id]` will return the value stored for project id). That said, this module uses its _own_ set of replacement fields. The variables available for replacement are the column names in the response to the SQL query specified in the _Prebuilt SQL_ configuration option. No other values are available to be piped.

### Prebuilt SQL

Select an optional SQL query from the list provided. The recommended query is _REDCap projects table_ which runs the query `SELECT * FROM redcap_projects WHERE project_id = [project_id]` where [project_id] is the Project ID for the current project. Note that if the SQL query fails, the banner will still be displayed, but without the intended data piping.

Advanced users may edit the `config.json` file to add their own SQL queries. These queries should be project-centric and return only one row. Any reference to [project_id] in these queries will be replaced with the current Project ID.  That is the only substition made to the query string.
