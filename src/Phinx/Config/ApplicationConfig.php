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
 * @subpackage Phinx\Config
 */

namespace Phinx\Config;

use Symfony\Component\Yaml\Yaml,
    Phinx\Registry;

class ApplicationConfig  implements \ArrayAccess
{
    /**
     * @var array
     */
    private $__configuration = array();

    /**
     * @var null|string
     */
    private $__configFilePath = null;


    public function __construct($configFilePath)
    {
        $this->__configFilePath = $configFilePath;
        $this->setConfiguration(Yaml::parse($this->__configFilePath));
    }

    /**
     * set configuration
     * @param array $configuration
     */
    public  function setConfiguration($configuration)
    {
        $this->__configuration = $configuration;
    }

    /**
     * Return the configuration file array
     * @return array
     */
    public function toArray()
    {
        return $this->__configuration;
    }

    /**
     * save new configuration to configuration file
     *
     * @param array $configuration
     * @return int
     */
    public function save()
    {
        if (!is_file($this->__configFilePath)) {
            touch($this->__configFilePath);
        }
        return file_put_contents($this->__configFilePath, Yaml::dump($this->__configuration));
    }


    /**
     * Sets a parameter or an object.
     *
     * Objects must be defined as Closures.
     *
     * Allowing any PHP callable leads to difficult to debug problems
     * as function names (strings) are callable (creating a function with
     * the same a name as an existing parameter would break your container).
     *
     * @param string $id    The unique identifier for the parameter or object
     * @param mixed  $value The value of the parameter or a closure to defined an object
     * @return void
     */
    public function offsetSet($id, $value)
    {
        $this->__configuration[$id] = $value;
    }

    /**
     * Gets a parameter or an object.
     *
     * @param  string $id The unique identifier for the parameter or object
     * @throws InvalidArgumentException if the identifier is not defined
     * @return mixed  The value of the parameter or an object
     */
    function offsetGet($id)
    {
        if (!array_key_exists($id, $this->__configuration)) {
            throw new \InvalidArgumentException(sprintf('Identifier "%s" is not defined.', $id));
        }

        return $this->__configuration[$id] instanceof \Closure ? $this->__configuration[$id]($this) : $this->__configuration[$id];
    }

    /**
     * Checks if a parameter or an object is set.
     *
     * @param  string $id The unique identifier for the parameter or object
     * @return boolean
     */
    function offsetExists($id)
    {
        return isset($this->__configuration[$id]);
    }

    /**
     * Unsets a parameter or an object.
     *
     * @param  string $id The unique identifier for the parameter or object
     * @return void
     */
    function offsetUnset($id)
    {
        unset($this->__configuration[$id]);
    }
}