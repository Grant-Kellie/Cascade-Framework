<?php 
namespace App\Admin;
class ControlPanel {

    public function index($request){
        echo '<h2>Admin Control Panel</h2>';
    }

    public function users($request){
        echo __FUNCTION__ . '<br>';
        exit;        
    }

    public function create_user($request){
        echo __FUNCTION__ . '<br>';
        exit;
    }

    public function delete_user($request){
        echo __FUNCTION__ . '<br>';
        exit;
    }

    /***
    * @method outputCoreData
     * @param array $request
     * 
     * Outputs test data from request / response, user session
     */
    public function outputCoreData($request){
        echo '<h2>' . __FUNCTION__ . '</h2>';
        echo '<pre>';
        print_r($request);
        print_r($_SESSION['cascade']); 
        exit;    
    }


}