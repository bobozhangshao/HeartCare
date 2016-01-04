<?php
/**
 * Created by PhpStorm.
 * User: zhangshaobo
 * Date: 15/12/14
 * Time: 19:14
 */

defined('_JEXEC') or die('Restricted Access');

use Joomla\Registry\Registry;

class HeartCareModelDevice extends JModelAdmin
{
    public function getTable($type = 'Device', $prefix = 'HeartCareTable', $config = array())
    {
        return JTable::getInstance($type, $prefix, $config);
    }

    /**
     * Abstract method for getting the form from the model.
     *
     * @param   array $data Data for the form.
     * @param   boolean $loadData True if the form is to load its own data (default case), false if not.
     *
     * @return  mixed  A JForm object on success, false on failure
     *
     * @since   12.2
     */
    public function getForm($data = array(), $loadData = true)
    {
        //是从forms中找到device对应
        $form = $this->loadForm(
            'com_heartcare.device',
            'device',
            array(
                'control' => 'jform',
                'load_data' => $loadData
            )
        );


        if (empty($form))
        {
            return false;
        }

        return $form;
    }

    /**
     * Method to get the data that should be injected in the form.
     * 获得应该键入到form中的数据.
     * @return  array  The default data is an empty array.
     *
     * @since   1.6
     */
    protected function loadFormData()
    {
        $data = JFactory::getApplication()->getUserState(
            'com_heartcare.device.edit.data',
            array()
        );

        if (empty($data))
        {
            $data = $this->getItem();
        }

        return $data;
    }

    /**
     * Method to get a single record.
     *
     * @param   integer  $pk  The id of the primary key.
     *
     * @return  mixed  Object on success, false on failure.
     */
    public function getItem($pk = null)
    {
        if ($item = parent::getItem($pk))
        {
            // Convert the images field to an array.
            $registry = new Registry;
            $registry->loadString($item->images);
            $item->images = $registry->toArray();

            //Convert the sensors field to an array
            $registry = new Registry;
            $registry->loadString($item->sensors);
            $item->sensors = $registry->toArray();
        }

        return $item;
    }

    /**
     * Method to save the form data.
     *
     * @param   array  $data  The form data.
     *
     * @return  boolean  True on success.
     *
     * @since   1.6
     * */
    public function save($data)
    {
        $dispatcher = JEventDispatcher::getInstance();
        $pk         = (!empty($data['id'])) ? $data['id'] : (int) $this->getState('device.id');
        $isNew      = true;
        $table      = $this->getTable();
        $context    = $this->option . '.' . $this->name;
        $key = $table->getKeyName();

        // Include the plugins for the on save events.
        JPluginHelper::importPlugin($this->events_map['save']);

        // Allow an exception to be thrown.
        try
        {
            // Load the row if saving an existing record.
            if ($pk > 0)
            {
                $table->load($pk);
                $isNew = false;
            }

            // Bind the data.
            if (!$table->bind($data))
            {
                $this->setError($table->getError());

                return false;
            }

            // Prepare the row for saving
            $this->prepareTable($table);

            // Check the data.
            if (!$table->check())
            {
                $this->setError($table->getError());

                return false;
            }

            // Trigger the before save event.
            $result = $dispatcher->trigger($this->event_before_save, array($context, $table, $isNew));

            if (in_array(false, $result, true))
            {
                $this->setError($table->getError());

                return false;
            }

            //保存其他device_id相同的条目
            if(!$isNew){
                $db1    = $this->getDbo();
                $query1 = $db1->getQuery(true)
                    ->select('*')
                    ->from($db1->quoteName('#__health_device'))
                    ->where('device_id = '.$db1->quote($this->getItem()->device_id));
                $db1->setQuery($query1);
                $result1 = $db1->loadObjectList();
                foreach($result1 as $i => $row ){
                    $db = JFactory::getDbo();
                    $query = $db->getQuery(true);
                    $images = json_encode($this->getItem()->images);
                    $sensors = json_encode($this->getItem()->sensors);

                    $fields = array(
                        $db->quoteName('device_id') . ' = ' . $db1->quote($this->getItem()->device_id),
                        $db->quoteName('device_type') . ' = '. $db1->quote($this->getItem()->device_type),
                        $db->quoteName('produce_date') . ' = '. $db1->quote($this->getItem()->produce_date),
                        $db->quoteName('images') . ' = '. $db1->quote($images),
                        $db->quoteName('sensors') . ' = '. $db1->quote($sensors),
                        $db->quoteName('description') . ' = '. $db1->quote($this->getItem()->description),
                        $db->quoteName('service') . ' = '. $db1->quote($this->getItem()->service)
                    );

                    $conditions = array(
                        $db->quoteName('device_id') . ' = ' . $db1->quote($row->device_id),
                        $db->quoteName('device_type') . ' = ' . $db1->quote($row->device_type)
                    );
                    $query->update($db->quoteName('#__health_device'))->set($fields)->where($conditions);
                    $db->setQuery($query);
                    $db->loadObjectList();
                }
            }

            $isStore = $table->store();

            // Store the data.
            if (!$isStore)
            {
                $this->setError($table->getError());

                return false;
            }

            // Clean the cache.
            $this->cleanCache();

            // Trigger the after save event.
            $dispatcher->trigger($this->event_after_save, array($context, $table, $isNew));
        }
        catch (Exception $e)
        {
            $this->setError($e->getMessage());

            return false;
        }

        if (isset($table->$key))
        {
            $this->setState($this->getName() . '.id', $table->$key);
        }

        $this->setState($this->getName() . '.new', $isNew);

        if ($this->associationsContext && JLanguageAssociations::isEnabled())
        {
            $associations = $data['associations'];

            // Unset any invalid associations
            $associations = Joomla\Utilities\ArrayHelper::toInteger($associations);

            // Unset any invalid associations
            foreach ($associations as $tag => $id)
            {
                if (!$id)
                {
                    unset($associations[$tag]);
                }
            }

            // Show a notice if the item isn't assigned to a language but we have associations.
            if ($associations && ($table->language == '*'))
            {
                JFactory::getApplication()->enqueueMessage(
                    JText::_(strtoupper($this->option) . '_ERROR_ALL_LANGUAGE_ASSOCIATED'),
                    'notice'
                );
            }

            // Adding self to the association
            $associations[$table->language] = (int) $table->$key;

            // Deleting old association for these items
            $db    = $this->getDbo();
            $query = $db->getQuery(true)
                ->delete($db->qn('#__associations'))
                ->where($db->qn('context') . ' = ' . $db->quote($this->associationsContext))
                ->where($db->qn('id') . ' IN (' . implode(',', $associations) . ')');
            $db->setQuery($query);
            $db->execute();

            if ((count($associations) > 1) && ($table->language != '*'))
            {
                // Adding new association for these items
                $key   = md5(json_encode($associations));
                $query = $db->getQuery(true)
                    ->insert('#__associations');

                foreach ($associations as $id)
                {
                    $query->values(((int) $id) . ',' . $db->quote($this->associationsContext) . ',' . $db->quote($key));
                }

                $db->setQuery($query);
                $db->execute();
            }
        }

        return true;
    }

    /**
     * method to get the script that have to be incuded on the form
     * @return string Script files
     */
    public function getScript()
    {
        return 'administrator/components/com_heartcare/models/forms/device.js';
    }
}