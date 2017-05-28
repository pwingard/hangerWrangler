<?php
/**
 * Created by PhpStorm.
 * User: petewingard
 * Date: 5/23/17
 * Time: 10:00 PM
 */

namespace HangerWrangler;


class CostToSell {

        //user can overwrite any public on input

        //used in startup costs
        public $patent = 18000;
        public $legal_accounting = 6000;

        public $prototyping_cost_unit = 100;
        public $number_prototypes = 10;
        private $costPrototypes;

        public $percent_cost_of_sales_as_seed =  .25;
        private $seedSalesCostForRound;

        public $inventory =  3000;
        public $inventory_cost_per_unit = 10;
        private $costInventory;
    //****************************
        private $startUpCost;//√
    //****************************

        //used in cost of doing business
        public $daily_sold =  100;

        public $owner_cost_hr = 50;
        private $owners_daily_cost;
        public $manager_cost_hr = 25;
        private $manager_daily_cost;
        public $labor_cost_hr = 15;
        private $labor_daily_cost;
        private $employees_daily_cost;

        private $this_rounds_months;

        private $employee_monthly_cost;
        public $supply_cost_per_unit_sold = 1.25;
        private $monthly_supply_cost;
        public $monthly_rent = 1200;
        public $monthly_utilities = 150;
    //****************************
        private $salesCostForRound;
    //****************************

        //used in net calculations
        public $unit_price = 17.95;

        //other
        public $result = "No result.";
        private $err_flag = false;


    public function __construct() {
        $this->start();
    }

    /** @method calls each step in the class
     * @return string result, start()
     */
    private function start() {

        $this->checkInput();
        if( $this->err_flag ) return $this->result;

        /**
         * @return $this->salesCostForRound
         */
        $this->salesCost();

        /**
         * @return $this->startUpCost
         */
        $this->startUpCosts();

        /**
         * @return $this->result
         */
        $this->calNet();


//        $this->result = $this->salesCostForRound.PHP_EOL;
    }

    /**
     * @method calculates net for round
     * @param float $this->unit_price
     * @param float $this->inventory
     * @param float $this->salesCostForRound
     * @param float $this->startUpCost
     * @return float $this->result
     */
    private function calNet(){
        $this->result = "$".number_format(round(
            ($this->inventory *  $this->unit_price) -
            $this->salesCostForRound -
            $this->startUpCost
            ,0));
    }

    /**
     * @method calculate one time costs of start up
     * @param float $patent
     * @param float $legal_accounting
     * @param float $prototyping_cost_unit
     * @param float $number_prototypes
     * @param float $percent_cost_of_sales_as_seed
     * @param float $inventory
     * @param float $inventory_cost_per_unit
     * @param float $this->salesCostForRound
     * @return $this->startUpCost
     */
    private function startUpCosts(){
        $this->costPrototypes = $this->prototyping_cost_unit * $this->number_prototypes;
        $this->costInventory = $this->inventory * $this->inventory_cost_per_unit;
        $this->startUpCost = round(
            $this->patent +
            $this->legal_accounting +
            $this->prototyping_cost_unit * $this->number_prototypes +
            $this->inventory * $this->inventory_cost_per_unit +
            $this->percent_cost_of_sales_as_seed * $this->salesCostForRound
        ,0);
    }

    /**
     * @method
     * @return $this->salesCostForRound
     */
    private function salesCost(){

        $this->daysReq();//√
        $this->calDailyHours();//√
        $this->calLaborCosts();
        $this->laborCostPerDay();

        $this->calSupplies();
        $this->calMonthlyNonEmployeeCosts();

        $this->sumMonthlyCostofSales();
        $this->costofSalesForRound();
    }

    /**
     * @method costofSalesForRound() sums the cost of sales for round of inventory
     * @param float monthlyCostofSales
     * @param float days_this_round
     * @return float salesCostForRound
     */
    private function costofSalesForRound(){
        $this->this_rounds_months = round($this->days_this_round/22,2);
        $this->employee_monthly_cost = $this->employees_daily_cost * 22;
        $this->salesCostForRound = round(($this->monthlyCostofSales ) * $this->this_rounds_months,0);
        $this->seedSalesCostForRound = round($this->salesCostForRound * $this->percent_cost_of_sales_as_seed,0);
    }

    /**
     * @method sumMonthlyCostofSales() sums the cost of sales
     * @param float monthlyNonEmployeeCosts
     * @param float monthly_supply_cost
     * @return float monthlyCostofSales
     */
    private function sumMonthlyCostofSales(){
        $this->monthlyCostofSales = $this->monthlyNonEmployeeCosts + $this->monthly_supply_cost + $this->employee_monthly_cost;
    }

    /**
     * @method calMonthlyNonEmployeeCosts() sums the non-employee overhead
     * @param float $monthly_rent
     * @param float $monthly_utilities
     * @param float monthly_supply_cost
     * @return float monthlyNonEmployeeCosts
     */
    private function calMonthlyNonEmployeeCosts(){
        $this->monthlyNonEmployeeCosts = $this->monthly_rent + $this->monthly_utilities + $this->monthly_supply_cost;
    }

    /**
     * @method calSupplies() estimates cost of supplies/month based on daily units sold
     * @param float daily_sold
     * @param float supply_cost_per_unit_sold
     * @return float monthly_supply_cost
     */
    private function calSupplies(){
        $daily_supply_cost = $this->daily_sold * $this->supply_cost_per_unit_sold;
        $this->monthly_supply_cost = round($daily_supply_cost * 22, 0); //22 averge work days in a month
    }

    /**
     * @method laborCostPerDay() sums the various estimated types of labor costs for the each day
     * @param float labor_daily_cost
     * @param float manager_daily_cost
     * @param float owners_daily_cost
     * @return float employee_daily_cost
     */
    private function laborCostPerDay(){
        $this->employees_daily_cost = $this->labor_daily_cost + $this->manager_daily_cost + $this->owners_daily_cost;
    }

    /**
     * @method calLaborCosts() calculates daily labor cost
     * @param float owners_daily_hrs
     * @param float owner_cost_hr
     * @param float manager_daily_hrs
     * @param float manager_cost_hr
     * @param float labor_daily_hrs
     * @param float labor_cost_hr
     * @return float owners_daily_cost
     * @return float manager_daily_cost
     * @return float labor_daily_cost
     */
    private function calLaborCosts(){
        $this->owners_daily_cost = $this->owners_daily_hrs * $this->owner_cost_hr;
        $this->manager_daily_cost = $this->manager_daily_hrs * $this->manager_cost_hr;
        $this->labor_daily_cost = $this->labor_daily_hrs * $this->labor_cost_hr;
    }

    /**
     * @method calculates estimated number of days required to sell inventory for round
     * @param float inventory
     * @param float daily_sold
     * @return float days_this_round daysReq()
     */
    private function daysReq(){
        $this->days_this_round = (int)round(
            $this->inventory/$this->daily_sold,0
        );
    }

    /**
     * @method calDailyHours, calculates daily manhours based
     * @param float daily_sold
     * @return float round_daily_man_hrs
     */
    private function calDailyHours(){
        $this->round_daily_man_hrs = $this->calOwnerHrsPerDay() + $this->calManagerHrsPerDay() + $this->calLaborHrsPerDay();
    }

    /**
     * @return float owners_daily_hrs
     */
    private function calOwnerHrsPerDay(){
        $this->owners_daily_hrs = ($this->daily_sold <= 100) ? 4 : 8;
        return $this->owners_daily_hrs;
    }

    /**
     * @return float manager_daily_hrs
     */
    private function calManagerHrsPerDay(){
        $this->manager_daily_hrs = ($this->daily_sold > 200) ? 8 : 0;
        return $this->manager_daily_hrs;
    }

    /**
     * @return float|int labor_daily_hrs
     */
    private function calLaborHrsPerDay(){
        $this->labor_daily_hrs = $this->daily_sold/25;
        return $this->labor_daily_hrs;
    }

    /**
     * @method errorOut, sets error flag & msg
     * @param string $msg
     * @return string result
     * @return bool err_flag
     */
    private function errorOut($msg){
        $this->err_flag = true;
        $this->result = $msg;
    }

    /**
     * checkInput checks all class properties for numeric and non-zero values
     * @method checkInput()
     * @param CostToSell class properties
     * @calls errorOut(string) (on error)
     */
    public function checkInput(){
        foreach ($this as $key => $value) {

            //check for numeric (internal class value exceptions)
            if (
                $key !== "result"
                && $key !== "err_flag"
                && $key !== "costPrototypes"
                && $key !== "costInventory"
                && $key !== "seedSalesCostForRound"
                && $key !== "salesCostForRound"
                && $key !== "startUpCost"
                && $key !== "owners_daily_cost"
                && $key !== "manager_daily_cost"
                && $key !== "labor_daily_cost"
                && $key !== "employees_daily_cost"
                && $key !== "this_rounds_months"
                && $key !== "employee_monthly_cost"
                && $key !== "monthly_supply_cost"
            ) {
                if (!is_numeric($value)) {
                    $this->errorOut("$key with value '$value' is not numeric.");
                    break;
                };
            }
            //check for 0
            if($value===0){
                $this->errorOut("$key cannot be '0'.");
                break;
            };

        }
    }
}
