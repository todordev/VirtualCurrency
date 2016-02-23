<?php
/**
 * @package      VirtualCurrency
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

/**
 * VirtualCurrency export controller
 *
 * @package      VirtualCurrency
 * @subpackage   Components
 */
class VirtualCurrencyControllerExport extends JControllerLegacy
{
    public function getModel($name = 'Export', $prefix = 'VirtualCurrencyModel', $config = array('ignore_request' => true))
    {
        $model = parent::getModel($name, $prefix, $config);

        return $model;
    }

    public function download()
    {
        $app = JFactory::getApplication();
        /** @var $app JApplicationAdministrator */

        $type  = $this->input->get->getCmd('type');
        $model = $this->getModel();

        try {

            switch ($type) {
                case 'locations':
                    $output   = $model->getLocations();
                    $fileName = 'locations.xml';
                    break;

                case 'currencies':
                    $output   = $model->getCurrencies();
                    $fileName = 'currencies.xml';
                    break;

                case 'countries':
                    $output   = $model->getCountries();
                    $fileName = 'countries.xml';
                    break;

                case 'states':
                    $output   = $model->getStates();
                    $fileName = 'states.xml';
                    break;

                default: // Error
                    $output   = '';
                    $fileName = 'error.xml';
                    break;
            }

        } catch (Exception $e) {
            JLog::add($e->getMessage());
            throw new Exception(JText::_('COM_VIRTUALCURRENCY_ERROR_SYSTEM'));
        }

        jimport('joomla.filesystem.folder');
        jimport('joomla.filesystem.file');
        jimport('joomla.filesystem.path');
        jimport('joomla.filesystem.archive');

        $tmpFolder = JPath::clean($app->get('tmp_path'));

        $date = new JDate();
        $date = $date->format('d_m_Y_H_i_s');

        $archiveName = JFile::stripExt(basename($fileName)) . '_' . $date;
        $archiveFile = $archiveName . '.zip';
        $destination = $tmpFolder . DIRECTORY_SEPARATOR . $archiveFile;

        // compression type
        $zipAdapter   = JArchive::getAdapter('zip');
        $filesToZip[] = array(
            'name' => $fileName,
            'data' => $output
        );

        $zipAdapter->create($destination, $filesToZip, array());

        $filesize = filesize($destination);

        JResponse::setHeader('Content-Type', 'application/octet-stream', true);
        JResponse::setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true);
        JResponse::setHeader('Content-Transfer-Encoding', 'binary', true);
        JResponse::setHeader('Pragma', 'no-cache', true);
        JResponse::setHeader('Expires', '0', true);
        JResponse::setHeader('Content-Disposition', 'attachment; filename=' . $archiveFile, true);
        JResponse::setHeader('Content-Length', $filesize, true);

        $doc = JFactory::getDocument();
        $doc->setMimeEncoding('application/octet-stream');

        JResponse::sendHeaders();

        echo file_get_contents($destination);
        JFactory::getApplication()->close();
    }
}
