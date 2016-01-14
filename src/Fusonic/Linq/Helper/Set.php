<?php
/*
 * This file is part of Fusonic-linq.
 * https://github.com/fusonic/fusonic-linq
 *
 * (c) Fusonic GmbH
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fusonic\Linq\Helper;


class Set
{
    private $objects = [];

    /**
     * If the value is not in the set, it will be added and true is returned. otherwise false is returned.
     * @return bool
     */
    public function add($value)
    {
        return !$this->find($value, true);
    }

    /**
     * If the value is in the set, it will be removed and true is returned. otherwise false is returned.
     * @param $value
     * @return bool
     */
    public function remove($value)
    {
        $hash = self::hash($value);
        if(array_key_exists($hash, $this->objects)) {
            unset($this->objects[$hash]);
            return true;
        }
        return false;
    }

    /**
     * Returns true if the value exist in the set. Otherwise false.
     * @return bool
     */
    public function contains($value)
    {
        $hash = self::hash($value);
        return array_key_exists($hash, $this->objects);
    }

    // Finds the given value and returns true if it was found. Otherwise return false.
    private function find($value, $add = false)
    {
        $hash = self::hash($value);
        if(array_key_exists($hash, $this->objects)) {
            return true;
        }
        else if($add) {
            $this->objects[$hash] = $value;
        }
        return false;
    }

    private static function hash($value)
    {
        if (is_object($value)) {
            return spl_object_hash($value);
        } elseif (is_scalar($value)) {
            return "s_$value";
        } elseif (is_array($value)) {
            return 'a_' . md5(json_encode($value));
        } else if($value === null) {
            return null;
        }
        else throw new \InvalidArgumentException("Value type is not supported.");
    }
}