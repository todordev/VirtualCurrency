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

class VirtualcurrencyModelCommodity extends JModelAdmin
{
    /**
     * Returns a reference to the a Table object, always creating it.
     *
     * @param   string $type    The table type to instantiate
     * @param   string $prefix A prefix for the table class name. Optional.
     * @param   array  $config Configuration array for model. Optional.
     *
     * @return  VirtualcurrencyTableCommodity  A database object
     * @since   1.6
     */
    public function getTable($type = 'Commodity', $prefix = 'VirtualcurrencyTable', $config = array())
    {
        return JTable::getInstance($type, $prefix, $config);
    }

    /**
     * Method to get the record form.
     *
     * @param   array   $data     An optional array of data for the form to interrogate.
     * @param   boolean $loadData True if the form is to load its own data (default case), false if not.
     *
     * @return  JForm   A JForm object on success, false on failure
     * @since   1.6
     */
    public function getForm($data = array(), $loadData = true)
    {
        // Get the form.
        $form = $this->loadForm($this->option . '.commodity', 'commodity', array('control' => 'jform', 'load_data' => $loadData));
        if (empty($form)) {
            return false;
        }

        return $form;
    }

    /**
     * Method to get the data that should be injected in the form.
     *
     * @return  mixed   The data for the form.
     * @since   1.6
     */
    protected function loadFormData()
    {
        // Check the session for previously entered form data.
        $data = JFactory::getApplication()->getUserState($this->option . '.edit.commodity.data', array());
        if (!$data) {
            $data            = $this->getItem();

            $moneyFormatter = VirtualcurrencyHelper::getMoneyFormatter();

            if (array_key_exists('price_real', $data->params) and $data->params['price_real'] !== '') {
                $data->params['price_real'] = $moneyFormatter->format($data->params['price_real']);
            }

            if (array_key_exists('price_virtual', $data->params) and $data->params['price_virtual'] !== '') {
                $data->params['price_virtual'] = $moneyFormatter->format($data->params['price_virtual']);
            }
        }

        return $data;
    }

    /**
     * Save data into the DB
     *
     * @param array $data The data about item
     *
     * @return   int  Item ID
     */
    public function save($data)
    {
        $id             = Joomla\Utilities\ArrayHelper::getValue($data, 'id', 0, 'int');
        $title          = Joomla\Utilities\ArrayHelper::getValue($data, 'title');
        $description    = Joomla\Utilities\ArrayHelper::getValue($data, 'description');
        $published      = Joomla\Utilities\ArrayHelper::getValue($data, 'published', 0, 'int');
        $inStock        = Joomla\Utilities\ArrayHelper::getValue($data, 'in_stock');
        $params         = Joomla\Utilities\ArrayHelper::getValue($data, 'params', array(), 'array');

        if (!$description) {
            $description = null;
        }

        if (!is_numeric($inStock)) {
            $inStock = null;
        }

        $moneyParser     = VirtualcurrencyHelper::getMoneyFormatter();
        $numberFormatter = VirtualcurrencyHelper::getNumberFormatter();

        $params['price_real']    = ($params['price_real'] !== '') ? $numberFormatter->format($moneyParser->parse($params['price_real'])) : '';
        $params['price_virtual'] = ($params['price_virtual'] !== '') ? $numberFormatter->format($moneyParser->parse($params['price_virtual'])) : '';

        // Load a record from the database
        $row = $this->getTable();
        $row->load($id);

        $row->set('title', $title);
        $row->set('description', $description);
        $row->set('in_stock', $inStock);
        $row->set('published', $published);
        $row->set('params', json_encode($params));

        $this->prepareImages($row, $data);

        $row->store(true);

        return $row->get('id');
    }

    /**
     * Prepare and sanitise the table prior to saving.
     *
     * @param VirtualcurrencyTableCommodity $table
     * @param array                       $data
     *
     * @since    1.6
     */
    protected function prepareImages($table, $data)
    {
        $params          = JComponentHelper::getParams($this->option);
        /** @var  $params Joomla\Registry\Registry */

        $mediaFolder     = JPath::clean(JPATH_ROOT . '/' . $params->get('media_folder', 'images/virtualcurrency'));

        if (!empty($data['image'])) {
            // Delete old image.
            if ($table->get('image')) {
                $file = JPath::clean($mediaFolder . '/' . $table->get('image'));
                if (JFile::exists($file)) {
                    JFile::delete($file);
                }
            }

            $table->set('image', $data['image']);
        }

        if (!empty($data['image_icon'])) {
            // Delete old icon.
            if ($table->get('image_icon')) {
                $file = JPath::clean($mediaFolder . '/' . $table->get('image_icon'));
                if (JFile::exists($file)) {
                    JFile::delete($file);
                }
            }

            $table->set('image_icon', $data['image_icon']);
        }
    }

    /**
     * Upload an image
     *
     * @param array $image
     *
     * @throws RuntimeException
     * @return string
     */
    public function uploadImage($image, $type)
    {
        $app = JFactory::getApplication();
        /** @var $app JApplicationAdministrator */

        $uploadedFile = Joomla\Utilities\ArrayHelper::getValue($image, 'tmp_name');
        $uploadedName = Joomla\Utilities\ArrayHelper::getValue($image, 'name');
        $errorCode    = Joomla\Utilities\ArrayHelper::getValue($image, 'error');

        $params          = JComponentHelper::getParams($this->option);
        /** @var  $params Joomla\Registry\Registry */

        // Prepare media folder.
        $mediaFolder     = JPath::clean(JPATH_ROOT . '/' . $params->get('media_folder', 'images/virtualcurrency'));
        if (!JFolder::exists($mediaFolder)) {
            JFolder::create($mediaFolder);
        }

        // Joomla! media extension parameters
        /** @var  $mediaParams Joomla\Registry\Registry */
        $mediaParams   = JComponentHelper::getParams('com_media');

        $file          = new Prism\File\File();

        // Prepare size validator.
        $KB            = 1024 * 1024;
        $fileSize      = (int)$app->input->server->get('CONTENT_LENGTH');
        $uploadMaxSize = $mediaParams->get('upload_maxsize') * $KB;

        // Prepare file size validator
        $sizeValidator = new Prism\File\Validator\Size($fileSize, $uploadMaxSize);

        // Prepare server validator.
        $serverValidator = new Prism\File\Validator\Server($errorCode);

        // Prepare image validator.
        $imageValidator = new Prism\File\Validator\Image($uploadedFile, $uploadedName);

        // Get allowed mime types from media manager options
        $mimeTypes = explode(',', $mediaParams->get('upload_mime'));
        $imageValidator->setMimeTypes($mimeTypes);

        // Get allowed image extensions from media manager options
        $imageExtensions = explode(',', $mediaParams->get('image_extensions'));
        $imageValidator->setImageExtensions($imageExtensions);

        $file
            ->addValidator($sizeValidator)
            ->addValidator($imageValidator)
            ->addValidator($serverValidator);

        // Validate the file
        if (!$file->isValid()) {
            throw new RuntimeException($file->getError());
        }

        // Generate temporary file name
        $ext        = JFile::makeSafe(JFile::getExt($image['name']));
        $suffix     = (strcmp('image', $type) === 0) ? '_image' : '_icon';
        $filename   = Prism\Utilities\StringHelper::generateRandomString(12) . $suffix .'.' . $ext;

        $destinationFile  = JPath::clean($mediaFolder . DIRECTORY_SEPARATOR . $filename);

        if (!JFile::upload($uploadedFile, $destinationFile)) {
            throw new \RuntimeException(\JText::_('COM_VIRTUALCURRENCY_ERROR_FILE_CANT_BE_UPLOADED'));
        }

        return $filename;
    }

    /**
     * Delete image only
     *
     * @param int $id Item ID.
     * @param string $mediaFolder
     * @param string $type
     */
    public function removeImage($id, $mediaFolder, $type)
    {
        // Load category data
        $row = $this->getTable();
        $row->load($id);

        if (strcmp('image', $type) === 0 and $row->get('image')) {
            $file = JPath::clean($mediaFolder . '/' . $row->get('image'));
            if (JFile::exists($file)) {
                JFile::delete($file);
            }

            $row->set('image', null);
        }

        if (strcmp('icon', $type) === 0 and $row->get('image_icon')) {
            $file = JPath::clean($mediaFolder . '/' . $row->get('image_icon'));
            if (JFile::exists($file)) {
                JFile::delete($file);
            }

            $row->set('image_icon', null);
        }

        $row->store(true);
    }

    /**
     * Check for dependencies in transactions and users.
     *
     * @param array $ids Currency ID.
     *
     * @return array
     */
    public function prepareDependencies(array $ids)
    {
        $returnResults = array(
            'ids' => array(),
            'excluded' => array()
        );

        foreach ($ids as $key => $id) {
            $db     = $this->getDbo();
            $query1 = $db->getQuery(true);

            $query1
                ->select('COUNT(*) AS number_of_results')
                ->from($db->quoteName('#__vc_transactions'))
                ->where($db->quoteName('item_id') . '=' . (int)$id);

            $query2 = $db->getQuery(true);
            $query2
                ->select('COUNT(*) AS number_of_results')
                ->from($db->quoteName('#__vc_usercommodities'))
                ->where($db->quoteName('commodity_id') . '=' . (int)$id)
                ->where($db->quoteName('number') . ' > 0');

            $query1->union($query2);

            $db->setQuery($query1);
            $results = (array)$db->loadColumn();
            $results = array_filter($results);

            if (count($results)) {
                $returnResults['excluded'][] = $id;
                unset($ids[$key]);
            }
        }
        
        $returnResults['ids'] = $ids;

        return $returnResults;
    }
}
