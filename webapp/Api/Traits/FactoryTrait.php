<?php
/**
 * @author Huynh Phat <phat.nguyen@gmail.com>
 * @license http://localhost:8282/api/v1/android [v1]
 */
namespace App\Api\Traits;

trait FactoryTrait
{
    /**
     * Resolve library
     *
     * @param string $library []
     * @param string $alias   []
     * @param array  $params  []
     *
     * @return object
     */
    public function make($library, $alias = null, $params = [])
    {
        $CI =& get_instance();
        $CI->load->library($library, $params, $alias);
        return $CI->{strtolower($library)};
    }

    /**
     * Resolve library
     *
     * @param string $library []
     * @param string $alias   []
     * @param array  $params  []
     *
     * @return object
     */
    public function resolve($library, $alias = null, $params = [])
    {
        return $this->make($library, $params, $alias);
    }

    /**
     * __call
     *
     * @param string $type      []
     * @param array  $arguments []
     *
     * @return void
     */
    public function __call($type, $arguments)
    {
        $CI =& get_instance();
        $CI->load->{$type}(...$arguments);
        $model = count($arguments) === 2 ? $arguments[1] : $arguments[0];
        return $CI->{$model};
    }
}
