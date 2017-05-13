<?php
/**
 * Slim - a micro PHP 5 framework
 *
 * @author      Josh Lockhart <info@slimframework.com>
 * @copyright   2011 Josh Lockhart
 * @link        http://www.slimframework.com
 * @license     http://www.slimframework.com/license
 * @version     2.4.2
 * @package     Slim
 *
 * MIT LICENSE
 *
 * Permission is hereby granted, free of charge, to any person obtaining
 * a copy of this software and associated documentation files (the
 * "Software"), to deal in the Software without restriction, including
 * without limitation the rights to use, copy, modify, merge, publish,
 * distribute, sublicense, and/or sell copies of the Software, and to
 * permit persons to whom the Software is furnished to do so, subject to
 * the following conditions:
 *
 * The above copyright notice and this permission notice shall be
 * included in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
 * MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
 * NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
 * LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
 * OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION
 * WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */
namespace Slim;

/**
 * Middleware
 *
 * @package Slim
 * @author  Josh Lockhart
 * @since   1.6.0
 */
abstract class Middleware
{
    /**
     * @var \Slim\Slim Reference to the primary application instance
     */
    protected $app;

    /**
     * @var mixed Reference to the next downstream middleware
     */
    protected $next;

    /**
     * Set application
     *
     * This method injects the primary Slim application instance into
     * this middleware.
     *
     * @param  \Slim\Slim $application
     */
    final public function setApplication($application)
    {
        $this->app = $application;
    }

    /**
     * Get application
     *
     * This method retrieves the application previously injected
     * into this middleware.
     *
     * @return \Slim\Slim
     */
    final public function getApplication()
    {
        return $this->app;
    }

    /**
     * Set next middleware
     *
     * This method injects the next downstream middleware into
     * this middleware so that it may optionally be called
     * when appropriate.
     *
     * @param \Slim|\Slim\Middleware
     */
    final public function setNextMiddleware($nextMiddleware)
    {
        $this->next = $nextMiddleware;
    }

    /**
     * Get next middleware
     *
     * This method retrieves the next downstream middleware
     * previously injected into this middleware.
     *
     * @return \Slim\Slim|\Slim\Middleware
     */
    final public function getNextMiddleware()
    {
        return $this->next;
    }

    /**
     * Call
     *
     * Perform actions specific to this middleware and optionally
     * call the next downstream middleware.
     */
    abstract public function call();


    public function error($e) {
    	$app = $this->getApplication();

    	//preg_match('/\/v\d+/i', $e->getFile(), $aMatches);
    	//switch (trim($aMatches[0], '/')) {
    	switch (trim($app->request->getRootUri(), '/')) {
    		case 'v2':
    		default:
		    	$aExceptionsCodes = array(
		    		400 => array(-0002,-1002,-3002,-8002,-10002,-12002,-15002,-16002,-17006,-22002,-24002,-25002,-27002,-30002,-32002,-34002,-35002),
		    		401 => array(-1000,-1001,-2000,-2001,-2002,-17002,-17003,-17004),
		    		403 => array(-8003,-12003,-13003,-32003),
		    		404 => array(-0001,-3000,-4000,-5000,-6000,-7000,-8000,-10000,-11000,-12000,-13000,-14000,-15000,-17000,-18000,-19000,-20000,-22000,-23000,-25000,-26000,-27000,-28000,-29000,-31000,-32000,-36000,-39000,-40000,-41000,-42000,-45000,-46000,-47000,-48000,-49000,-50000),
		    		409 => array(-3001,-4001,-5001,-6001,-7001,-8001,-9001,-9003,-10001,-12001,-13001,-17005,-21001,-22001,-23001,-26001,-28001,-29001,-31001,-32001,-33001,-33003,-36001,-37001,-38001,-39001,-40001,-43001,-44001,-25001,-51001),
		    		500 => array(-25002)
		    	);

		    	switch (get_class($e)) {
		    		case 'PDO_Wrapper_Exception':
		    			$app->render(500, array(
		    				'code' => 500,
		    				'status' => 'error',
		    				'error' => true,
		    				'message' => $e->getMessage(),
		    				'data' => array(
		    					'exception_name' => get_class($e),
		    					'exception_code' => $e->getCode()
		    				)
		    			));
		    			break;
		    		case 'Action_Exception':
		    		case 'Action_Type_Exception':
		    		case 'Badge_Exception':
		    		case 'Badge_Rule_Exception':
		    		case 'Campaign_Exception':
		    		case 'Campaign_Action_Feed_Exception':
		    		case 'Campaign_Branding_Exception':
		    		case 'Campaign_Gallery_Exception':
		    		case 'Campaign_Gallery_Branding_Setup_Exception':
		    		case 'Campaign_Gallery_Hashtag_Exception':
		    		case 'Campaign_Gallery_Social_Network_Exception':
		    		case 'Campaign_Gallery_Suggested_Hashtag_Exception':
		    		case 'Campaign_Hashtag_Exception':
		    		case 'Campaign_Location_Exception':
		    		case 'Currency_Exception':
		    		case 'Customer_Exception':
		    		case 'Customer_Dynamic_Form_Exception':
		    		case 'Customer_User_Exception':
		    		case 'Dynamic_Form_Exception':
		    		case 'Field_Type_Exception':
		    		case 'Hashtag_Exception':
		    		case 'Media_Exception':
		    		case 'Media_Library_Exception':
		    		case 'Message_Exception':
		    		case 'Quiz_Exception':
		    		case 'Quiz_Question_Exception':
		    		case 'Quiz_Answer_Exception':
		    		case 'Quiz_User_Exception':
		    		case 'Reward_Exception':
		    		case 'Reward_Default_Exception':
		    		case 'Reward_Exchange_Exception':
		    		case 'Reward_Order_Exception':
		    		case 'Rule_Action_Exception':
		    		case 'Rule_Action_Concept_Exception':
		    		case 'Rule_Comparation_Exception':
		    		case 'Social_NEtwork_Exception':
		    		case 'Tag_Exception':
		    		case 'Tag_Default_Exception':
		    		case 'Tag_Read_Exception':
		    		case 'Tag_Type_Exception':
		    		case 'Target_EXception_Exception':
		    		case 'Target_Type_Exception':
		    		case 'Trivia_Exception':
		    		case 'Trivia_Default_Exception':
		    		case 'Trivia_Response_Exception':
		    		case 'Trivia_Sequential_Exception':
		    		case 'Trivia_Sequential_Default_Exception':
		    		case 'Trivia_Sequential_Response_Exception':
		    		case 'Trivia_Sequential_Value_Exception':
		    		case 'Trivia_Value_Exception':
		    		case 'User_Exception':
		    		case 'User_Tracking_Exception':
		    		case 'Wall_Message_Exception':
		    			$iHttpStatusCode = self::_recursiveArraySearch($e->getCode(), $aExceptionsCodes);
		    			$app->render($iHttpStatusCode, array(
		    				'code' => $iHttpStatusCode,
		    				'status' => 'error',
		    				'error' => true,
		    				'message' => $e->getMessage(),
		    				'data' => array(
		    					'exception_name' => get_class($e),
		    					'exception_code' => $e->getCode(),
		    					'exception_errors' => $e->getErrors()
		    				)
		    			));
		    			break;
		    		case 'FormDataApiException':
		    		case 'JsonApiException':
		    		case 'XmlApiException':
		    			$iHttpStatusCode = self::_recursiveArraySearch($e->getCode(), $aExceptionsCodes);
		    			$app->render($iHttpStatusCode, array(
		    				'code' => $iHttpStatusCode,
		    				'status' => 'error',
		    				'error' => true,
		    				'message' => $e->getMessage(),
		    				'data' => array(
		    					'exception_errors' => $e->getErrors()
		    				)
		    			));
		    			break;
		    		default:
		    			$app->render(500, array(
		    				'error' => true,
		    				'msg' => self::_errorType($e->getCode()) .": ". $e->getMessage()
		    			));
		    			break;
		    	}

    			break;
    		case 'v3':
    			switch (get_class($e)) {
    				case 'PDO_Wrapper_Exception':
    					$app->render(500, array(
    						'code' => 500,
    						'status' => 'error',
    						'error' => true,
    						'message' => $e->getMessage(),
    						'data' => array(
    							'exception_name' => get_class($e),
    							'exception_code' => $e->getCode()
    						)
    					));
    					break;
    				default:
    					$iHttpStatusCode = $e->getHttpStatusCode();
    					$app->render($iHttpStatusCode, array(
    						'code' => $iHttpStatusCode,
    						'status' => 'error',
    						'error' => true,
    						'message' => $e->getMessage(),
    						'data' => array(
    							'exception_name' => get_class($e),
    							'exception_code' => $e->getCode(),
    							'exception_errors' => $e->getErrors()
    						)
    					));
    					break;
    			}

    			break;
    	}
    }

    static function _recursiveArraySearch($needle, $haystack) {
    	$current_key = false;

    	foreach ($haystack as $key=>$value) {
    		if ($needle===$value OR (is_array($value) && self::_recursiveArraySearch($needle, $value)!==false)) {
    			$current_key = $key;
    			break;
    		}
    	}

    	return $current_key;
    }

    static function _errorType($type=1) {
    	switch ($type) {
    		default:
    		case E_ERROR: // 1 //
    			return 'ERROR';
    		case E_WARNING: // 2 //
    			return 'WARNING';
    		case E_PARSE: // 4 //
    			return 'PARSE';
    		case E_NOTICE: // 8 //
    			return 'NOTICE';
    		case E_CORE_ERROR: // 16 //
    			return 'CORE_ERROR';
    		case E_CORE_WARNING: // 32 //
    			return 'CORE_WARNING';
    		case E_CORE_ERROR: // 64 //
    			return 'COMPILE_ERROR';
    		case E_CORE_WARNING: // 128 //
    			return 'COMPILE_WARNING';
    		case E_USER_ERROR: // 256 //
    			return 'USER_ERROR';
    		case E_USER_WARNING: // 512 //
    			return 'USER_WARNING';
    		case E_USER_NOTICE: // 1024 //
    			return 'USER_NOTICE';
    		case E_STRICT: // 2048 //
    			return 'STRICT';
    		case E_RECOVERABLE_ERROR: // 4096 //
    			return 'RECOVERABLE_ERROR';
    		case E_DEPRECATED: // 8192 //
    			return 'DEPRECATED';
    		case E_USER_DEPRECATED: // 16384 //
    			return 'USER_DEPRECATED';
    	}
    }
}
