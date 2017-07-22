<?php
/**
 * @package      Virtualcurrency
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

use Prism\Money;
use Joomla\Utilities\ArrayHelper;
use Virtualcurrency\Intl\Helper as IntlHelper;

// no direct access
defined('_JEXEC') or die;

class VirtualcurrencyModelCurrency extends JModelAdmin
{
    /**
     * Returns a reference to the a Table object, always creating it.
     *
     * @param   string $type    The table type to instantiate
     * @param   string $prefix A prefix for the table class name. Optional.
     * @param   array  $config Configuration array for model. Optional.
     *
     * @return  VirtualcurrencyTableCurrency|bool  A database object
     * @since   1.6
     */
    public function getTable($type = 'Currency', $prefix = 'VirtualcurrencyTable', $config = array())
    {
        return JTable::getInstance($type, $prefix, $config);
    }

    /**
     * Method to get the record form.
     *
     * @param   array   $data     An optional array of data for the form to interrogate.
     * @param   boolean $loadData True if the form is to load its own data (default case), false if not.
     *
     * @return  JForm|bool   A JForm object on success, false on failure
     * @since   1.6
     */
    public function getForm($data = array(), $loadData = true)
    {
        // Get the form.
        $form = $this->loadForm($this->option . '.currency', 'currency', array('control' => 'jform', 'load_data' => $loadData));
        if (!$form) {
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
        $data = JFactory::getApplication()->getUserState($this->option . '.edit.currency.data', array());
        if (!$data) {
            $data = $this->getItem();

            $numberFormatter = IntlHelper::factory('joomla')->getNumberFormatter();

            if (array_key_exists('price_real', $data->params) and $data->params['price_real'] !== '') {
                $data->params['price_real'] = $numberFormatter->format($data->params['price_real']);
            }

            if (array_key_exists('price_virtual', $data->params) and $data->params['price_virtual'] !== '') {
                $data->params['price_virtual'] = $numberFormatter->format($data->params['price_virtual']);
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
     *
     * @throws InvalidArgumentException
     * @throws RuntimeException
     * @throws UnexpectedValueException
     */
    public function save($data)
    {
        $id             = ArrayHelper::getValue($data, 'id', 0, 'int');
        $title          = ArrayHelper::getValue($data, 'title');
        $description    = ArrayHelper::getValue($data, 'description');
        $published      = ArrayHelper::getValue($data, 'published', 0, 'int');
        $code           = ArrayHelper::getValue($data, 'code');
        $symbol         = ArrayHelper::getValue($data, 'symbol');
        $position       = ArrayHelper::getValue($data, 'position');
        $params         = ArrayHelper::getValue($data, 'params', array(), 'array');

        if (!$description) {
            $description = null;
        }

        // Load a record from the database
        $row = $this->getTable();
        $row->load($id);

        $row->set('title', $title);
        $row->set('description', $description);
        $row->set('code', $code);
        $row->set('symbol', $symbol);
        $row->set('position', $position);
        $row->set('published', $published);
        $row->set('params', $params);

        $this->prepareImages($row, $data);
        $this->prepareTable($row);

        $row->store(true);

        return $row->get('id');
    }

    /**
     * Prepare and sanitise the table prior to saving.
     *
     * @param VirtualcurrencyTableCurrency $table
     * @param array                       $data
     *
     * @since    1.6
     *
     * @throws UnexpectedValueException
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
     * Prepare and sanitise the table prior to saving.
     *
     * @param VirtualcurrencyTableCurrency $table
     *
     * @since    1.6
     */
    protected function prepareTable($table)
    {
        $params = $table->get('params');

        if ($table->get('id') and is_array($params)) {
            $numberParser    = IntlHelper::factory('joomla')->getNumberParser();

            $params['price_real']    = ($params['price_real'] !== '') ? $numberParser->parse($params['price_real']) : '0.00';
            $params['price_virtual'] = ($params['price_virtual'] !== '') ? $numberParser->parse($params['price_virtual']) : '0.00';

            if ($params['price_virtual'] > 0 and (int)$table->get('id') === (int)$params['currency_id']) {
                $params['price_virtual'] = 0;
                $params['currency_id'] = 0;
            }
        }

        if (!$params) {
            $params = array();
        } else {
            $params['minimum'] = (!array_key_exists('minimum', $params) or !$params['minimum']) ? 0 : abs($params['minimum']);
        }

        $table->set('params', json_encode($params));
    }

    /**
     * Upload an image
     *
     * @param array $uploadedFileData
     * @param string $type
     *
     * @return string
     *
     * @throws InvalidArgumentException
     * @throws UnexpectedValueException
     * @throws RuntimeException
     */
    public function uploadImage($uploadedFileData, $type)
    {
        $uploadedFile = ArrayHelper::getValue($uploadedFileData, 'tmp_name');
        $uploadedName = ArrayHelper::getValue($uploadedFileData, 'name');
        $errorCode    = ArrayHelper::getValue($uploadedFileData, 'error');

        $params          = JComponentHelper::getParams($this->option);
        /** @var  $params Joomla\Registry\Registry */

        // Prepare media folder.
        $mediaFolder     = JPath::clean(JPATH_ROOT .'/'. $params->get('media_folder', 'images/virtualcurrency'));
        if (!JFolder::exists($mediaFolder)) {
            JFolder::create($mediaFolder);
        }

        // Joomla! media extension parameters
        /** @var  $mediaParams Joomla\Registry\Registry */
        $mediaParams   = JComponentHelper::getParams('com_media');

        // Prepare size validator.
        $KB            = pow(1024, 2);
        $fileSize      = ArrayHelper::getValue($uploadedFileData, 'size', 0, 'int');
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

        $file = new Prism\File\File($uploadedFile);
        $file
            ->addValidator($sizeValidator)
            ->addValidator($serverValidator);

        // Validate the file
        if (!$file->isValid()) {
            throw new RuntimeException($file->getError());
        }

        // Generate file name
        $ext                = JFile::makeSafe(JFile::getExt($uploadedName));
        $suffix             = (strcmp('image', $type) === 0) ? '_image' : '_icon';
        $filename           = Prism\Utilities\StringHelper::generateRandomString(12) . $suffix .'.'. $ext;
        $destinationFile    = JPath::clean($mediaFolder .'/'. $filename);

        // Upload the file.
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
     *
     * @throws \UnexpectedValueException
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
     * Check for dependencies in transactions and virtual goods.
     *
     * @param array $ids Currency ID.
     *
     * @return array
     */
    public function prepareDependencies(array $ids)
    {
        $ids     = ArrayHelper::toInteger($ids);
        $exclude = array();

        $returnResults = array(
            'ids' => array(),
            'excluded' => array()
        );

        $mapper     = new Virtualcurrency\Commodity\Mapper(new Virtualcurrency\Commodity\Gateway\JoomlaGateway(JFactory::getDbo()));
        $repository = new Virtualcurrency\Commodity\Repository($mapper);
        $products   = $repository->fetchAll();
        
        foreach ($products as $product) {
            $params = new Joomla\Registry\Registry();
            $params->loadString($product['params']);

            if ($params->get('price_virtual') > 0 and (int)$params->get('currency_id') > 0) {
                $currencyId = (int)$params->get('currency_id');

                if (in_array($currencyId, $ids, true)) {
                    $exclude[] = $currencyId;

                    $key = array_search($currencyId, $ids, true);
                    unset($ids[$key]);
                }
            }
        }

        $returnResults['excluded'] = array_filter(array_unique($exclude));
        $returnResults['ids']      = array_filter(array_unique($ids));

        return $returnResults;
    }
}
