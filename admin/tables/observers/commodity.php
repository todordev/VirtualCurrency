<?php
/**
 * @package      VirtualCurrency
 * @subpackage   Component
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */
defined('JPATH_PLATFORM') or die;

/**
 * Abstract class defining methods that can be
 * implemented by an Observer class of a JTable class (which is an Observable).
 * Attaches $this Observer to the $table in the constructor.
 * The classes extending this class should not be instanciated directly, as they
 * are automatically instanciated by the JObserverMapper
 *
 * @package      VirtualCurrency
 * @subpackage   Component
 * @link         http://docs.joomla.org/JTableObserver
 * @since        3.1.2
 */
class VirtualcurrencyObserverCommodity extends JTableObserver
{
    /**
     * The pattern for this table's TypeAlias
     *
     * @var    string
     * @since  3.1.2
     */
    protected $typeAliasPattern = null;

    /**
     * Creates the associated observer instance and attaches it to the $observableObject
     * $typeAlias can be of the form "{variableName}.type", automatically replacing {variableName} with table-instance variables variableName
     *
     * @param   JObservableInterface $observableObject The subject object to be observed
     * @param   array                $params           ( 'typeAlias' => $typeAlias )
     *
     * @return  VirtualcurrencyObserverCommodity
     *
     * @since   3.1.2
     */
    public static function createObserver(JObservableInterface $observableObject, $params = array())
    {
        $observer = new self($observableObject);

        $observer->typeAliasPattern = Joomla\Utilities\ArrayHelper::getValue($params, 'typeAlias');

        return $observer;
    }

    /**
     * Pre-processor for $table->delete($pk)
     *
     * @param   mixed $pk An optional primary key value to delete.  If not set the instance property value is used.
     *
     * @return  void
     *
     * @since   3.1.2
     * @throws  UnexpectedValueException
     */
    public function onAfterDelete($pk)
    {
        $params         = JComponentHelper::getParams('com_virtualcurrency');
        $mediaFolder    = JPath::clean(JPATH_ROOT .'/'. $params->get('media_folder', 'images/virtualcurrency'));

        if ($this->table->get('id') > 0) {
            $db    = $this->table->getDbo();

            // Delete image.
            if ($this->table->get('image')) {
                $file = JPath::clean($mediaFolder .'/'. $this->table->get('image'));

                if (JFile::exists($file)) {
                    JFile::delete($file);
                }
            }

            // Delete icon.
            if ($this->table->get('image_icon')) {
                $file = JPath::clean($mediaFolder . '/' . $this->table->get('image_icon'));

                if (JFile::exists($file)) {
                    JFile::delete($file);
                }
            }

            // Remove the user commodity records.
            $query = $db->getQuery(true);
            $query
                ->delete($db->quoteName('#__vc_usercommodities'))
                ->where($db->quoteName('commodity_id') . ' = ' . (int)$this->table->get('id'));

            $db->setQuery($query);
            $db->execute();
        }

    }
}
