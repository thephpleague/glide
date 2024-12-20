<?php

declare(strict_types=1);

namespace League\Glide\Manipulators;

abstract class BaseManipulator implements ManipulatorInterface
{
    /**
     * The manipulation params.
     */
    protected array $params = [];

    /**
     * Set the manipulation params.
     *
     * @param array $params The manipulation params.
     *
     * @return $this
     */
    public function setParams(array $params): static
    {
        $this->params = array_filter($params, fn (string $key): bool => in_array($key, $this->getApiParams()), ARRAY_FILTER_USE_KEY);

        return $this;
    }

    /**
     * Get a specific manipulation param.
     */
    public function getParam(string $name): mixed
    {
        return $this->params[$name] ?? null;
    }
}
