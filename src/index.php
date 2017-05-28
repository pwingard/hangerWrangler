<?php
namespace html_docs\hanger_wrangler;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once (dirname(__DIR__)).'/src/HangerWrangler.php';

$HangerWrangler = new HangerWrangler();

var_dump($HangerWrangler);
die();
echo $HangerWrangler->result . PHP_EOL;

/**
 * list of available input overrides
//used in startup costs
@param int patent, legal_accounting, prototyping_cost_unit, number_prototypes, percent_cost_of_sales_as_seed,
//used in cost of doing business
@param int inventory_cost_per_unit,inventory,daily_sold,owner_cost_hr,manager_cost_hr,labor_cost_hr,monthly_rent,monthly_utilities,
//used in net calculations
@param int unit_price
*/

////total inventory to sell for initial period
//HangerWrangler->input["inventory"] = 1000;
//
////estimated units sold on a daily basis
//HangerWrangler->input["daily_sold"] = 10;
//
////$TotCostToSell->setUp();
//
//echo "The result is: " . $TotCostToSell->result.PHP_EOL;