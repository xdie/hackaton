<?php
/**
 * mlAPI - Slim extension to implement fast XML API's
 *
 * @package Slim
 * @subpackage View
 * @author
 * @license GNU General Public License, version 3
 */

/**
 * XmlApiView - view wrapper for xml responses (with error code).
 *
 * @package Slim
 * @subpackage View
 * @author
 * @license GNU General Public License, version 3
 */
class XmlApiView extends \Slim\View {
    public $encodingOptions = 0;

    public $sEntityName = '';

    public function __construct($psEntityName) {
        parent::__construct();
        $this->sEntityName = $psEntityName;
    }

    public function render($status=200, $data = NULL) {
        $app = \Slim\Slim::getInstance();

        $status = intval($status);

        $response = $this->all();

        //append error bool
        if (!$this->has('error')) {
            $response['error'] = false;
        }

        //append status code
        //$response['status'] = $status;

		//add flash messages
		if (isset($this->data->flash) && is_object($this->data->flash)) {
		    $flash = $this->data->flash->getMessages();
            if (count($flash)) {
                $response['flash'] = $flash;   
            } else {
                unset($response['flash']);
            }
		}

        $app->response->setStatus($status);
        $app->response()->header('Content-Type', 'application/xml');

        $jsonp_callback = $app->request->get('callback', null);
        $app->response()->body($this->array2xml($response));

        $app->stop();
    }

    /**
     * http://php.net/manual/es/domdocument.createcdatasection.php
     *
     * @param unknown $ar
     * @param string $root_element_name
     * @return mixed
     */
    public function array2xml($ar, $root_element_name='root') {
        $xml = new XmlApiSimpleXMLExtended("<?xml version=\"1.0\"?><{$root_element_name}></{$root_element_name}>");
        $f = create_function('$f,$c,$a','
            foreach ($a as $k=>$v) {
                if (is_array($v)) {
                    if (!is_numeric($k)) $ch = $c->addChild($k);
                    else $ch = $c->addChild(substr($c->getName(), 0, -1));
                    $f($f, $ch, $v);
                } else {
                    if (is_numeric($v)) {
                    $c->addChild($k, $v);
                    } else {
                    $n = $c->addChild($k);
                    if (!empty($v)) $n->addCData($v);
                    }
                }
            }');
        $f ($f, $xml, $ar);
        return $xml->asXML();
    }

    /**
     * http://php.net/manual/es/domdocument.createcdatasection.php
     *
     * @param unknown $ar
     * @param string $root_element_name
     * @return mixed
     */
    public function array2xml_new($ar, $root_element_name='root') {
        $ename = $this->sEntityName;
        $xml = new XmlApiSimpleXMLExtended("<?xml version=\"1.0\"?><{$root_element_name}></{$root_element_name}>");
        $f = create_function('$f,$c,$a,$ename','
            $aEntitiesNames = array(
//                 "action" => "actions",
                "action-type" => "actions-types",
//                 "badge_type" => "badges_types",
                "campaign" => "campaigns",
//                 "campaign_action_feed" => "campaigns_actions_feeds",
//                 "campaign_badge" => "campaigns_badges",
//                 "campaign_gallery" => "campaigns_galleries",
//                 "campaign_gallery_branding_setup" => "campaigns_galleries_branding_setup",
//                 "campaign_location" => "campaigns_locations",
                "currency" => "currencies",
                "customer" => "customers",
//                 "customer_dynamic_form" => "customers_dynamic_forms",
//                 "customer_user" => "customers_users",
//                 "dynamic_form" => "dynamic_forms",
//                 "dynamic_form_allowed_field" => "dynamic_forms_allowed_fields",
//                 "field_type" => "fields_types",
                "media" => "medias",
                "media_library" => "medias_libraries",
                "message" => "messages",
                "quizz" => "quizzes",
//                 "quizz_answer" => "quizzes_answers",
//                 "quizz_question" => "quizzes_questions",
//                 "quizz_user" => "quizzes_users",
                "reward" => "rewards",
                "reward-default" => "rewards-default",
//                 "reward_exchange" => "rewards_exchange",
//                 "reward_order" => "rewards_order",
                "tag" => "tags",
                "tag-default" => "tags-default",
//                 "tag_read" => "tags_read",
//                 "tag_type" => "tags_types",
                "trivia" => "trivias",
//                 "trivia-default" => "trivias-default",
//                 "trivia_response" => "trivias_response",
//                 "trivia_sequential" => "trivias_sequentials",
                "trivia-sequential-default" => "trivias-sequentials-default",
//                 "trivia_sequential_response" => "trivias_sequentials_response",
//                 "trivia_sequential_value" => "trivias_sequentials_values",
//                 "trivia_value" => "trivias_values",
                "user" => "users",
//                 "user_tracking" => "users_tracking",
//                 "wall_message" => "wall_messages",
        		"badge" => "badges",
        		"feed" => "feeds"
            );
            foreach ($a as $k=>$v) {
//         		error_log("\n {$k} => {$ename} \n", 3, __DIR__."/mylog.log");
//              error_log("\n {$k} => {$v} => ".print_r($c, true)."\n", 3, __DIR__."/mylog.log");
                if (is_array($v)) {
                    if (!is_numeric($k)) {
                        $ch = $c->addChild($k);
                    } else {
// 						if ($c->getName() == "data")
//         					$_k = array_search($ename, $aEntitiesNames);
//         				else if ($k == "customer_form")
//         					$_k = array_search($k, $aEntitiesNames);
//         				else
//         					$_k = substr($c->getName(), 0, -1);
                        $_k = ($c->getName()!="data") ? substr($c->getName(), 0, -1) : array_search($ename, $aEntitiesNames);
                        $ch = $c->addChild($_k);
                    }
                    $f($f, $ch, $v, $ename);
                } else {
                    if (is_numeric($v)) {
                        $_k = $k;
//                         switch ($c->getName()) {
//                             case "customer_form":
//                             case "logs":
//                                 $_k = array_search($c->getName(), $aEntitiesNames);
//                                 break;
//                         }
                        $c->addChild($_k, $v);
                    } else {
                        $n = $c->addChild($k);
                        if (!empty($v)) $n->addCData($v);
                    }
                }
            }');
        $f ($f, $xml, $ar, $ename);
        return $xml->asXML();
    }
}
