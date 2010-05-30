<?php

##############################################################################
# *                                                                          #
# * 2MOONS                                                                   #
# *                                                                          #
# * @copyright Copyright (C) 2010 By ShadoX from titanspace.de               #
# *                                                                          #
# *	                                                                         #
# *  This program is free software: you can redistribute it and/or modify    #
# *  it under the terms of the GNU General Public License as published by    #
# *  the Free Software Foundation, either version 3 of the License, or       #
# *  (at your option) any later version.                                     #
# *	                                                                         #
# *  This program is distributed in the hope that it will be useful,         #
# *  but WITHOUT ANY WARRANTY; without even the implied warranty of          #
# *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the           #
# *  GNU General Public License for more details.                            #
# *                                                                          #
##############################################################################

include_once("class.Smarty.".PHP_EXT);

class template extends Smarty
{
	function __construct()
	{	
		parent::__construct();
		$this->allow_php_templates	= true;
		$this->force_compile 		= false;
		$this->caching 				= false;
		$this->compile_check		= true;
		$this->template_dir 		= ROOT_PATH . TEMPLATE_DIR."smarty/";
		$this->compile_dir 			= ROOT_PATH ."cache/";
		$this->script				= array();
		$this->page					= array();
	}
	
	public function getplanets()
	{
		global $USER;
		$this->UserPlanets			= SortUserPlanets($USER);
	}
	
	public function loadscript($script)
	{
		$this->script[]				= $script;
	}
		
	public function assign_vars($assign)
	{
		foreach($assign as $AssignName => $AssignContent) {
			$this->assign($AssignName, $AssignContent);
		}
	}
	
	private function planetmenu()
	{
		global $LNG;
		if(empty($this->UserPlanets))
			$this->getplanets();
		
		foreach($this->UserPlanets as $PlanetQuery)
		{
			if(!empty($PlanetQuery['b_building_id']))
			{
				$QueueArray	= explode ( ";", $PlanetQuery['b_building_id']);
				$BuildArray	= explode (",", $QueueArray[0]);
			}
			
			$Planetlist[$PlanetQuery['id']]	= array(
				'url'		=> $this->phpself."&amp;cp=".$PlanetQuery['id']."&amp;re=0",
				'name'		=> $PlanetQuery['name'].(($PlanetQuery['planet_type'] == 3) ? " (".$LNG['fcm_moon'].")":""),
				'image'		=> $PlanetQuery['image'],
				'galaxy'	=> $PlanetQuery['galaxy'],
				'system'	=> $PlanetQuery['system'],
				'planet'	=> $PlanetQuery['planet'],
				'ptype'		=> $PlanetQuery['planet_type'],
				'Buildtime'	=> (!empty($PlanetQuery['b_building_id']) && $BuildArray[3] - TIMESTAMP > 0) ? pretty_time($BuildArray[3] - TIMESTAMP) : false,
			);
		}
		
		$this->assign_vars(array(	
			'PlanetMenu' 		=> $Planetlist,
			'show_planetmenu' 	=> $LNG['show_planetmenu'],
			'current_pid'		=> $USER['current_planet'],
		));
	}
	
	private function leftmenu()
	{
		global $CONF, $LNG, $USER;
		$this->assign_vars(array(	
			'lm_overview'		=> $LNG['lm_overview'],
			'lm_empire'			=> $LNG['lm_empire'],
			'lm_buildings'		=> $LNG['lm_buildings'],
			'lm_resources'		=> $LNG['lm_resources'],
			'lm_trader'			=> $LNG['lm_trader'],
			'lm_research'		=> $LNG['lm_research'],
			'lm_shipshard'		=> $LNG['lm_shipshard'],
			'lm_fleet'			=> $LNG['lm_fleet'],
			'lm_technology'		=> $LNG['lm_technology'],
			'lm_galaxy'			=> $LNG['lm_galaxy'],
			'lm_defenses'		=> $LNG['lm_defenses'],
			'lm_alliance'		=> $LNG['lm_alliance'],
			'lm_forums'			=> $LNG['lm_forums'],
			'lm_officiers'		=> $LNG['lm_officiers'],
			'lm_statistics' 	=> $LNG['lm_statistics'],
			'lm_records'		=> $LNG['lm_records'],
			'lm_topkb'			=> $LNG['lm_topkb'],
			'lm_search'			=> $LNG['lm_search'],
			'lm_battlesim'		=> $LNG['lm_battlesim'],
			'lm_messages'		=> $LNG['lm_messages'],
			'lm_notes'			=> $LNG['lm_notes'],
			'lm_buddylist'		=> $LNG['lm_buddylist'],
			'lm_chat'			=> $LNG['lm_chat'],
			'lm_support'		=> $LNG['lm_support'],
			'lm_faq'			=> $LNG['lm_faq'],
			'lm_options'		=> $LNG['lm_options'],
			'lm_banned'			=> $LNG['lm_banned'],
			'lm_rules'			=> $LNG['lm_rules'],
			'lm_logout'			=> $LNG['lm_logout'],
			'authlevel' 		=> $USER['authlevel'],
			'new_message' 		=> $USER['new_message'],
			'forum_url'			=> $CONF['forum_url'],
			'lm_administration'	=> $LNG['lm_administration'],
		));
	}
	
	private function topnav()
	{
		global $PLANET, $LNG, $USER;
		$this->phpself			= "?page=".request_var('page', '')."&amp;mode=".request_var('mode', '');
		$this->loadscript("topnav.js");
		if(empty($this->UserPlanets))
			$this->getplanets();
		
		foreach($this->UserPlanets as $CurPlanetID => $CurPlanet)
		{
			$SelectorVaules[]	= $this->phpself."&amp;cp=".$CurPlanet['id']."&amp;re=0";
			$SelectorNames[]	= $CurPlanet['name'].(($CurPlanet['planet_type'] == 3) ? " (" . $LNG['fcm_moon'] . ")":"")."&nbsp;[".$CurPlanet['galaxy'].":".$CurPlanet['system'].":".$CurPlanet['planet']."]&nbsp;&nbsp;";
		}
		
		$this->assign_vars(array(
			'energy'			=> (($PLANET["energy_max"] + $PLANET["energy_used"]) < 0) ? colorRed(pretty_number($PLANET["energy_max"] + $PLANET["energy_used"]) . "/" . pretty_number($PLANET["energy_max"])) : pretty_number($PLANET["energy_max"] + $PLANET["energy_used"]) . "/" . pretty_number($PLANET["energy_max"]),
			'metal'				=> ($PLANET["metal"] >= $PLANET["metal_max"]) ? colorRed(pretty_number($PLANET["metal"])) : pretty_number($PLANET["metal"]),
			'crystal'			=> ($PLANET["crystal"] >= $PLANET["crystal_max"]) ? colorRed(pretty_number($PLANET["crystal"])) : pretty_number($PLANET["crystal"]),
			'deuterium'			=> ($PLANET["deuterium"] >= $PLANET["deuterium_max"]) ? colorRed(pretty_number($PLANET["deuterium"])) : pretty_number($PLANET["deuterium"]),
			'darkmatter'		=> pretty_number($USER["darkmatter"]),
			'metal_max'			=> shortly_number($PLANET["metal_max"]),
			'crystal_max'		=> shortly_number($PLANET["crystal_max"]),
			'deuterium_max' 	=> shortly_number($PLANET["deuterium_max"]),
			'alt_metal_max'		=> pretty_number($PLANET["metal_max"]),
			'alt_crystal_max'	=> pretty_number($PLANET["crystal_max"]),
			'alt_deuterium_max' => pretty_number($PLANET["deuterium_max"]),
			'js_metal_max'		=> floattostring($PLANET["metal_max"]),
			'js_crystal_max'	=> floattostring($PLANET["crystal_max"]),
			'js_deuterium_max' 	=> floattostring($PLANET["deuterium_max"]),
			'js_metal_hr'		=> floattostring($PLANET['metal_perhour'] + $CONF['metal_basic_income'] * $CONF['resource_multiplier']),
			'js_crystal_hr'		=> floattostring($PLANET['crystal_perhour'] + $CONF['crystal_basic_income'] * $CONF['resource_multiplier']),
			'js_deuterium_hr'	=> floattostring($PLANET['deuterium_perhour'] + $CONF['deuterium_basic_income'] * $CONF['resource_multiplier']),
			'current_panet'		=> $this->phpself."&amp;cp=".$USER['current_planet']."&amp;re=0",
			'tn_vacation_mode'	=> $LNG['tn_vacation_mode'],
			'vacation'			=> $USER['urlaubs_modus'] ? date('d.m.Y H:i:s',$USER['urlaubs_until']) : false,
			'delete'			=> $USER['db_deaktjava'] ? sprintf($LNG['tn_delete_mode'], date('d. M Y\, h:i:s',$USER['db_deaktjava'] + (60 * 60 * 24 * 7))) : false,
			'image'				=> $PLANET['image'],
			'settings_tnstor'	=> $USER['settings_tnstor'],
			'SelectorVaules'	=> $SelectorVaules,
			'SelectorNames'		=> $SelectorNames,
			'Metal'				=> $LNG['Metal'],
			'Crystal'			=> $LNG['Crystal'],
			'Deuterium'			=> $LNG['Deuterium'],
			'Darkmatter'		=> $LNG['Darkmatter'],
			'Energy'			=> $LNG['Energy'],
		));
	}
	
	private function header()
	{
		global $USER, $CONF, $LNG;
		$this->assign_vars(array(
			'title'			=> $CONF['game_name'],
			'dpath'			=> $USER['dpath'],
			'is_pmenu'		=> $USER['settings_planetmenu'],
			'thousands_sep'	=> (!empty($LNG['locale']['thousands_sep']) ? $LNG['locale']['thousands_sep'] : "."),
		));
	}
	
	private function footer()
	{
		global $CONF;
		$this->assign_vars(array(
			'cron'		=> ((TIMESTAMP >= ($CONF['stat_last_update'] + (60 * $CONF['stat_update_time']))) ? "<img src=\"".ROOT_PATH."cronjobs.php?cron=stats\" alt=\"\" height=\"1\" width=\"1\">" : "").((TIMESTAMP >= ($CONF['stat_last_db_update'] + (60 * 60 * 24))) ? "<img src=\"".ROOT_PATH."cronjobs.php?cron=opdb\" alt=\"\" height=\"1\" width=\"1\">" : ""),
			'scripts'	=> $this->script,
			'ga_active'	=> $CONF['ga_active'],
			'ga_key'	=> $CONF['ga_key'],
		));
	}
	
	public function set_index()
	{
		global $USER, $CONF, $LNG;
		$this->assign_vars(array(
			'cappublic'			=> $CONF['cappublic'],
			'servername' 		=> $CONF['game_name'],
			'forum_url' 		=> $CONF['forum_url'],
			'fb_active'			=> $CONF['fb_on'],
			'fb_key' 			=> $CONF['fb_apikey'],
			'forum' 			=> $LNG['forum'],
			'register_closed'	=> $LNG['register_closed'],
			'fb_perm'			=> sprintf($LNG['fb_perm'], $CONF['game_name']),
			'menu_index'		=> $LNG['menu_index'],
			'menu_news'			=> $LNG['menu_news'],
			'menu_rules'		=> $LNG['menu_rules'],
			'menu_agb'			=> $LNG['menu_agb'],
			'menu_pranger'		=> $LNG['menu_pranger'],
			'menu_top100'		=> $LNG['menu_top100'],
			'menu_disclamer'	=> $LNG['menu_disclamer'],
			'game_captcha'		=> $CONF['capaktiv'],
			'reg_close'			=> $CONF['reg_closed'],
			'ga_active'			=> $CONF['ga_active'],
			'ga_key'			=> $CONF['ga_key'],
			'getajax'			=> request_var('getajax', 0),
			'lang'				=> DEFAULT_LANG,
		));
	}
		
	public function page_header()
	{
		$this->page['header']		= true;
	}
	
	public function page_topnav()
	{
		$this->page['topnav']		= true;
	}
	
	public function page_leftmenu()
	{
		$this->page['leftmenu']		= true;
	}
	
	public function page_planetmenu()
	{
		$this->page['planetmenu']	= true;
	}
	
	public function page_footer()
	{
		$this->page['footer']		= true;
	}
	
	public function show($file)
	{		
		global $USER, $CONF, $LNG, $db;
		if($this->page['header'] == true)
			$this->header();
			
		if($this->page['topnav'] == true)
			$this->topnav();
			
		if($this->page['leftmenu'] == true)
			$this->leftmenu();
			
		if($this->page['planetmenu'] == true)
			$this->planetmenu();
			
		if($this->page['footer'] == true)
			$this->footer();

		$this->assign_vars(array(
			'sql_num'	=> ((!defined('INSTALL') || !defined('IN_ADMIN')) && $USER['authlevel'] == 3 && $CONF['debug'] == 1) ? "<center><div id=\"footer\">SQL Abfragen:".$db->get_sql()." (".round($db->time, 4)." Sekunden) - Seiten generiert in ".round(microtime(true) - STARTTIME, 4)." Sekunden</div></center>" : "",
		));
		$this->display($file);
	}
	
	public function gotoside($dest, $time = 3)
	{
		$this->assign_vars(array(
			'gotoinsec'	=> $time,
			'goto'		=> $dest,
		));
	}
	
	public function message($mes, $dest = false, $time = 3, $Fatal = false)
	{
		$this->page_header();
		if(!$Fatal){
			$this->page_topnav();
			$this->page_leftmenu();
			$this->page_planetmenu();
		}
		$this->page_footer();

		$this->assign_vars(array(
			'mes'		=> $mes,
			'fcm_info'	=> $LNG['fcm_info'],
			'Fatal'		=> $Fatal,
		));
		$this->gotoside($dest, $time);
		$this->show('error_message_body.tpl');
	}
}

?>