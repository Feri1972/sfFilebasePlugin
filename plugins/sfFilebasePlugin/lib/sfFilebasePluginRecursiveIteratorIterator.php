<?php
/**
 * This file is part of the sfFilebase symfony plugin.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Iterator for recursively traversing filesystem structure to render
 * tree views.
 *
 * @package    de.optimusprime.sfFilebasePlugin
 * @see        RecursiveDirectoryIterator
 * @author     Johannes Heinen <johannes.heinen@gmail.com>
 * @copyright  Johannes Heinen <johannes.heinen@gmail.com>
 */
class sfFilebasePluginRecursiveIteratorIterator extends RecursiveIteratorIterator
{
  private $tokenBegin;
  private $tokenEnd;
  public function __construct(sfFilebasePluginRecursiveDirectoryIterator $iterator, $mode=sfFilebasePluginRecursiveIteratorIterator::SELF_FIRST, $flags=0, $tokenBegin = "", $tokenEnd = "")
  {
    $this->tokenBegin = $tokenBegin;
    $this->tokenEnd = $tokenEnd;
    parent::__construct($iterator, $mode, $flags);
  }
  
  public function beginChildren()
  {
    echo $this->tokenBegin;
  }
  
  public function endChildren()
  {
    echo $this->tokenEnd;
  }
}