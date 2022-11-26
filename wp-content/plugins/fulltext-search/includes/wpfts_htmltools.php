<?php

/**  
 * Copyright 2013-2022 Epsiloncool
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 ******************************************************************************
 *  I am thank you for the help by buying PRO version of this plugin 
 *  at https://fulltextsearch.org/ 
 *  It will keep me working further on this useful product.
 ******************************************************************************
 * 
 *  @copyright 2013-2022
 *  @license GPLv3
 *  @package Wordpress Fulltext Search Pro
 *  @author Epsiloncool <info@e-wm.org>
 */

class WPFTS_Htmltools
{
	static function makeNode($name, $attrs = array(), $html = false)
	{
		$s = '<'.$name.' ';
		foreach ($attrs as $k => $d) {
			$s .= ' '.htmlspecialchars($k).'="'.htmlspecialchars($d).'"';
		}
		
		if ($html !== false) {
			$s .= '>'.$html.'</'.$name.'>';
		} else {
			$s .= '/>';
		}
		
		return $s;
	}
	
	static function makeSelect($data, $current = false, $attrs = array())
	{
		$html = '';
		foreach ($data as $k => $v) {
			$html .= '<option value="'.htmlspecialchars($k).'" '.((($current !== false) && ($current == $k)) ? ' selected="selected"' : '').'>'.htmlspecialchars($v).'</option>';
		}
		
		return self::makeNode('select', $attrs, $html);
	}
	
	static function makeMultiSelect($data, $current = array(), $attrs = array())
	{
		$attrs['multiple'] = 'multiple';
		
		$html = '';
		foreach ($data as $k => $v) {
			$html .= '<option value="'.htmlspecialchars($k).'" '.((($current !== false) && (in_array($k, $current))) ? ' selected="selected"' : '').'>'.htmlspecialchars($v).'</option>';
		}
		
		return self::makeNode('select', $attrs, $html);
	}
	
	static function makeRadioGroup($basename, $data, $current = false, $attrs = array())
	{
		$html = '';
		
		if (is_array($data)) {
			$uniq = '';
			foreach ($data as $k => $d) {
				$id = 'rg'.$uniq.$basename.'_'.$k;
				$html .= '<label for="'.htmlspecialchars($id).'"><input type="radio" name="'.htmlspecialchars($basename).'" id="'.htmlspecialchars($id).'" value="'.htmlspecialchars($k).'"'.($current == $k ? ' checked="checked"' : '').'>&nbsp;'.htmlspecialchars($d).'</label>';
			}
		}
		
		return $html;
	}
	
	static function makeText($current = false, $attrs = array())
	{
		$html = '';
		$attrs['type'] = 'text';
		if ($current !== false) {
			$attrs['value'] = $current;
		}
		return self::makeNode('input', $attrs, $html);
	}
	
	static function makeHidden($current = false, $attrs = array())
	{
		$html = '';
		$attrs['type'] = 'hidden';
		if ($current !== false) {
			$attrs['value'] = $current;
		}
		return self::makeNode('input', $attrs, $html);
	}
	
	static function makeTextarea($current = '', $attrs = array())
	{
		$html = htmlspecialchars($current);
		return self::makeNode('textarea', $attrs, $html);
	}
	
	static function makeButton($caption = '', $attrs = array())
	{
		$html = $caption;
		return self::makeNode('button', $attrs, $html);
	}
	
	static function makeCheckbox($current = 0, $attrs = array(), $label = false)
	{
		if ($current) {
			$attrs['checked'] = 'checked';
		} else {
			if (isset($attrs['checked'])) {
				unset($attrs['checked']);
			}
		}
		$attrs['type'] = 'checkbox';
		
		$html = self::makeNode('input', $attrs).$label;
		if ($label !== false) {
			$l_attrs = array();
			if (isset($attrs['id'])) {
				$l_attrs['for'] = $attrs['id'];
			}
			return self::makeNode('label', $l_attrs, $html);
		} else {
			return $html;
		}
	}
	
	static function makeLabelledCheckbox($name, $value, $label, $ischecked = false, $attrs2 = array())
	{
		$id = 'lch'.$name;
		
		$attrs = array(
			'type' => 'checkbox',
			'value' => $value,
			'name' => $name,
			'id' => $id,
		) + $attrs2;
		if ($ischecked) {
			$attrs['checked'] = 'checked';
		}
	
		$html = '<label for="'.$id.'">'.self::makeNode('input', $attrs).'&nbsp;<span>'.$label.'</span></label>';
		
		return $html;
	}
}