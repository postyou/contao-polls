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

namespace Polls;


/**
 * Front end module "poll".
 */
class ModulePoll extends \Module
{

	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate = 'mod_poll';


	/**
	 * Display a wildcard in the back end
	 * @return string
	 */
	public function generate()
	{
		if (TL_MODE == 'BE')
		{
			$objTemplate = new \BackendTemplate('be_wildcard');

			$objTemplate->wildcard = '### POLL ###';
			$objTemplate->title = $this->headline;
			$objTemplate->id = $this->id;
			$objTemplate->link = $this->name;
			$objTemplate->href = 'contao/main.php?do=themes&amp;table=tl_module&amp;act=edit&amp;id=' . $this->id;

			return $objTemplate->parse();
		}

		// Return if there is no poll
		if (!$this->poll && !$this->poll_current && !$this->poll_random && !$this->poll_newest)
		{
			return '';
		}

		return parent::generate();
	}


	/**
	 * Generate the module
	 */
	protected function compile()
	{
		$intPoll = $this->poll;

		
		

		// Try to find the active poll
		if ($this->poll_current)
		{
			$time = time();
			$objCurrentPoll = $this->Database->prepare("SELECT id FROM tl_poll WHERE (showStart='' OR showStart<?) AND (showStop='' OR showStop>?)" . (!BE_USER_LOGGED_IN ? " AND published=1" : "") . " ORDER BY showStart DESC, activeStart DESC")
											 ->limit(1)
											 ->execute($time, $time);

			if ($objCurrentPoll->numRows)
			{
				$intPoll = $objCurrentPoll->id;
			}
		} else if ($this->poll_random) {
			$lastPoll = $_SESSION['LAST_POLL'];
			if ($_SESSION['POLL'][$lastPoll] == $GLOBALS['TL_LANG']['MSC']['voteSubmitted']) {
				$intPoll = $lastPoll;
			} else {
				$objRandomPoll = $this->Database->prepare("SELECT id FROM tl_poll WHERE ". (!BE_USER_LOGGED_IN ? " published=1" : "") . " ORDER BY RAND()")
											 ->limit(1)->execute();
				if ($objRandomPoll->numRows)
				{
					$intPoll = $objRandomPoll->id;
				}
			}
			
		} else if ($this->poll_newest) {
			$objNewestPoll = $this->Database->prepare("SELECT id FROM tl_poll WHERE ". (!BE_USER_LOGGED_IN ? " published=1" : "") . " ORDER BY tstamp DESC")
											 ->limit(1)->execute();
			if ($objNewestPoll->numRows)
			{
				$intPoll = $objNewestPoll->id;
			}
		}

				

		// Return if there is no poll
		if (!$intPoll)
		{
			$this->Template->poll = '';
			return;
		}

		$objPoll = new \Poll($intPoll);
		$this->Template->poll = $objPoll->generate();
	}
}
