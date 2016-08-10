<?php

/**
 * polls extension for Contao Open Source CMS
 *
 * Copyright (C) 2013 Codefog
 *
 * @package polls
 * @author  Codefog <http://codefog.pl>
 * @author  Kamil Kuzminski <kamil.kuzminski@codefog.pl>
 * @license LGPL
 */


/**
 * Add a palette to tl_content
 */
$GLOBALS['TL_DCA']['tl_content']['palettes']['poll'] = '{type_legend},type,headline;{include_legend},poll,poll_current, poll_random, poll_newest, javascriptField;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID,space';


/**
 * Add a field to tl_content
 */
$GLOBALS['TL_DCA']['tl_content']['fields']['poll'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_content']['poll'],
	'exclude'                 => true,
	'inputType'               => 'select',
	'options_callback'        => array('tl_content_poll', 'getPolls'),
	'eval'                    => array('includeBlankOption'=>true, 'chosen'=>true, 'tl_class'=>'w50'),
	'sql'                     => "int(10) unsigned NOT NULL default '0'"
);

$GLOBALS['TL_DCA']['tl_content']['fields']['poll_current'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_content']['poll_current'],
	'exclude'                 => true,
	'inputType'               => 'checkbox',
	'eval'                    => array('tl_class'=>'w50 m12'),
	'sql'                     => "char(1) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_content']['fields']['poll_random'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_content']['poll_random'],
	'exclude'                 => true,
	'inputType'               => 'checkbox',
	'eval'                    => array('tl_class'=>'w50 m12'),
	'sql'                     => "char(1) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_content']['fields']['poll_newest'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_content']['poll_newest'],
	'exclude'                 => true,
	'inputType'               => 'checkbox',
	'eval'                    => array('tl_class'=>'w50 m12'),
	'sql'                     => "char(1) NOT NULL default ''"
);


$GLOBALS['TL_DCA']['tl_content']['fields']['javascriptField'] = array 
(
	'input_field_callback' => array('tl_content_poll', 'changeCheckboxStates')
);

/**
 * Provide miscellaneous methods that are used by the data configuration array.
 */
class tl_content_poll extends Backend
{

	/**
	 * Get all polls and return them as array
	 * @return array
	 */
	public function getPolls()
	{
		$arrPolls = array();
		$objPolls = $this->Database->execute("SELECT id, title FROM tl_poll" . (\Poll::checkMultilingual() ? " WHERE lid=0" : "") . " ORDER BY title");

		while ($objPolls->next())
		{
			$arrPolls[$objPolls->id] = $objPolls->title;
		}

		return $arrPolls;
	}

	public function changeCheckboxStates() {
		return '<script type="text/javascript">
			document.getElementById("opt_poll_current_0").addEventListener("click", function() {
				document.getElementById("opt_poll_random_0").disabled = !document.getElementById("opt_poll_random_0").disabled;
				document.getElementById("opt_poll_random_0").checked = false;
				document.getElementById("opt_poll_newest_0").disabled = !document.getElementById("opt_poll_newest_0").disabled;
				document.getElementById("opt_poll_newest_0").checked = false;
			});
			document.getElementById("opt_poll_random_0").addEventListener("click", function() {
				document.getElementById("opt_poll_current_0").disabled = !document.getElementById("opt_poll_current_0").disabled;
				document.getElementById("opt_poll_current_0").checked = false;
				document.getElementById("opt_poll_newest_0").disabled = !document.getElementById("opt_poll_newest_0").disabled;
				document.getElementById("opt_poll_newest_0").checked = false;
			});
			document.getElementById("opt_poll_newest_0").addEventListener("click", function() {
				document.getElementById("opt_poll_random_0").disabled = !document.getElementById("opt_poll_random_0").disabled;
				document.getElementById("opt_poll_random_0").checked = false;
				document.getElementById("opt_poll_current_0").disabled = !document.getElementById("opt_poll_current_0").disabled;
				document.getElementById("opt_poll_current_0").checked = false;
			});

		</script>';
	}
}
