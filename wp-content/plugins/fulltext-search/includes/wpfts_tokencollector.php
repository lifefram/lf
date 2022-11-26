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

class WPFTS_TokenCollector
{
	public $tokenlist = array();

	public function GetMostPowerful($goal, $fullterms, &$exclude)
	{
		//echo 'Getting most powerful for "'.$goal.'" excluding '.print_r(array_keys($exclude), true)."\n";

		$a = $this->GetFull($fullterms, $exclude);

		if ($a && isset($a[1]) && (count($a[1]) > 0)) {
			//echo 'Success for full combination'."\n";
			return $a;
		}

		//echo 'Checking for partial solutions'."\n";

		$plan = $this->GetCombinationPlan($goal, $fullterms);

		//echo 'Here is a plan: '.print_r($plan, true)."\n";

		foreach ($plan as $n_key) {
			$a2 = $this->GetFull($n_key, $exclude);
			if ($a2 && isset($a2[1]) && (count($a2[1]) > 0)) {
				// Found the good solution
				//echo 'Found for key='.$n_key."\n";
				return $a2;
			}
		}

		return false;
	}

	public function GetFull($key, &$exclude)
	{
		if (strlen($key) < 1) {
			return false;
		}

		if (strlen($key) < 2) {
			// Exactly 1
			if (isset($this->tokenlist['t'.$key])) {
				$sum = array_diff_key($this->tokenlist['t'.$key], $exclude);
				return array($key, $sum);
			} else {
				return false;
			}
		}

		// Make combination
		$sum = array();
		$tk = '';
		for ($i = 0; $i < strlen($key); $i ++) {
			$tk .= ''.$key[$i];
			$newpart = array();
			if (isset($this->tokenlist['t'.$key[$i]])) {
				$newpart = $this->tokenlist['t'.$key[$i]];
			}
			if ($i == 0) {
				$sum = $newpart;
			} else {
				if (isset($this->tokenlist['t'.$tk])) {
					$sum = $this->tokenlist['t'.$tk];
				} else {
					// Construct combination
					$sum = array_intersect_key($sum, $newpart);
					$this->tokenlist['t'.$tk] = $sum;
				}
				
			}
			// Remove $exclude keys
			$sum = array_diff_key($sum, $exclude);
			if ((!$sum) || (count($sum) < 1)) {
				break;
			}
		}
		return array($key, $sum);
	}

	public function GetOrderedByLessDistance($mp)
	{
		$keylist = isset($mp[0]) ? $mp[0] : '';
		if (strlen($keylist) > 1) {
			$keyarrays = array();
			for ($i = 0; $i < strlen($keylist); $i ++) {
				$key = 't'.$keylist[$i];
				if (isset($this->tokenlist[$key])) {
					$keyarrays[] = $this->tokenlist[$key];
				}
			}
			foreach ($mp[1] as $k => $d) {
				$ofss = array();
				for ($i = 0; $i < count($keyarrays); $i ++) {
					if (isset($keyarrays[$i][$k][1])) {
						$ofss[] = $keyarrays[$i][$k][1];
					}
				}
				$mp[1][$k][1] = max($ofss) - min($ofss);
				$mp[1][$k][2] = $ofss;
			}
			uasort($mp[1], function($v1, $v2){
				return $v1[1] > $v2[1];
			});
			return $mp;
		} else {
			// No ordering
			return $mp;
		}
	}

	public function GetCombinationPlan($key, $fullterms)
	{
		$combs = array();
		for ($i = 1; $i < pow(2, strlen($fullterms)) - 1; $i ++) {	// We are excluding 0 and full combination
			$tk = '';
			$pw = 0;
			$pw_secondary = 0;
			$ii = $i;
			$bitn = 0;
			while ($ii > 0) {
				if (($ii & 0x1) > 0) {
					$lt = $fullterms[$bitn];
					$tk .= $lt;
					if (strpos($key, $lt) !== false) {
						$pw ++;
					} else {
						$pw_secondary ++;
					}
				}
				$ii = $ii >> 1;
				$bitn ++;
			}
			if ($pw > 0) {
				$pwkey = $pw * 1000 + $pw_secondary;
				if (isset($combs[$pwkey])) {
					$combs[$pwkey][] = $tk;
				} else {
					$combs[$pwkey] = array($tk);
				}
			}
		}
		// Sort combs by key
		uksort($combs, function($v1, $v2){
			return ($v1 < $v2);
		});
		$res = array();
		foreach ($combs as $cb) {
			$res = array_merge($res, $cb);
		}
		return $res;
	}
}
