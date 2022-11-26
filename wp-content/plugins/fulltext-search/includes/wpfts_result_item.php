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
 *  @package Wordpress Fulltext Search
 *  @author Epsiloncool <info@e-wm.org>
 */

require_once dirname(__FILE__).'/wpfts_tokencollector.php';

class WPFTS_Result_Item
{
	protected $_demodata = array(
		'post_link' => '/the-secret-lives-of-intps/',
		'post_title' => 'The Secret Lives of INTPs',
		'file_link' => '/wp-content/uploads/2018/03/The-Secret-Lives-of-INTPs.pdf',
		'post_excerpt' => 'An <b>INTP</b> <b>child</b> may enjoy constructing pens, building  tunnel systems for rodents, and laying out aquariums and terrariums. An <b>INTP</b> <b>Family</b> What about an <b>INTP</b> <b>family</b> setting? Thus, a listener can conclude that the speaker saw an eagle-like <b>bird</b> flying over what should probably  be considered a smallish hill exactly fourteen days previously.',
		'score' => 0.17,
		'filesize' => 1720000,
		'not_found_words' => array('timeliness', 'iridescence'),
	);
	public $demo_mode = false;
	public $post = array();
	public $is_post = false;

	public function __construct($post_id = false)
	{
		$this->post = array();
		$this->is_post = false;

		if ($post_id === false) {
			$this->demo_mode = true;
		} else {
			$this->demo_mode = false;
			$this->post = get_post($post_id, ARRAY_A);
			if ($this->post) {
				$this->is_post = true;
			}
		}
	}

	public function TitleLink($link = false)
	{
		global $wpfts_core;

		$is_attachment = 1;
		if ($this->demo_mode) {
			if (intval($wpfts_core->get_option('is_title_direct_link')) != 0) {
				$link = $this->_demodata['file_link'];
			} else {
				$link = $this->_demodata['post_link'];
			}
		} else {
			// Real case
			$is_attachment = ($this->post['post_type'] == 'attachment') ? 1 : 0;
			if ($is_attachment) {
				if (intval($wpfts_core->get_option('is_title_direct_link')) != 0) {
					$link = wp_get_attachment_url($this->post['ID']);
				} else {
					// Use original link
				}
			} else {
				// Use original link
			}
		}

		$r1 = array(
			'is_demo' => $this->demo_mode,
			'is_attachment' => $is_attachment,
			'is_title_direct_link' => (intval($wpfts_core->get_option('is_title_direct_link') != 0)) ? 1 : 0,
			'link' => $link,
		);
		$r1 = apply_filters('wpfts_se_titlelink', $r1, $this->post);

		return isset($r1['link']) ? $r1['link'] : '';
	}

	public function TitleText($title = false)
	{
		global $wpfts_core;

		$is_attachment = ((isset($this->post['post_type'])) && ($this->post['post_type'] == 'attachment'));
		if ($this->demo_mode) {
			$is_attachment = true;
			$filepath = $this->_demodata['file_link'];
			$post_title = $this->_demodata['post_title'];
		} else {
			// Real case
			if ($title !== false) {
				$post_title = $title;
			} else {
				if (($this->is_post) && (isset($this->post['post_title']))) {
					$post_title = $this->post['post_title'];
				} else {
					$post_title = '';
				}
			}

			$filepath = wp_get_attachment_url($this->post['ID']);
		}
		$ext = (($p = mb_strrpos($filepath, '.')) !== false) ? mb_substr($filepath, $p + 1) : '';
		$ext = mb_strtoupper($ext);
		if ((intval($wpfts_core->get_option('is_file_ext')) != 0) && (strlen($ext) > 0) && ($is_attachment)) {
			$ret_title = '<sup>['.$ext.']</sup> '.$post_title;
		} else {
			$ret_title = $post_title;
		}

		$r1 = array(
			'is_demo' => $this->demo_mode,
			'is_attachment' => $is_attachment,
			'is_file_ext' => intval($wpfts_core->get_option('is_file_ext')) != 0 ? 1 : 0,
			'title' => $ret_title,
		);

		$r1 = apply_filters('wpfts_se_titletext', $r1, $this->post);

		return isset($r1['title']) ? $r1['title'] : '';
	}

	public function GetExcData($query = '')
	{
		//ini_set('pcre.jit', 0);

		global $wpfts_core;

		$chunks = $wpfts_core->getPostChunks($this->post['ID']);

		$fulltext = '';
		if ($chunks) {
			foreach ($chunks as $k => $d) {
				if ($k != 'post_title') {
					$fulltext .= $d.' ';
				}
			}
		}

		$fulltext = apply_filters('wpfts_get_fulltext', $fulltext, $this->post['ID']);

		$fulltext = html_entity_decode(str_replace('&nbsp;', ' ', $fulltext));
		// Prepare fulltext for Smart Excerpt-ing
		$fulltext = str_replace('<', ' <', $fulltext);
		$fulltext = preg_replace('~\s+~', ' ', $fulltext);
		$fulltext = htmlspecialchars($fulltext);
		$fulltext = mb_convert_encoding($fulltext, 'UTF-8', 'UTF-8');	// Guarantee a valid UTF-8 string!
//echo $fulltext;



		// Find key word positions
		$ws = $wpfts_core->split_to_words($query);
				
		$fulltext = preg_replace('~([\.\!\?]\s|\W{4,}?)~u', '$1'."\n", $fulltext);

		$sents = array();
		$ii = 0;
		foreach ($ws as $w) {
			// Lets try to catch some hares in the same time
			//$i = preg_match_all('~([\.\!\?]\s|^)([^\.\!\?]*((?<=\W)'.preg_quote($w).'[^\.\?\!]*)([\.\!\?]\s|[\.\!\?]$|$))~Uius', $fulltext, $zz, PREG_OFFSET_CAPTURE);
			//preg_match_all('~(?<=[\.\!\?]\s|^)(([^\.\!\?]|[\.\!\?](?!\s))*'.preg_quote($w).'([^\.\!\?]|[\.\!\?](?!\s))*)([\.\!\?]\s|[\.\!\?]$|$)~Uius', $fulltext, $zz, PREG_OFFSET_CAPTURE);

			preg_match_all('~(.*('.$w.').*)~iu', $fulltext, $zz, PREG_OFFSET_CAPTURE);

			$ssa = array();
			if (isset($zz[0])) {
				// A list of sentences and offsets
				foreach ($zz[0] as $kk => $dd) {
					$ssa[$dd[1]] = array($dd[0], $zz[2][$kk][1] - $dd[1]);	// Sentence plus word offset
				}
			}
			$sents['t'.$ii] = $ssa;
			$ii ++;
		}

		$tc = new WPFTS_TokenCollector();
		$tc->tokenlist = $sents;

		$nominal_length = intval($wpfts_core->get_option('optimal_length'));
		if ($nominal_length < 10) {
			$nominal_length = 300;
		}

		$minlength = $nominal_length * 0.9;
		$maxlength = $nominal_length * 1.1;

		$goal = '';
		for ($i = 0; $i < count($sents); $i ++) {
			$goal .= ''.$i;
		}

		$fullgoal = $goal;

		$bestsentenses = array();
		$total_length = 0;
		$outt = array();
		$is_goal_done = false;

		$goal = $fullgoal;

		$g_wdt = 100;
		while (true) {

			$wdt = strlen($goal);

			$is_found_any = false;
			$is_can_not_more = false;
			$is_filled = false;

//echo 'Full Goal: '.$fullgoal."\n";

			while (strlen($goal) > 0) {
//echo 'Current Goal: '.$goal."\n";
				$mp = $tc->GetMostPowerful($goal, $fullgoal, $outt);

				if ($mp && isset($mp[1]) && (count($mp[1]) > 0) && isset($mp[0]) && (strlen($mp[0]) > 0)) {

					// Calculate min_length and max_length for this piece
					$nom_len = $nominal_length * strlen($mp[0]) / strlen($fullgoal);
					$min_len = $nom_len * 0.9;
					$max_len = $nom_len * 1.1;
//echo 'min_len='.$min_len."\n";
//echo 'max_len='.$max_len."\n";
					$local_used_len = 0;

					if (strlen($mp[0]) > 1) {
						$t_ordered = $tc->GetOrderedByLessDistance($mp);
					} else {
						$t_ordered = $mp;
					}
//print_r($t_ordered);

					// Looping by found sentences, trying to find first sentence, which one is allowed by length (shorter than max)
					$n_sentences_used = 0;
					foreach ($t_ordered[1] as $tt_k => $tt_v) {
						$ss = trim($tt_v[0]);
						$ss_len = mb_strlen($ss, 'utf-8');

						if ($ss_len <= ($max_len - $local_used_len)) {
							// This one is good!
//echo 'Okay, using the text key='.$tt_k.', value="'.$tt_v[0].'", length='.$ss_len."\n";
							$outt[$tt_k] = $ss;
							$total_length += $ss_len;
							$local_used_len += $ss_len;
							$n_sentences_used ++;

							// Check if it's enough for this goal
							if ($local_used_len >= $min_len) {
								break;
							}
						}
					}

					if ($n_sentences_used < 1) {
						// We have a problem, let's use different algorithm to generate excerpt for this goal
//echo 'SPECIAL ALGORITHM!'."\n";
						// Step1: check how much space we have and compare with delta from first term to last term
						$remaining_len = $max_len - $local_used_len;

						$ii = 0;
						foreach ($t_ordered[1] as $tt_k => $tt_v) {
							if ($ii > 0) {
								// We need only first element
								break;
							}
							$ii ++;

							// Let's calculate remaining length in bytes (should be more in bytes than in chars, it depends from the string we analyzing)
							$remaining_len_bytes =  floor($remaining_len * strlen($tt_v[0]) / mb_strlen($tt_v[0], 'utf-8'));

							if (isset($tt_v[2]) && (count($tt_v[2]) > 0)) {
								// More than 1 term here
								$first_term_pos = min($tt_v[2]);
								$last_term_pos = max($tt_v[2]);
								$sentc_len = $last_term_pos - $first_term_pos;

							} else {
								// Only one word, let's find margin
//echo 'ONLY ONE WORD'."\n";
								$first_term_pos = $last_term_pos = $tt_v[1];
								$sentc_len = 0;
							
							}

							// Check margins
							$margin = ($remaining_len_bytes - $sentc_len) / 2;
//echo 'Margin: '.$margin."\n";
//echo 'Remaining length (bytes) = '.$remaining_len_bytes."\n";
//echo 'Remaining length (chars) = '.$remaining_len."\n";
//echo 'sentence = '.strlen($tt_v[0])."\n";
							if ($margin > 0) {

								$margin1 = $first_term_pos - $margin;
								$margin2 = $last_term_pos + $margin;

								$is_begin_3p = true;
								$is_end_3p = true;

								if ($margin1 < 0) {
									$margin1 -= $margin1;
									$margin2 -= $margin1;
									$is_begin_3p = false;
								} else {
									if ($margin2 > strlen($tt_v[0])) {
										$margin2 -= ($margin2 - strlen($tt_v[0]));
										$margin1 -= ($margin2 - strlen($tt_v[0]));
										$is_end_3p = false;
									}
								}

//echo 'Margin1 = '.$margin1."\n";
//echo 'Margin2 = '.$margin2."\n";

								// Good, let's find next space/letter after first margin and next space/letter after last margin
								$matches2 = array();
								preg_match_all('~\W+~u', $tt_v[0], $matches2, PREG_OFFSET_CAPTURE);

								if (isset($matches2[0]) && is_array($matches2[0])) {
									$t_start = $margin1;
									$t_end = $margin2;
									foreach ($matches2[0] as $t2) {
										$zz = $t2[1] + strlen($t2[0]);
										if ($zz < $margin1) {
											$t_start = $zz;
										}
										if ($t2[1] > $margin2) {
											$t_end = $t2[1];
											break;
										}
									}
	
//echo 't_start = '.$t_start."\n";
//echo 't_end = '.$t_end."\n";

									$tt_text = ($is_begin_3p ? '...' : '').substr($tt_v[0], $t_start, $t_end - $t_start).($is_end_3p ? '...' : '');
//echo 'tt_text = '.$tt_text."\n";

								} else {
									// No spaces?? Let's cut the string as is
									$tt_text = ($is_begin_3p ? '...' : '').substr($tt_v[0], $margin1, $margin2 - $margin1).($is_end_3p ? '...' : '');
								}

							} else {
								// We need to break the line to some parts
//echo 'NEED TO BREAK'."\n";
								$ts = $tt_v[2];
								usort($ts, function($v1, $v2)
								{
									return $v1 < $v2 ? - 1 : 1;
								});

								$margin = floor($remaining_len_bytes / count($ts) / 2);

								$ts_data = array();
								foreach ($ts as $z2) {
									$t_start = $z2 - $margin;
									$t_end = $z2 + $margin;
									$is_start_3p = true;
									$is_end_3p = true;
									if ($t_start < 0) {
										$t_start -= $t_start;
										$t_end -= $t_start;
										$is_start_3p = false;
									} else {
										if ($t_end > strlen($tt_v[0])) {
											$t_end -= ($t_end - strlen($tt_v[0]));
											$t_start -= ($t_end - strlen($tt_v[0]));
											$is_end_3p = false;
										}
									}
									$ts_data[] = array(
										'start' => $t_start,
										'margin1' => $t_start,
										'end' => $t_end,
										'margin2' => $t_end,
										'is_start_3p' => $is_start_3p,
										'is_end_3p' => $is_end_3p,
										'base' => $z2,
										't_c' => 0,	// Counter used to calculate end_offset
									);
								}

								// Find empty spaces
								$matches2 = array();
								preg_match_all('~\W+~u', $tt_v[0], $matches2, PREG_OFFSET_CAPTURE);

								if (isset($matches2[0]) && is_array($matches2[0])) {
									foreach ($matches2[0] as $t2) {
										$zz = $t2[1] + strlen($t2[0]);

										foreach ($ts_data as $ts_k => $ts_item) {
											if ($zz < $ts_item['margin1']) {
												$ts_data[$ts_k]['start'] = $zz;
											}
											if ($t2[1] > $ts_item['margin2']) {
												if ($ts_item['t_c'] < 2) {
													$ts_data[$ts_k]['end'] = $t2[1];
													$ts_data[$ts_k]['t_c'] ++;
												}
											}
										}
									}
//echo 'Before overlap check'."\n";
//print_r($ts_data);
									// Let's detect if we have overlaps
									$has_overlaps = true;
									while ($has_overlaps) {
										$has_overlaps = false;

										$k_pre = false;
										foreach ($ts_data as $ts_k => $ts_item) {
											
											if ($k_pre === false) {
												$k_pre = $ts_k;
												continue;
											}
											if ($ts_data[$k_pre]['end'] >= $ts_item['start']) {
												// Overlap!
//echo 'Overlap detected: '.$k_pre.' - '.$ts_k."\n";
												$has_overlaps = true;
												// Merge and repeat
												$ts_data[$ts_k]['start'] = $ts_data[$k_pre]['start'];
												$ts_data[$ts_k]['margin1'] = $ts_data[$k_pre]['margin1'];
												$ts_data[$ts_k]['is_start_3p'] = $ts_data[$k_pre]['is_start_3p'];
												
												unset($ts_data[$k_pre]);

												break;
											}
											$k_pre = $ts_k;
										}
									}
								}
								// Assume we merged overlaps, now let's construct the final string

								$pieces = array();
								$k_pre = false;
								foreach ($ts_data as $ts_k => $ts_item) {
									if ($k_pre !== false) {
										// Not first element
										$pieces[] = substr($tt_v[0], $ts_item['start'], $ts_item['end'] - $ts_item['start']).($ts_item['is_end_3p'] ? ' ... ' : '');
									} else {
										// First element
										$pieces[] = ($ts_item['is_start_3p'] ? ' ... ' : '').substr($tt_v[0], $ts_item['start'], $ts_item['end'] - $ts_item['start']).($ts_item['is_end_3p'] ? ' ... ' : '');
									}
									$k_pre = $ts_k;
								}

								$tt_text = implode('', $pieces);
//echo 'tt_text = '.$tt_text."\n";

							}

							$outt[$tt_k] = $tt_text;
							$ss_len = mb_strlen($tt_text, 'utf-8');
							$total_length += $ss_len;
							$local_used_len += $ss_len;
							break;
						}

						// If enough space, let's cut text from the start and from the end to leave as much context as possible

						// If not enough space, then we need to cut out words separately


					}

					//$bestsentenses[] = $t_ordered;
					
					// Remove found terms from the goal
					$b = '';
					for ($i = 0; $i < strlen($goal); $i ++) {
						if (strpos($mp[0], $goal[$i]) === false) {
							$b .= $goal[$i];
						}
					}

					// New goal
					$goal = $b;
//echo 'New goal='.$goal."\n";
				} else {
					// We can not extract more, tried all combinations, but can't
					$is_can_not_more = true;
					break;
				}
				$wdt --;
				if ($wdt < 0) {
					// Watchdog triggered
					break;
				}
			}
			if (($total_length > $minlength) || ($is_can_not_more) || $is_filled) {
				break;
			}
			if (strlen($goal) < 1) {
				$is_goal_done = true;
				break;
			}
			
			$g_wdt--;
			if ($g_wdt < 1) {
				break;
			}
		}

		// Sort $outt by keys
		uksort($outt, function($v1, $v2) {
			return $v1 > $v2;
		});

		$outtext = implode(" ", $outt);

		// Lets put highlighting
		foreach ($ws as $w) {
			$outtext = preg_replace("/\p{L}*?".preg_quote($w)."\p{L}*/ui", "<b>$0</b>", $outtext);
		}

		$goal_words = array();
		if (!$is_goal_done) {
			for ($i = 0; $i < strlen($goal); $i ++) {
				if (isset($ws[$goal[$i]])) {
					$goal_words[] = $ws[$goal[$i]];
				}
			}
		}

		return array(
			'text' => $outtext,
			'no_words' => $goal_words,
		);
	}

	public function Excerpt($query = '')
	{
		global $wpfts_core;
		
		if ($this->demo_mode) {
			// Demo data
			$is_attachment = true;
			$excerpt_text = $this->_demodata['post_excerpt'];
			$score = $this->_demodata['score'];
			$filesize = $this->_demodata['filesize'];
			$file_link = $this->_demodata['file_link'];
			$nf_words = $this->_demodata['not_found_words'];
		} else {
			$is_attachment = ((isset($this->post['post_type'])) && ($this->post['post_type'] == 'attachment'));

			$score = isset($this->post['relev']) ? $this->post['relev'] : 0;
			if ($is_attachment) {
				$file_link = wp_get_attachment_url($this->post['ID']);
				$file_path = get_attached_file($this->post['ID']);
				if (is_file($file_path)) {
					$filesize = intval(@filesize($file_path));
				} else {
					$filesize = 0;
				}
			} else {
				$filesize = 0;
				$file_link = '#';
			}

			$excdata = $this->GetExcData($query);
			$excerpt_text = $excdata['text'];
			$nf_words = $excdata['no_words'];
		}
		
		$r1 = array();
		// Is Excerpt content
		$r1['is_excerpt_text'] = (intval($wpfts_core->get_option('is_smart_excerpt_text')) != 0) ? 1 : 0;
		// Excerpt content
		$r1['excerpt_text'] = (intval($wpfts_core->get_option('is_smart_excerpt_text')) != 0) ? $excerpt_text : false;
		// Not found words
		$r1['is_not_found_words'] = (intval($wpfts_core->get_option('is_not_found_words')) != 0) ? 1 : 0;
		$r1['not_found_words'] = $nf_words;
		// Score
		$r1['is_score'] = ((intval($wpfts_core->get_option('is_show_score')) != 0) && ($score > 0.001)) ? 1 : 0;
		$r1['score'] = $score;
		// Is Attachment
		$r1['is_attachment'] = $is_attachment;
		// Is Filesize
		$r1['is_filesize'] = intval($wpfts_core->get_option('is_filesize'));
		// Is Direct Link
		$r1['is_direct_link'] = intval($wpfts_core->get_option('is_direct_link'));
		// Filesize
		$r1['filesize'] = ($is_attachment) ? $filesize : 0;
		// Direct Link
		$r1['link'] = ($is_attachment) ? $file_link : '';
		$r1['is_demo'] = $this->demo_mode;

		$r1 = apply_filters('wpfts_se_data', $r1, $this->post);

		// Create an excerpt HTML
		$a = array();
		$a['excerpt_text'] = ($r1['is_excerpt_text']) ? '<div class="wpfts-smart-excerpt">'.$r1['excerpt_text'].'</div>' : '';
		$a['not_found_words'] = '';
		if (($r1['is_not_found_words']) && is_array($r1['not_found_words']) && (count($r1['not_found_words']) > 0)) {
			$nfs = array();
			foreach ($r1['not_found_words'] as $dd) {
				$nfs[] = '<s>'.$dd.'</s>';
			}
			$a['not_found_words'] = '<div class="wpfts-not-found"><span>'.__('Not found', 'fulltext-search').': '.implode(' ', $nfs).'</span></div>';
		}
		$a['score'] = '';
		if ($r1['is_score']) {
			$a['score'] = '<span class="wpfts-score">'.__('Score', 'fulltext-search').': '.number_format_i18n($r1['score'], 2).'</span>';
		}
		$a['link'] = '';
		if ($r1['is_attachment']) {
			$shift = (strlen($a['score']) > 0) ? ' wpfts-shift' : '';

			if ($r1['is_direct_link']) {
				$a['link'] = '<a class="wpfts-download-link'.$shift.'" href="'.esc_url($r1['link']).'"><span>'.__('Download', 'fulltext-search').( $r1['is_filesize'] ? ' ('.size_format(floatval($r1['filesize']), 2).')' : '').'</span></a>';
			} else {
				if ($r1['is_filesize']) {
					$a['link'] = '<span class="wpfts-file-size'.$shift.'">'.__('File Size', 'fulltext-search').': '.size_format(floatval($r1['filesize']), 2).'</span>';
				}
			}

		}
		$a['is_demo'] = $r1['is_demo'];

		$a = apply_filters('wpfts_se_output', $a, $this->post);

		$s = '';
		$s .= $a['excerpt_text'];
		$s .= $a['not_found_words'];
		if ((strlen($a['score']) > 0) || (strlen($a['link']) > 0)) {
			$s .= '<div class="wpfts-bottom-row">'.implode(' ', array($a['score'], $a['link'])).'</div>';
		}

		return $s;
	}


}