# Data Driven Project Banner

[![DOI](https://zenodo.org/badge/DOI/10.5281/zenodo.3561124.svg)](https://doi.org/10.5281/zenodo.3561124)


A REDCap Module designed to display a banner at the top of select project pages. Supports the data piping of query results into the banner text.

Please see the [CHANGELOG](CHANGELOG.md) to learn about changes and bugfixes. Version 2.0.0 introduces **breaking** changes.

## Prerequisites
- REDCap >= 8.0.3

## Easy Installation
- Obtain this module from the Consortium [REDCap Repo](https://redcap.vanderbilt.edu/consortium/modules/index.php) from the REDCap Control Center.

## Manual Installation
- Clone this repo into `<redcap-root>/modules/data_driven_project_banner_v0.0.0`.

## Global Configuration

Typically, this module should be enabled on all projects after it has been configured.  All configuration options are at the system-level.

### Multi Row Response

Check this option if you expect your query will return multiple rows, doing so will reveal 2 additional fields: **Banner Top Text** and **Banner Bottom Text**. These fields will appear before and after the **Banner Text**, respectively. These fields do _not_ support data piping.

### Banner Text

Provide the text you would like displayed in the project banner. This field supports HTML formatted text. If you do not configure this value, you will get the default project banner:

```
This is the default project banner. Change this in the system level
module configuration for the Data Driven Project Banner module.
```

The banner text field also supports data piping akin to REDCap Smart Variables, (i.e. `[project_id]` will return the value stored for project id). That said, this module uses its _own_ set of replacement fields. The variables available for replacement are the column names in the response to the SQL query specified in the [**Data to display**](#Data-to-display) configuration option. No other values are available to be piped.

**Note:** This block will be repeated for _every row returned by the query_. If your [**Data to display**](#Data-to-display) query is expected to return multiple rows, use the fields provided by **Multi Row Response**

### Criteria for display


- **Always display**: Always display the banner. Note that if the SQL query in [**Data to display**](#Data-to-display) fails, the banner may still be displayed, but without the intended data piping. If a query result is _required_ to display the banner, select _Require a non-empty query result_.
- **Require a non-empty query result**: Display the banner _only_ if the query from [**Data to display**](#Data-to-display) returns results. Useful for banners utilizing data piping fields.
- **Custom query**: This advanced option allows you to create your own query in a new field titled **Custom SQL for criteria**; the banner will only be displayed if the result of your query is not empty.
  - **Custom SQL for criteria**: This text field spawns when **Custom query** is selected, use this field to create your SQL statement. The `SELECT` keyword is automatically prepended to queries for you as your queries should all be `SELECT` statements. A simple example may be `* FROM redcap_data WHERE project_id = [project_id]`, which will cause the banner to only appear on projects which have data.
    - Any reference to [project_id] in these queries will be replaced with the current Project ID. That is the only substitution made to the query string.

### Data to display

Create your own SQL query to provide data. This field behaves similarly to the **Custom SQL for criteria** option for **Criteria for display** - it supports data piping of all columns it returns and prepends `SELECT` to your queries.  
A basic example you may find useful is `* FROM redcap_projects WHERE project_id = [project_id]` which returns the information for the project in which the banner is displayed.

### Data piping fields

If you use the suggested query in **Data to display**, there are over 110 possible column names to choose from when data piping. These columns are probably the most interesting for data piping:

```
project_id
app_title
project_contact_name
project_contact_email
project_pi_firstname
project_pi_mi
project_pi_lastname
project_pi_email
project_pi_alias
project_pi_username
project_pi_pub_exclude
project_pub_matching_institution
project_irb_number
project_grant_number
```
