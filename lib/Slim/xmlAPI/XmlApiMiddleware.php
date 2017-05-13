<?php
/**
 * XmlAPI - Slim extension to implement fast XML API's
 *
 * @package Slim
 * @subpackage Middleware
 * @author
 * @license GNU General Public License, version 3
 */

/**
 * XmlApiMiddleware - Middleware that sets a bunch of static routes for easy bootstrapping of XML API's
 *
 * @package Slim
 * @subpackage View
 * @author
 * @license GNU General Public License, version 3
 */
class XmlApiMiddleware extends \Slim\Middleware {
	/**
	 * Sets a buch of static API calls
	 *
	 */
	function __construct() {
		$app = \Slim\Slim::getInstance();
		$app->config('debug', false);

		// Mirrors the API request
		$app->get('/return', function() use ($app) {
			$app->render(200,array(
				'method'    => $app->request()->getMethod(),
				'name'      => $app->request()->get('name'),
				'headers'   => $app->request()->headers(),
				'params'    => $app->request()->params(),
			));
		});

		// Generic error handler
		$app->error(function (Exception $e) use ($app) {
			parent::error($e);
		});

		// Not found handler (invalid routes, invalid method types)
		$app->notFound(function() use ($app) {
			throw new XmlApiException(null, -0001);
		});

		// Handle Empty response body
		$app->hook('slim.after.router', function () use ($app) {
			//Fix sugested by: https://github.com/bdpsoft
			//Will allow download request to flow
			if($app->response()->header('Content-Type')==='application/octet-stream'){
				return;
			}

			if (strlen($app->response()->body()) == 0) {
				$app->render(500,array(
					'error' => TRUE,
					'msg'   => 'Empty response',
				));
			}
		});
	}

	/**
	 * Call next
	 */
	function call() {
		$app = $this->app;

		$aPost = null;

		$app->hook('slim.before.router', function() use ($app) {
			$body = $app->request->getBody();

			if ($app->request->getMediaType()=='application/xml' && !empty($body)) {
				try {
					$params = $this->xml2array($body, 0);
				} catch (ErrorException $e) {
					$err_msg = sprintf(
						'Unknown error occured: %s, json: %s',
						str_replace("xml2array(): ", "", $e->getMessage()),
						$body);
					throw new XmlApiException(null, -0002, null, array($err_msg));
				}

				$app->request()->setBody($params['root']);
			}

			preg_match('/\/v\d+/i', $app->request->getRootUri(), $aMatches);

			if ($app->request->isPost() || $app->request->isPut()) {
				if (empty($params))
					throw new XmlApiException(null, -0002, null, array('Body cannot be empty.'));

				$aPost = $params;
			}

			switch(trim($aMatches[0], '/')) {
				case 'v2':
					$oModel = new Model();
					$oModel->log($aPost);
					break;
				case 'v3':
					$oModel = Model::getInstance();
					$oModel->log($aPost);
					break;
			}
		});

		$this->next->call();
	}

	/**
	 * xml2array() will convert the given XML text to an array in the XML structure.
	 * Link: http://www.bin-co.com/php/scripts/xml2array/
	 * Arguments : $contents - The XML text
	 *                $get_attributes - 1 or 0. If this is 1 the function will get the attributes as well as the tag values - this results in a different array structure in the return value.
	 *                $priority - Can be 'tag' or 'attribute'. This will change the way the resulting array structure. For 'tag', the tags are given more importance.
	 * Return: The parsed XML in an array form. Use print_r() to see the resulting array structure.
	 * Examples: $array =  xml2array(file_get_contents('feed.xml'));
	 *              $array =  xml2array(file_get_contents('feed.xml', 1, 'attribute'));
	 */
	public function xml2array($contents, $get_attributes=1, $priority = 'tag') {
		if (!$contents) return array();

		if (!function_exists('xml_parser_create')) {
			// print "'xml_parser_create()' function not found!";
			return array();
		}

		// Get the XML parser of PHP - PHP must have this module for the parser to work
		$parser = xml_parser_create('');

		xml_parser_set_option($parser, XML_OPTION_TARGET_ENCODING, "UTF-8"); # http://minutillo.com/steve/weblog/2004/6/17/php-xml-and-character-encodings-a-tale-of-sadness-rage-and-data-loss
		xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
		xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
		xml_parse_into_struct($parser, trim($contents), $xml_values);

		if (xml_get_error_code($parser) !== XML_ERROR_NONE) {
			$err_msg = sprintf('Post body is not xml format: %s', $contents);
			throw new XmlApiException(null, -0002, null, array($err_msg));
		}
		xml_parser_free($parser);

		if (!$xml_values) return; // Hmm...

		// Initializations
		$xml_array = array();
		$parents = array();
		$opened_tags = array();
		$arr = array();

		$current = &$xml_array; //Refference

		// Go through the tags.
		$repeated_tag_index = array(); // Multiple tags with same name will be turned into an array
		foreach ($xml_values as $data) {
			/* Fix Ingenicent v2 root element */
			if (intval($data['level'])==1 && $data['type']=='open' && $data['tag']!='root')
				throw new XmlApiException(null, -0002, null, array('XML structure must begin with <root> element'));
			/* End - Fix Ingenicent v2 root element */
				
			unset($attributes, $value); // Remove existing values, or there will be trouble

			// This command will extract these variables into the foreach scope
			// tag(string), type(string), level(int), attributes(array).
			extract($data); // We could use the array by itself, but this cooler.

			$result = array();
			$attributes_data = array();

			if (isset($value)) {
				if ($priority=='tag') $result = $value;
				else $result['value'] = $value; // Put the value in a assoc array if we are in the 'Attribute' mode
			}

			// Set the attributes too.
			if (isset($attributes) and $get_attributes) {
				foreach ($attributes as $attr => $val) {
					if ($priority=='tag') $attributes_data[$attr] = $val;
					else $result['attr'][$attr] = $val; // Set all the attributes in a array called 'attr'
				}
			}

			// See tag status and do the needed.
			if ($type == "open") { // The starting of the tag '<tag>'
				$parent[$level-1] = &$current;
				if (!is_array($current) or (!in_array($tag, array_keys($current)))) { // Insert New tag
					$current[$tag] = $result;
					if ($attributes_data) $current[$tag. '_attr'] = $attributes_data;
					$repeated_tag_index[$tag.'_'.$level] = 1;

					$current = &$current[$tag];
				} else { // There was another element with the same tag name
					if (isset($current[$tag][0])) { // If there is a 0th element it is already an array
						$current[$tag][$repeated_tag_index[$tag.'_'.$level]] = $result;
						$repeated_tag_index[$tag.'_'.$level]++;
					} else { // This section will make the value an array if multiple tags with the same name appear together
						$current[$tag] = array($current[$tag],$result); // This will combine the existing item and the new item together to make an array
						$repeated_tag_index[$tag.'_'.$level] = 2;

						if (isset($current[$tag.'_attr'])) { // The attribute of the last(0th) tag must be moved as well
							$current[$tag]['0_attr'] = $current[$tag.'_attr'];
							unset($current[$tag.'_attr']);
						}
	
					}
					$last_item_index = $repeated_tag_index[$tag.'_'.$level]-1;
					$current = &$current[$tag][$last_item_index];
				}
			} else if ($type == "complete") { // Tags that ends in 1 line '<tag />'
				// See if the key is already taken.
				if (!isset($current[$tag])) { // New Key
					$current[$tag] = $result;
					$repeated_tag_index[$tag.'_'.$level] = 1;
					if ($priority=='tag' and $attributes_data) $current[$tag. '_attr'] = $attributes_data;
				} else { // If taken, put all things inside a list(array)
					if (isset($current[$tag][0]) and is_array($current[$tag])) { // If it is already an array...
						// ...push the new element into that array.
						$current[$tag][$repeated_tag_index[$tag.'_'.$level]] = $result;

						if ($priority=='tag' and $get_attributes and $attributes_data) {
							$current[$tag][$repeated_tag_index[$tag.'_'.$level] . '_attr'] = $attributes_data;
						}
						$repeated_tag_index[$tag.'_'.$level]++;
					} else { // If it is not an array...
						$current[$tag] = array($current[$tag],$result); // ...Make it an array using using the existing value and the new value
						$repeated_tag_index[$tag.'_'.$level] = 1;
						if ($priority=='tag' and $get_attributes) {
							if (isset($current[$tag.'_attr'])) { // The attribute of the last(0th) tag must be moved as well
								$current[$tag]['0_attr'] = $current[$tag.'_attr'];
								unset($current[$tag.'_attr']);
							}

							if ($attributes_data) {
								$current[$tag][$repeated_tag_index[$tag.'_'.$level] . '_attr'] = $attributes_data;
							}
						}
						$repeated_tag_index[$tag.'_'.$level]++; // 0 and 1 index is already taken
					}
				}
			} else if ($type == 'close') { // End of tag '</tag>'
				$current = &$parent[$level-1];
			}
		}

		return $this->recursiveArrayFilter($xml_array);
	}

	public function recursiveArrayFilter($paArray) {
		foreach ($paArray as &$mValue) {
			if (is_array($mValue))
				$mValue = $this->recursiveArrayFilter($mValue);
		}
	
		return array_filter($paArray);
	}
}
