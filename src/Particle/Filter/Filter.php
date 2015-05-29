<?php
/**
 * Particle.
 *
 * @link      http://github.com/particle-php for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Particle (http://particle-php.com)
 * @license   https://github.com/particle-php/Filter/blob/master/LICENSE New BSD License
 */
namespace Particle\Filter;

/**
 * Class Filter
 *
 * @package Particle\Filter
 */
class Filter
{
    /**
     * @var Chain[]
     */
    protected $chains = [];

    /**
     * @var Chain
     */
    protected $globalChain = null;

    /**
     * Construct the filter
     */
    public function __construct()
    {
    }

    /**
     * Set a filter for a value on a specific key
     *
     * @param string $key
     * @return FilterResource
     */
    public function value($key)
    {
        return $this->getFilterResource($key);
    }

    /**
     * Set a filter for the values on the given keys
     *
     * @param string[] $keys
     * @return FilterResource
     */
    public function values(array $keys)
    {
        return $this->getFilterResource($keys);
    }

    /**
     * Set a filter for all values off an array
     *
     * @return FilterResource
     */
    public function all()
    {
        return $this->getFilterResource();
    }

    /**
     * @param null|string|string[] $keys
     * @return FilterResource
     */
    public function getFilterResource($keys = null)
    {
        return new FilterResource($this, $keys);
    }

    /**
     * Set a filter rule on a chain
     *
     * @param FilterRule $rule
     * @param null $key
     */
    public function addFilterRule(FilterRule $rule, $key = null)
    {
        $this->getChain($key)->addRule($rule);
    }

    /**
     * Filter all set fields with a global chain
     *
     * @param array $data
     * @return array
     */
    public function globalFilter(array $data)
    {
        if ($this->globalChain !== null) {
            foreach ($data as $key => $value) {
                $data[$key] = $this->globalChain->filter($value);
            }
        }

        return $data;
    }

    /**
     * Filter the provided data
     *
     * @param array $data
     * @return array
     */
    public function filter(array $data)
    {
        $data = $this->globalFilter($data);

        foreach ($this->chains as $key => $chain) {
            if (array_key_exists($key, $data)) {
                $data[$key] = $chain->filter($data[$key]);
            }
        }

        return $data;
    }

    /**
     * Get a filter rule chain for a key
     *
     * @param null|string $key
     * @return Chain
     */
    protected function getChain($key)
    {
        // If no key, set global chain
        if ($key === null) {
            return $this->getGlobalChain();
        }

        // Return chain for key
        if (isset($this->chains[$key])) {
            return $this->chains[$key];
        }
        return $this->chains[$key] = $this->buildChain();
    }

    /**
     * Get the global chain for all values
     *
     * @return Chain
     */
    protected function getGlobalChain()
    {
        if ($this->globalChain === null) {
            $this->globalChain = $this->buildChain();
        }
        return $this->globalChain;
    }

    /**
     * Build a new chain of filters
     *
     * @return Chain
     */
    protected function buildChain()
    {
        return new Chain();
    }
}
