<?php
/**
 * Phinx
 *
 * (The MIT license)
 * Copyright (c) 2012 Rob Morgan
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated * documentation files (the "Software"), to
 * deal in the Software without restriction, including without limitation the
 * rights to use, copy, modify, merge, publish, distribute, sublicense, and/or
 * sell copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS
 * IN THE SOFTWARE.
 *
 * @package    Phinx
 * @subpackage Phinx\Registry
 */

namespace Phinx\Registry;

use Phinx\Registry\Exception\NoEntry;

/**
 * Registry
 */
class Registry
{
    /**
     * @var null|Registry
     */
    private static $__instance = null;

    /**
     * @var array
     */
    private static $__registry = array();


    /**
     *
     */
    private function __construct() {}


    /**
     * Return instance of registry
     *
     * @static
     * @return null|Registry
     */
    public static function instance()
    {
        if (!isset(self::$__instance)) {
            self::$__instance = new self();
        }

        return self::$__instance;
    }


    /**
     * set value
     *
     * @static
     * @param $key
     * @param $value
     */
    public static function set($key, $value)
    {

        self::$__registry[$key]  = $value;
    }

    /**
     * get value by key
     *
     * @static
     * @param $key
     * @return mixed
     * @throws Exception\NoEntry
     */
    public static function get($key)
    {
        if (!self::isRegistry($key)) {
            throw new NoEntry("No entry registred for key " . $key);
        }

        return $result = self::$__registry[$key];
    }


    /**
     * is registry?
     *
     * @static
     * @param $key
     * @return bool
     */
    public static function isRegistry($key)
    {
        return isset(self::$__registry[$key]);
    }


}