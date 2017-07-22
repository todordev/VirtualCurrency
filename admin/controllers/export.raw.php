<?php
/**
 * @package      Virtualcurrency
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

/**
 * Virtualcurrency export controller
 *
 * @package      Virtualcurrency
 * @subpackage   Components
 */
class VirtualcurrencyControllerExport extends JControllerLegacy
{
    /**
     * @param string $name
     * @param string $prefix
     * @param array  $config
     *
     * @return bool|VirtualcurrencyModelExport
     */
    public function getModel($name = 'Export', $prefix = 'VirtualcurrencyModel', $config = array('ignore_request' => true))
    {
        $model = parent::getModel($name, $prefix, $config);

        return $model;
    }

    public function download()
    {
        $app = JFactory::getApplication();
        /** @var $app JApplicationAdministrator */

        $model = $this->getModel();

        $output   = $model->getCurrencies();
        $fileName = 'currencies.xml';

        jimport('joomla.filesystem.folder');
        jimport('joomla.filesystem.file');
        jimport('joomla.filesystem.path');
        jimport('joomla.filesystem.archive');

        $tmpFolder = JPath::clean($app->get('tmp_path'));

        $date = new JDate();
        $date = $date->format('d_m_Y_H_i_s');

        $archiveName = JFile::stripExt(basename($fileName)) . '_' . $date;
        $archiveFile = $archiveName . '.zip';
        $destination = JPath::clean($tmpFolder .DIRECTORY_SEPARATOR. $archiveFile);

        // compression type
        $zipAdapter   = JArchive::getAdapter('zip');
        $filesToZip[] = array(
            'name' => $fileName,
            'data' => $output
        );

        $zipAdapter->create($destination, $filesToZip, array());

        $filesize = filesize($destination);

        $app->setHeader('Content-Type', 'application/octet-stream', true);
        $app->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true);
        $app->setHeader('Content-Transfer-Encoding', 'binary', true);
        $app->setHeader('Pragma', 'no-cache', true);
        $app->setHeader('Expires', '0', true);
        $app->setHeader('Content-Disposition', 'attachment; filename=' . $archiveFile, true);
        $app->setHeader('Content-Length', $filesize, true);

        $doc = JFactory::getDocument();
        $doc->setMimeEncoding('application/octet-stream');

        $app->sendHeaders();

        echo file_get_contents($destination);
        $app->close();
    }
}
