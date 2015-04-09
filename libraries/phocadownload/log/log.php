<?php
/* @package Joomla
 * @copyright Copyright (C) Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @extension Phoca Extension
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

### am - 2015.02.08 -> add HUA column
### am - 2015.03.20 -> add disable logging for anonymous

defined( '_JEXEC' ) or die( 'Restricted access' );

class PhocaDownloadLog
{
	
	public static function log($fileid, $type = 1) {
	
		$paramsC 	= JComponentHelper::getParams('com_phocadownload');
		$logging	= $paramsC->get('enable_logging', 0);
###
		$disable_anonymous_logging 	= $paramsC->get('disable_anonymous_logging', 0);
###
		// No Logging
		if ($logging == 0) {
			return false;
		}
		
		// Only Downloads
		if ($logging == 1 && $type == 2) {
			return false;
		}
		
		// Only Uploads
		if ($logging == 2 && $type == 1) {
			return false;
		}
		
		$user 	= JFactory::getUser();

###	No Anonymous Logging
		if ($disable_anonymous_logging && (int)$user->id == 0 ) {
			return false;
		}
###

		$uri 	= JFactory::getURI();
		$db 	= JFactory::getDBO();

		$row 	= JTable::getInstance('PhocaDownloadLogging', 'Table');
		$data					= array();
		$data['type']			= (int)$type;
		$data['fileid']			= (int)$fileid;
		$data['catid']			= 0;// Don't stored catid, bind the catid while displaying log
		$data['userid']			= (int)$user->id;
		$data['ip']	=			$_SERVER["REMOTE_ADDR"];
		$data['page']			= $uri->toString();

### am - 8.2.2015
 		$data['hua']	=			$_SERVER["HTTP_USER_AGENT"];
### am - 8.2.2015
 		
		if (!$row->bind($data)) {
			$this->setError($db->getErrorMsg());
			return false;
		}
		
		$jnow		= JFactory::getDate();
		$row->date	= $jnow->toSql();

		if (!$row->check()) {
			$this->setError($db->getErrorMsg());
			return false;
		}

		if (!$row->store()) {
			$this->setError($db->getErrorMsg());
			return false;
		}
		return true;
	}
}
?>