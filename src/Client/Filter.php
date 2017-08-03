<?php declare(strict_types = 1);

namespace TreeHouse\NewRelicApi\Client;

class Filter
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var mixed
     */
    private $value;

    /**
     * @param string $name
     * @param mixed  $value
     */
    public function __construct(string $name, $value = null)
    {
        $this->name = $name;

        if ($value !== null) {
            $this->value = $value;
        }
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }
}
