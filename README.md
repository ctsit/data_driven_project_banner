# unpaid_project_banner
A REDCap Module designed to gently prod customers to pay their annual per-project support fee

## Global Configuration
### Banner Text
Richly (HTML) formatted text may be inserted here to be displayed on the banner.

**Data piping support**  
The banner text field allows the use of data piping to display relevant information. Syntax for data piping is the same as smart variables , (i.e. `[project_id]` will return the value stored for project id). 
All columns from the `redcap_projects` table in your database are available as well as additional variables which are determined by the SQL query selected in the configuration menu.

### Prebuilt SQL
Select a SQL query from. If the SQL query chosen returns any rows, the banner is shown.  
Advanced users may edit the `config.json` file to add their own SQL queries. Custom written SQL statements support data piping _only_ for `[project_id]`.

