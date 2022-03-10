<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Http\Controllers\DialogflowAPI;

class dialogflowWebhook extends Controller
{

    public function test(){
        
        
        
    

        $method = $_SERVER['REQUEST_METHOD'];

        $drinkPrices = array(
            "americano"=> 100,
            "barista coffee"=> 150,
            "cappuccino"=> 175,
            "cocoa"=> 75,
            "espresso"=> 90,
            "tea"=> 50,
            "coffee"=> 120,
            "affogato" => 150,
        );

        $sizePrices = array(
            "large"=> 20,
            "medium"=> 10,
            "small"=> 0,
        );


        $deliveryPrices = 0;


        $icedPrices = 0;

        // Process only when method is POST
        if($method == 'POST'){
            $requestBody = file_get_contents('php://input');

            $df = new DialogflowAPI();
            $df->setRequest($requestBody);
            $df->setFlowAddress('###ADDRESS OF FLOW HERE###');

            if(strcasecmp($df->getTag(),"question1")==0){
                $speech = "what drink would you like?";
                $df->sendResponse($speech);
            }

            if(strcasecmp($df->getTag(),"fill_drink")==0){
                $speech = "I'm sorry, I might have missed something. what drink would you like?";
                $df->sendResponse($speech);
            }
            
            if(strcasecmp($df->getTag(),"fill_size")==0){
                $speech = "what size?";
                $df->sendResponse($speech);
            }

            if(strcasecmp($df->getTag(),"fill_delivery")==0){
                $speech = "for pickup or delivery?";
                $df->sendResponse($speech);
            }

            if(strcasecmp($df->getTag(),"confirm_order")==0){
                $drink = $df->getParameter('drink');
                $drink = str_replace(' ', '', $drink);
                $size = $df->getParameter('size');
                $iced = $df->getParameter('iced');
                $qty = $df->getParameter('qty');
                $delivery_pickup = $df->getParameter('delivery_pickup');
                
                $speech = "
                your order is:
                drink: $drink\n
                iced: $iced\n
                size: $size\n
                qty: $qty\n
                for $delivery_pickup\n\n

                is this correct?
                ";
                $df->sendResponse($speech);
            }

            if(strcasecmp($df->getTag(),"yes_confirm")==0){
                $drink = $df->getParameter('drink');
                $drink = str_replace(' ', '', $drink);
                $size = $df->getParameter('size');
                $iced = $df->getParameter('iced');
                $qty = $df->getParameter('qty');
                $delivery_pickup = $df->getParameter('delivery_pickup');

                if(strcasecmp($delivery_pickup,"delivery")==0){
                    $deliveryPrices = 10;
                }
                
                if(strcasecmp($iced,"true")==0){
                    $icedPrices = 5;
                }

                $totalBill=($qty*($icedPrices+$sizePrices[$size]+$drinkPrices[$drink]))+$deliveryPrices;

                $speech="
                receipt\n
                drink:$drink\n
                iced:$iced\n
                size:$size\n
                qty:$qty\n
                method:$delivery_pickup\n
                Total: P$totalBill\n
                ";
                $df->sendResponse($speech);
            }

            if(strcasecmp($df->getTag(),"no_confirm")==0){
                $speech = "Sorry, let me take your order again.";
                $ask_order_page= "e4dc15ea-9704-4ea8-b90d-c48d1caf7de0";
                $df->sendResponseThenGoto($speech,$ask_order_page);
            }

            if(strcasecmp($df->getTag(),"thanks")==0){
                $speech = "";
                $df->sendResponse($speech);
            }





            
            
        }
        else
        {
            echo "Method not allowed";
        }

    }

}


?>
