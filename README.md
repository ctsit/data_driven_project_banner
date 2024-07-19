# Data Driven Project Banner

[![DOI](https://zenodo.org/badge/DOI/10.5281/zenodo.3561124.svg)](https://doi.org/10.5281/zenodo.3561124)


A REDCap Module designed to display banners at the top of select project pages. Supports the data piping of query results into the banner text.

Please see the [CHANGELOG](CHANGELOG.md) to learn about changes and bugfixes. Version 3.0.0 introduces **breaking** changes.

> **Important:** Version 3.0.0 introduces a new feature, **Multiple Banners**. This feature allows for the display of multiple banners on a single project. This change is **breaking** and will require reconfiguration of the module if you are upgrading from a previous version (Please see the [Migration Help](#migration-from-2xx-to-300) for more information). If you are new to the module you can ignore the migration help.

## Prerequisites
- REDCap >= 14.0.2

## Easy Installation
- Obtain this module from the Consortium [REDCap Repo](https://redcap.vanderbilt.edu/consortium/modules/index.php) from the REDCap Control Center.

## Manual Installation
- Clone this repo into `<redcap-root>/modules/data_driven_project_banner_v0.0.0`.

## Global Configuration

>Due to know [issues](https://redcap.vumc.org/community/post.php?id=90452&comment=90466) in REDCap with use of branchingLogic in system-settings with repeatable items, we have opted not to use the branching logic and provide all configuration options at a time.

You now have the option to have multiple banners on a single project. To have more than one banner click on the plus sign by the banner settings. This will add a new set of configuration options for a new banner. You can add as many banners as you like.

Typically, this module should be enabled on all projects after it has been configured.  All configuration options are at the system-level.

### Multi Row Response

Check this option, If you expect your query to return multiple rows. Doing so will add the text from two additional fields: **Banner Top Text** and **Banner Bottom Text** to the banner. These fields will appear before and after the Banner Text, respectively, and do not support data piping.

> *The configuration now will have the Banner Top Text and Banner Bottom Text fields visible at all times, but they will only be used if the Multi Row Response option is checked.*

### Banner Text

Provide the text you would like displayed in the project banner. This field supports HTML formatted text. If you do not configure this value, you will get the default project banner:

```
This is the default project banner. Change this in the system level
module configuration for the Data Driven Project Banner module.
```

The banner text field also supports data piping akin to REDCap Smart Variables, (i.e. `[project_id]` will return the value stored for project id). That said, this module uses its _own_ set of replacement fields. The variables available for replacement are the column names in the response to the SQL query specified in the [**Custom SQL for data**](#custom-sql-for-data) configuration option. 

**Note:** This block will be repeated for _every row returned by the query_. If your [**Custom SQL for data**](#custom-sql-for-data) query is expected to return multiple rows, use the fields provided by **Multi Row Response**.


### Banner Colors

You have the option to customize the banner colors. From a color picker, select a color for the background and border of the banner.


### Criteria for display

From a dropdown menu, choose how the banner should be displayed. There are three options:

- **Always display**: Always display the banner. 

  *Note that if the SQL query in [**Custom SQL for data**](#custom-sql-for-data) fails, the banner may still be displayed, but without the intended data piping. If a query result is _required_ to display the banner, select _Require a non-empty query result_.*
- **Require a non-empty query result**: Display the banner _only_ if the query from [**Custom SQL for data**](#custom-sql-for-data) returns results. Useful for banners utilizing data piping fields.
- **Custom query**: This advanced option allows you to create your own query in a new field titled **Custom SQL for criteria**; the banner will only be displayed if the result of your query is not empty.
  - **Custom SQL for criteria**: This text field spawns when **Custom query** is selected, use this field to create your SQL statement. The `SELECT` keyword is automatically prepended to queries for you as your queries should all be `SELECT` statements. A simple example may be `* FROM redcap_data WHERE project_id = [project_id]`, which will cause the banner to only appear on projects which have data.
    - Any reference to `[project_id]` in these queries will be replaced with the current Project ID. That is the only substitution made to the query string.

  > The **_Custom SQL for criteria_** field will always be visible in the configuration page, but will only be used if the **Criteria for display** is set to **Custom query**.

### Custom SQL for data

Create your own SQL query to provide data. This field behaves similarly to the **Custom SQL for criteria** option for **Criteria for display**: it prepends `SELECT` to your queries and allows piping of `[project_id]` into the query.
A basic example you may find useful is `* FROM redcap_projects WHERE project_id = [project_id]` which returns the information for the project in which the banner is displayed. This field will always be visible in the configuration page, but wil only be used if the **Criteria for display** is set to 

### Display on all pages

Check this option to display the banner on all pages in the project. If unchecked, the banner will only be displayed on the project home page and the project setup page.

### Data piping fields

If you use the suggested query in [**Custom SQL for data**](#Custom-SQL-for-data), there are over 110 possible column names to choose from when data piping. These columns are probably the most interesting for data piping:

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


### Migration from 2.X.X to 3.0.0
If you are upgrading from a version prior to 3.0.0, you will need to reconfigure your banner. To make this easier, we have a MySQL query that will help you migrate your current banner to the new configuration format. Your administrator will need to run this query on the REDCap database.


```sql
START TRANSACTION;
SET @module_id = (
    SELECT external_module_id
    FROM redcap_external_modules
    WHERE directory_prefix = 'data_driven_project_banner'
);

UPDATE redcap_external_module_settings
SET 
    type = 'json-array',
    value = CASE
        WHEN `key` IN ('multi_row_response', 'display_everywhere') THEN
            CASE 
                WHEN value IS NULL THEN '[]'
                WHEN LOWER(value) = 'true' THEN '[true]'
                WHEN LOWER(value) = 'false' THEN '[false]'
                ELSE CONCAT('[', LOWER(value), ']')
            END
        ELSE
            CASE 
                WHEN value IS NULL THEN '[]'
                ELSE JSON_ARRAY(value)
            END
    END
WHERE 
    external_module_id = @module_id
    AND `key` IN (
        'banner_settings', 'multi_row_response', 'banner_text_top', 'banner_text', 
        'banner_text_bottom', 'bg_color', 'border_color', 'criteria', 
        'custom_criteria_sql', 'custom_data_sql', 'display_everywhere'
    );
COMMIT;
```

> **Note:**
This query requires MySQL 8.0 or later. 

If you run this query shortly before upgrading Data Driven Project Banner, the migrated settings will be in place for the module upgrade. The module will start working correctly immediately. We **strongly** recommend you run this query before upgrading the module.

The query should be run in against the MySQL database of your REDCap instance. After running the query, you should see a message saying that some rows were inserted. The module will start working immediately. In the project home page go to the **External Module > Manage** and click on the Data Driven Project Banner configure button. You should see your banner migrated to the new configuration format and you should be able to see the existing banner on the project home page.
