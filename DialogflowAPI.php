<?php
namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DialogflowAPI{

    private $json=null;
    private $flow='';

    /**
     * function used to set the flow address. flow address must be set inorder to use the sendResponseThenGoto() function.
     * @param string $agent_address agent url
     * 
     */
    public function setFlowAddress($flow_address){
        $flow_address=str_replace("https://dialogflow.cloud.google.com/cx/","",$flow_address);
        $flow_address=str_replace("dialogflow.cloud.google.com/cx/","",$flow_address);
        $flow_address=str_replace("flow_creation","pages/",$flow_address);
        $this->flow=$flow_address;
    }

    public function getFlowAddress(){
        return $this->flow;
    }

    /**
     *
     * function used to decode the received Dialogflow CX post request.
     * 
     * @param string $request_body post request in JSON sent by DF CX
     * 
     */
    public function setRequest($request_body){
        $this->json = json_decode($request_body);
    }

    /**
     * function that returns set JSON post request. returns null when not set
     * @return object json JSON file that was set using 
     */
    public function getRequest(){
        return $this->json;
    }

    /**
     * function that returns session address. 
     * @return string session address
     */
    public function getSession(){
        return $this->json->sessionInfo->session;
    }

    /**
     * function used for fetching non-object parameters. 
     * @param string $parameter_name name of parameter to fetch 
     */
    public function getParameter($parameter_name){
        return $this->json->sessionInfo->parameters->$parameter_name;
    }
    
    /**
     * function used for fetching the Sys.name entity type. 
     * 
     * @param string $parameter_name name of paremeter to fetch
     * @param int $mode 0=original, 1=name
     * 
     */
    public function getParamSysName($parameter_name, $mode){
        $out='';
        switch($mode){
            case 0:
                $out=$this->json->sessionInfo->parameters->$parameter_name->name;
                break;
            case 1:
                $out=$this->json->sessionInfo->parameters->$parameter_name->original;
                break;

            default:
                $out=" no  mode $mode exist";

        }
        return $out;
    }

    /**
     * function used for getting Intent Response ID of DF CX post request. 
     * 
     * @return string IntentResponseId Intent Response ID.
     */
    public function getIntentResponseID(){
        return $this->json->detectIntentResponseId;
    }

    /**
     * function for getting last matched intent
     * @return string lastMatchedIntent
     */
    public function getLastMatchedIntent(){
        return $this->json->intentInfo->lastMatchedIntent;
    }

    /**
     * function for getting the display name of the intent which sent the post request.
     * @return string displayName name of intent 
     */
    public function getDisplayName(){
        return $this->json->intentInfo->displayName;
    }

    /**
     * function for getting the confidence value of the DF CX agent.
     * @return int confidence confidence value of agent.
     */
    public function getconfidence(){
        return $this->json->intentInfo->confidence;
    }

    /**
     * returns the address of current page.
     * @return string currentPage address of current page.
     */
    public function getcurrentPageInfo(){
        return $this->json->pageInfo->currentPage;
    }

    /**
     * returns formInfo values based on the given key.
     * @param string $key key
     * @return string value value for the corresponding key.
     */
    public function getFormInfo($key){
        return $this->json->pageInfo->formInfo->$key;
    }

    /**
     * returns the tag of webhook post request. 
     * @return string tag tag 
     */
    public function getTag(){
        return $this->json->fulfillmentInfo->tag;
    }

    /**
     * returns the raw reply of the user.
     * @return string reply raw reply of user
     */
    public function getUserReply(){
        return $this->json->text;
    }

    /**
     * returns the language code that the agent uses.
     * @return string languageCode language code used by agent.
     */
    public function getLanguageCode(){
        return $this->json->laguageCode;
    }

    /**
     * a function that creates and sends a simple JSON response to DF CX.
     * 
     * @param string $speech what the agent will reply to the user.
     *  
     */
    public function sendResponse($speech){
        $innerText=array('text'=>array($speech));
        $messages=array('text'=>$innerText);
        $fulfillment=array('messages'=>array($messages));
        $response = new \stdClass();
        $response->fulfillment_response = $fulfillment;
        echo json_encode($response);
    }

    /**
     * a functon that creates and sends a JSON response with session info to DF CX.
     * @param string $speech what the agent will reply to the user.
     * @param string $session_info session info (usually obtained by using getSession function)
     */
    public function sendResponseInfo($speech,$session_info){
        $innerText=array('text'=>array($speech));
        $messages=array('text'=>$innerText);
        $fulfillment=array('messages'=>array($messages));
        $response = new \stdClass();
        $response->fulfillment_response = $fulfillment;
        $session = array("session"=>$session_info);
        $response->sessionInfo=$session;
        echo json_encode($response);
    }

    /**
     * a functon that creates and sends a JSON response with a target page.
     * @param string $speech what the agent will reply to the user.
     * @param string $target_page session info (usually obtained by using getSession function)
     */
    public function sendResponseThenGoto($speech,$target_page){
        $innerText=array('text'=>array($speech));
        $messages=array('text'=>$innerText);
        $fulfillment=array('messages'=>array($messages));
        $response = new \stdClass();
        $response->fulfillment_response = $fulfillment;
        $response->targetPage = $this->flow.''.$target_page;
        echo json_encode($response);
    }


}



?>