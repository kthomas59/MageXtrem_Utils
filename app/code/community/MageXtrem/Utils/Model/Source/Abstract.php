<?php

/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 13/06/16
 * Time: 16:09
 */
class MageXtrem_Utils_Model_Source_Abstract
{

    /**
     * @return array
     */
    public function toArray()
    {
        return array();
    }

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $result = array();
        foreach ($this->toArray() as $key => $value) {
            $result[] = array(
                'label' => $value,
                'value' => $key
            );
        }
        return $result;
    }

}