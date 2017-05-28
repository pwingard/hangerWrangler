<?php
namespace html_docs\hanger_wrangler;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ ."/../../php/ReusableClasses/MathClass/MathClass.php";
use \php\ReusableClasses\MathClass\MathClass;

class HangerWrangler {

    //user can overwrite any public on input

    //starting params
    public $daily_avg_sold = 100;
    private $days_this_round;
    public $inventory_this_round =  3000;

    //used in startup costs
    public $patent = 18000;
    public $legal_accounting = 6000;

    public $prototyping_cost_unit = 100;
    public $number_prototypes = 10;
    private $cost_prototypes;

    public $percent_cost_of_sales_as_seed =  .25;
    private $seedSalesCostForRound;


    public $inventory_cost_per_unit = 10;
    private $cost_inventory;

    //selling costs
    private $work_days_per_month = 22;
    public $monthly_rent = 1200;
    private $daily_rent;
    public $monthly_utilities = 150;
    public $daily_utilities;
    public $supply_cost_per_unit = 1.25;
    private $daily_supply_cost;

    public function __construct() {
        $this->intialize();
        if(isset($this->err_flag)) return $this->result;
    }

    private function intialize(){

        $Math = new MathClass;

        //days_this_round
        $a = $this->inventory_this_round;
        $b = $this->daily_avg_sold;

        $c = $Math->setInput([$a,$b])
            ->isDivisable()->div()->rnd(2)->result;

        if( $c ){
            $this->days_this_round =  $c;
        } else {
            $this->errorOut("days_this_round.");
        }

        //cost of prototype
        $a = $this->prototyping_cost_unit;
        $b = $this->number_prototypes;

        $c = $Math->setInput([$a,$b])
            ->isNumeric()->mul()->rnd(2)->result;

        if( $c ){
            $this->cost_prototypes =  $c;
        } else {
            $this->errorOut("cost_prototypes.");
        }

        //daily_rent
        $a = $this->monthly_rent;
        $b = $this->work_days_per_month;

        $c = $Math->setInput([$a,$b])
            ->isDivisable()->div()->rnd(2)->result;

        if( $c ){
            $this->daily_rent =  $c;
        } else {
            errorOut("daily_rent failed.");
        }

        //daily_utilities
        $a = $this->monthly_utilities;
        $b = $this->work_days_per_month;

        $c = $Math->setInput([$a,$b])
            ->isDivisable()->div()->rnd(2)->result;

        if( $c ){
            $this->daily_utilities =  $c;
        } else {
            $this->errorOut("work_days_per_month failed.");
        }

        //cost_inventory
        $a = $this->inventory_this_round;
        $b = $this->inventory_cost_per_unit;

        $c = $Math->setInput([$a,$b])
            ->isNumeric()->mul()->rnd(2)->result;

        if( $c ){
            $this->cost_inventory =  $c;
        } else {
            $this->errorOut("cost_inventory failed.");
        }

        //supply_cost_this_round
        $a = $this->supply_cost_per_unit;
        $b = $this->inventory_this_round;

        $c = $Math->setInput([$a,$b])
            ->isNumeric()->mul()->rnd(2)->result;

        if( $c ){
            $this->supply_cost_this_round =  $c;
            //$this->daily_supply_cost =  $c / ;
        } else {
            $this->errorOut("supply_cost_this_round failed.");
        }

        //daily_supply_cost
        $a = $this->daily_avg_sold;
        $b = $this->supply_cost_per_unit;

        $c = $Math->setInput([$a,$b])
            ->isNumeric()->mul()->rnd(2)->result;

        if( $c ){
            $this->daily_supply_cost =  $c;
        } else {
            $this->errorOut("daily_supply_cost failed.");
        }

        //$this->dailyCOS();//cost of sales for round

    }

    private function dailyCOS(){
//round 1
//avg/sold/day
//days to sell *c
//man-hour/day *c
//cost/hr multiple
//cost/day *c √
//rent/mth
//utilities/mth
//supplies/mth *c√
        //supply_cost_this_round
//        $a = $this->daily_rent;
//        $b = $this->daily_utilities;
//        $c = $this->supp;
//
//        $c = $Math->setInput([$a,$b])
//            ->isNumeric()->mul()->rnd(2)->result;
//
//        if( $c ){
//            $this->supply_cost_this_round =  $c;
//        } else {
//            $this->errorOut("supply_cost_this_round failed.");
//        }
    }

    private function round(){
        return round($this,2);
    }

    /**
     * @method checks value for the given type
     * @param single array $argArr w indices of "type"=>"type" and "val"=>"value
     * @return boolean typeCheck()
     */
    private function typeCheck($argArr) {
        switch ($argArr["type"]) {
            case "numeric":
                return (is_numeric($argArr["val"])) ? true : false;
                break;
            case "string":
                return (is_string($argArr["val"])) ? true : false;
                break;
            case "zero":
                return (($argArr["val"]) === 0 ) ? true : false;
                break;
            case "integer":
                return (is_integer($argArr["val"])) ? true : false;
                break;
            case "boolean":
                return (is_bool($argArr["val"])) ? true : false;
                break;
            case "array":
                return (is_array($argArr["val"])) ? true : false;
                break;
            case "float":
                return (is_float($argArr["val"])) ? true : false;
                break;
            default:
                return false;
                break;
        }
    }

    /**
     * sets the $this->err_flag as true & $this->err_msg as $msg
     * @method
     * @param $msg string errorOut()
     * @return
     */
    private function errorOut($msg) {
        $this->err_flag = true;
        $this->result = $msg;

        if (!["val" => $msg, "type" => "string"]) {
        $this->err_msg = "$msg passed not a string ". $this->err_msg;
        }
    }

}
