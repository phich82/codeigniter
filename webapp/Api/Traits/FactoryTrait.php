<?php
/**
* @author Huynh Phat <phat.nguyen@persol.co.jp>
* @license [v1]
*/
namespace App\Api\Traits;

trait FactoryTrait
{
    /**
     * Resolve only for libraries
     *
     * @param string $library
     * @param string $alias
     * @param array  $params
     *
     * @return object
     */
    public function make($library, $alias = null, $params = [])
    {
        $CI =& get_instance();
        $CI->load->library($library, $params, $alias);

        return $CI->{$this->_classLoaded($library, true, $alias)};
    }

    /**
     * Resolve only for library
     *
     * @param string $library
     * @param string $alias
     * @param array  $params
     *
     * @return object
     */
    public function resolve($library, $alias = null, $params = [])
    {
        return $this->make($library, $params, $alias);
    }

    /**
     * __call (model, library...)
     *
     * @param string $type
     * @param array  $arguments
     *
     * @return void
     */
    public function __call($type, $arguments = [])
    {
        $CI =& get_instance();
        $CI->load->{$type}(...$arguments);
        $strToLower = ($type === 'library');

        return $CI->{$this->_classLoaded($arguments, $strToLower)};
    }

    /**
     * Get the loaded class
     *
     * @param  mixed $arguments
     *
     * @return string
     */
    private function _classLoaded($arguments, $strToLower = true, $alias = null)
    {
        // a string
        if (is_string($arguments)) {
            return $this->_resolveClassLoaded($arguments, $strToLower, $alias);
        }
        // an array: has the alias class
        if (count($arguments) === 2) {
            return $arguments[1];
        }
        // only get the loaded class
        return $this->_resolveClassLoaded($arguments[0], $strToLower);
    }

    /**
     * Resolve the loaded class
     *
     * @param  string $pathClassLoaded
     *
     * @return string
     */
    private function _resolveClassLoaded($pathClassLoaded, $strToLower = true, $alias = null)
    {
        $classLoaded = explode('/', $pathClassLoaded);
        $classLoaded = $strToLower ? strtolower(end($classLoaded)) : end($classLoaded);
        return $alias ?: $classLoaded;
    }
}
