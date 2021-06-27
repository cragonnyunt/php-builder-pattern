<?php

namespace PHPBuilder;

use PHPBuilder\Exceptions\OmitException;
use PHPBuilder\Contracts\RecordInterface;
use PHPBuilder\Contracts\BuilderInterface;
use PHPBuilder\Exceptions\BuilderException;
use PHPBuilder\Exceptions\NoInputException;
use PHPBuilder\Exceptions\TypeMisMatchException;

/**
 * Base class for Builders
 */
abstract class Builder implements BuilderInterface
{
    /**
     * Internal data storage
     */
    protected $data;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->reset();
    }

    /**
     * Finally building the object
     *
     * @return RecordInterface|null
     */
    final public function build(): ?RecordInterface
    {
        try {
            $this->checkPrerequisites();

            return $this->getObject();
        } catch (\Exception $e) {
            throw $e;
        } finally {
            $this->reset();
        }
    }

    /**
     * Reset everything, so that the builder is reusable
     *
     * @return void
     */
    protected function reset()
    {
        $this->data = [];
    }

    /**
     * Getter/Setter Magic Method
     *
     * use setProperty($property):void to set $property
     * use getProperty():$property to get $property
     *
     * @param string $name
     * @param array $arguments
     *
     * @return mixed
     */
    public function __call(string $name, array $arguments)
    {
        $sign = substr($name, 0, 3);
        $sign2 = substr($name, 0, 2);
        $property = strtolower(substr($name, 3));
        $property2 = strtolower(substr($name, 2));

        if ($sign === 'set' && sizeof($arguments) > 0) {
            // setter
            $this->data[$property] = $arguments[0];
            return $this;
        } elseif ($sign === 'get' && array_key_exists($property, $this->data)) {
            // getter
            return $this->data[$property];
        } elseif ($sign === 'has' && array_key_exists($property, $this->data)) {
            // getter with ('has')
            $data = $this->data[$property];
            if (!is_bool($data)) {
                throw new TypeMisMatchException();
            }
            return $data;
        } elseif ($sign2 === 'is' && array_key_exists($property2, $this->data)) {
            // getter with ('is')
            $data = $this->data[$property2];
            if (!is_bool($data)) {
                throw new TypeMisMatchException();
            }
            return $data;
        }

        if ($sign === 'set') {
            throw new NoInputException();
        } elseif (in_array($sign, ['get', 'has', 'is'])) {
            throw new OmitException();
        }

        throw new BuilderException();
    }

    /**
     * Get the actual object
     *
     * @return RecordInterface
     */
    abstract protected function getObject(): RecordInterface;

    /**
     * Checking requirement before building actual object
     * Throw error\exception or simply return nothing to pass condition
     *
     * @throws \Exception
     */
    abstract protected function checkPrerequisites(): void;
}
