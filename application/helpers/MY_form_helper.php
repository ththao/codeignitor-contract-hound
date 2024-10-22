<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Get the value from either post or get (in that order)
 *   or return default
 *
 * @access global
 * @param string $field
 * @param mixed  $default
 * @return mixed
 */
if ( ! function_exists('get_post_value'))
{
	function get_post_value($field = '', $default = '')
	{
		if (!isset($_POST[$field])) {
			return $default;
		} else {
			return $_POST[$field];
		}
	}
}

function get_post_value_safe($field='',$default='') {
	return str_replace(array('"',"'"),array('&quot;','&#039;'),escape_tags(get_post_value($field,$default)));
}

// ------------------------------------------------------------------------

if ( ! function_exists('my_form_input')) {
	function my_form_input($data = '', $value = '', $extra = '', $alt_name='') {
		if (is_array($data) && isset($data['name'])) {
			$name = $data['name'];
		} else {
			$name = $data;
		}

		if (is_array($extra)) {
			if (!empty($extra['instructions'])) {
				$instructions = $extra['instructions'];
			}
			if (!empty($extra['include'])) {
				$extra = $extra['include'];
			} else {
				$extra = '';
			}
		}

		$error = form_error($name);
		if (!empty($error)) {
			if (strpos($extra,'class="') !== false) {
				$extra = str_replace('class="','class="error ',$extra);
			} else {
				$extra = $extra . ' class="error"';
			}
		}

		$defaults = array('type' => 'text', 'name' => (( ! is_array($data)) ? $data : ''), 'value' => $value);

		if (empty($alt_name)) {
			$alt_name = str_replace('_',' ',$name);
			$alt_name = ucwords($alt_name);
		}
		$sReturn = '<div class="input text">';
		if (!empty($data['type']) && $data['type'] != 'hidden') {
			$sReturn .= "<label for=\"{$data}\">{$alt_name}</label>\n";
		}
		$sReturn .= "<input "._parse_form_attributes($data, $defaults).$extra." />";
		if (!empty($instructions)) {
			$sReturn .= '<dfn class="instructions">'.$instructions.'</dfn>';
		}
		$sReturn .= '</div>';
		return $sReturn;
	}
}

/**
 * Form Value
 *
 * Grabs a value from the POST array for the specified field so you can
 * re-populate an input field or textarea.  If Form Validation
 * is active it retrieves the info from the validation class
 *
 * @access	public
 * @param	string
 * @return	mixed
 */
if ( ! function_exists('get_value'))
{
	function get_value($field = '', $default = '')
	{
		if (FALSE === ($OBJ =& _get_validation_object()))
		{
			if ( ! isset($_POST[$field]))
			{
				return $default;
			}

			return $_POST[$field];
		}

		return $OBJ->set_value($field, $default);
	}
}

if (!function_exists('get_index_value')) {
	function get_index_value($field,$index,$default='') {
		$aArray = get_value($field,$default);
		if (!is_array($aArray) || !isset($aArray[$index])) {
			return $default;
		}

		return $aArray[$index];
	}
}

// ------------------------------------------------------------------------

/**
 * Form Error
 *
 * Returns the error for a specific form field.  This is a helper for the
 * form validation class.
 *
 * @access	public
 * @param	string
 * @param	string
 * @param	string
 * @return	string
 */
if ( ! function_exists('my_form_error')) {
	function my_form_error($field = '', $prefix = '<span class="help">', $suffix = '</span>') {
		if (FALSE === ($OBJ =& _get_validation_object())) {
			return '';
		}

		return $OBJ->error($field, $prefix, $suffix);
	}
}

// ------------------------------------------------------------------------

/**
 * Has Form Error
 *
 * Returns the error for a specific form field.  This is a helper for the
 * form validation class.
 *
 * @access	public
 * @param	string
 * @return	boolean
 */
if ( ! function_exists('has_form_error')) {
	function has_form_error($field = '') {
		if (FALSE === ($OBJ =& _get_validation_object())) {
			return false;
		}

		$sError = $OBJ->error($field,'','');
		if (empty($sError)) {
			return false;
		}
		return true;
	}
}

if ( ! function_exists('form_cc_expire')) {
	function form_cc_expire($data = '', $value = '', $extra = '', $alt_name = '',$addl_info='') {
		$settings = array(
			'name'           => (( ! is_array($data)) ? $data : $data['name'])
			,'month_start'    => 1
			,'month_end'      => 12
			,'month_selected' => 1
			,'year_start'     => date('y')
			,'year_end'       => date('y',strtotime(' +8 years'))
			,'year_selected'  => date('y')
		);

		if (!empty($value) && strpos($value,'/')) {
			$aValue = explode('/',$value);
			$settings['month_selected'] = $aValue[0];
			$settings['year_selected'] = substr($aValue[1],-2);
		}

		if (is_array($data)) {
			foreach ($settings as $sKey=>$sValue) {
				if (isset($data[$sKey])) {
					$settings[$sKey] = $data[$sKey];
				}
			}
		}

		$name = $settings['name'];
		$error = form_error($name.'_year');

		$form = "<div class=\"aleft\">\n";
		if (empty($alt_name)) {
			$alt_name = str_replace('_',' ',$name);
			$alt_name = ucwords($alt_name);
		}
		$form .= "\t<label>Expires</label>\n";

		// Add month select
		$form .= "\t<select id=\"{$name}_month\" name=\"{$name}_month\">\n";
		for ($i=$settings['month_start'];$i<=$settings['month_end'];$i++) {
			$value = str_pad($i,2,'0',STR_PAD_LEFT);
			$selected = '';
			if ($i == $settings['month_selected']) {
				$selected = ' selected="selected"';
			}
			$form .= "\t\t<option value=\"{$value}\"".$selected.">{$value}</option>\n";
		}
		$form .= "\t</select>";

		// add year select
		$form .= "<select id=\"{$name}_year\" name=\"{$name}_year\">\n";
		for ($i=$settings['year_start'];$i<=$settings['year_end'];$i++) {
			$selected = '';
			if ($i == $settings['year_selected']) {
				$selected = ' selected="selected"';
			}
			$form .= "\t\t<option value=\"{$i}\"".$selected.">20{$i}</option>\n";
		}
		$form .= "\t</select>\n";
		//if (!empty($error)) {
		//	$form .= "{$error}\n";
		//}

		if (!empty($addl_info)) {
			$form .= '<dfn class="instructions">'.$addl_info."</dfn>\n";
		}

		$form .= "</div>\n";

		return $form;
	}
}

if (!function_exists('country_dropdown')) {
	function country_dropdown($name, $id, $class, $selected_country, $top_countries=array(), $all=false, $selection=NULL, $show_all=TRUE ){
		// You may want to pull this from an array within the helper
		$countries = ConfigService::getItem('country_list');

		$classHtml = '';
		if (!empty($class)) {
			$classHtml = " class=\"{$class}\"";
		}

		$html = "<select id=\"{$id}\" name=\"{$name}\"{$classHtml}>\n";

		$selected = NULL;
		if (in_array($selection,$top_countries)) {
			$top_selection = $selection;
			$all_selection = NULL;
		} else {
			$top_selection = NULL;
			$all_selection = $selection;
		}

		$alreadySelected = false;
		if (!empty($selected_country) && !in_array($selected_country,array('all','select'))) {
			$html .= "\t<optgroup label=\"Selected Country\">\n";
			$html .= "\t\t<option value=\"{$selected_country}\" selected>{$countries[$selected_country]}</option>\n";
			$selected = NULL;
			$html .= "\t</optgroup>\n";
			$alreadySelected = true;

		} elseif ($selected_country=='all') {
			$html .= "<optgroup label=\"Selected Country\">";
			if ($selected_country === $top_selection) {
				$selected = " selected";
				$alreadySelected = true;
			}
			$html .= "<option value=\"all\"{$selected}>All</option>";
			$selected = NULL;
			$html .= "</optgroup>";

		} elseif ($selected_country=='select') {
			$html .= "<optgroup label=\"Selected Country\">";
			if ($selected_country === $top_selection) {
				$selected = " selected";
				$alreadySelected = true;
			}
			$html .= "<option value=\"select\"{$selected}>Select</option>";
			$selected = NULL;
			$html .= "</optgroup>";
		}

		if (!empty($all) && $all == 'all' && $selected_country != 'all'){
			$html .= "<option value=\"all\">All</option>";
			$selected = NULL;
		}

		if (!empty($all) && $all == 'select' && $selected_country != 'select'){
			$html .= "<option value=\"select\">Select</option>";
			$selected = NULL;
		}

		if (!empty($top_countries)) {
			$html .= "\t<optgroup label=\"Top Countries\">\n";
			foreach ($top_countries as $value) {
				if (array_key_exists($value, $countries)) {
					if ($value === $top_selection && !$alreadySelected) {
						$selected = " selected";
					}
					$html .= "\t\t<option value=\"{$value}\"{$selected}>{$countries[$value]}</option>\n";
					$selected = NULL;
				}
			}
			$html .= "\t</optgroup>\n";
		}

		if ($show_all) {
			$html .= "\t<optgroup label=\"All Countries\">\n";
			foreach ($countries as $key => $country) {
				if ($key === $all_selection && !$alreadySelected) {
					$selected = " selected";
				}
				$html .= "\t\t<option value=\"{$key}\"{$selected}>{$country}</option>\n";
				$selected = NULL;
			}
			$html .= "\t</optgroup>\n";
		}

		$html .= "</select>\n";
		return $html;
	}
}


/**
 * Textarea field
 *
 * @access	public
 * @param	mixed
 * @param	string
 * @param	string|array
 * @param	string
 * @return	string
 */
if ( ! function_exists('my_form_upload')) {
	function my_form_upload($data = '', $value = '', $extra = '', $alt_name='')
	{
		if ( ! is_array($data))
		{
			$data = array('name' => $data);
		}

		$name = (is_array($data)) ? $data['name'] : $data;
		if (empty($alt_name)) {
			$alt_name = str_replace('_',' ',$name);
			$alt_name = ucwords($alt_name);
		}

		if (strpos($extra,'id="') === false) {
			$extra .= ' id="'.$name.'"';
		}

		$error = form_error($name);
		if (!empty($error)) {
			if (strpos($extra,'class="') !== false) {
				$extra = str_replace('class="','class="error ',$extra);
			} else {
				$extra .= ' class="error"';
			}
		}

		preg_match('/id="(.*)"/',$extra,$aIdMatch);
		if (!empty($aIdMatch[1])) {
			$sLabelFor = $aIdMatch[1];
		} else {
			$sLabelFor = $name;
		}

		$data['type'] = 'file';

		$sReturn = '<div class="input file">';
		$sReturn .= "<label for=\"{$sLabelFor}\">{$alt_name}</label>\n";
		$sReturn .= form_input($data, $value, $extra);;
		if (!empty($instructions)) {
			$sReturn .= '<dfn class="instructions">'.$instructions."</dfn>\n";
		}
		$sReturn .= '</div>';

		return $sReturn;
	}
}