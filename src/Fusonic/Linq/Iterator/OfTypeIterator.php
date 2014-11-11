<?php
/**
 * Created by PhpStorm.
 * User: Burgy Benjamin <benjamin.burgy@gmail.com>
 * Date: 11.11.14
 * Time: 20:43
 */

namespace Fusonic\Linq\Iterator;

use FilterIterator;
use Fusonic\Linq\Helper;
use Iterator;

/**
 * Iterator for filtering the Linq query with a specified <b>type</b>.
 * @package Fusonic\Linq\Iterator
 */
final class OfTypeIterator
	extends
	\FilterIterator
{
	/**
	 * @var Iterator $iterator
	 */
	private $iterator;

	/**
	 * @var string
	 */
	private $type;

	/**
	 * Initializes an instance of <b>OfTypeIterator</b>.
	 *
	 * @param Iterator $iterator
	 * @param string   $type
	 */
	public function __construct(Iterator $iterator, $type)
	{
		parent::__construct($iterator);

		$this->iterator = $iterator;
		$this->type     = $type;
	}

	/**
	 * (PHP 5 &gt;= 5.1.0)<br/>
	 * Check whether the current element of the iterator is acceptable
	 * @link http://php.net/manual/en/filteriterator.accept.php
	 * @return bool true if the current element is acceptable, otherwise false.
	 */
	public function accept()
	{
		$current = $this->current();

		return $current instanceof $this->type;
	}
}