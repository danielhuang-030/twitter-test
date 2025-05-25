<?php

namespace App\Params;

abstract class BaseParam
{
    private ?int $page = null;
    private ?int $perPage = null;
    private array $sortBy = [];
    private array $withs = [];
    private array $withCounts = [];

    public function getPage(): int
    {
        return $this->page ?? 1;
    }

    public function setPage(int $page): self
    {
        $this->page = $page;

        return $this;
    }

    public function getPerPage(): int
    {
        return $this->perPage ?? config('app.per_page');
    }

    public function setPerPage(int $perPage): self
    {
        $this->perPage = $perPage;

        return $this;
    }

    public function getSortBy(): array
    {
        return $this->sortBy; // Already initialized to []
    }

    public function setSortBy(string $sortBy, bool $isDesc = false): self
    {
        $this->sortBy[$sortBy] = $isDesc;

        return $this;
    }

    public function getWiths(): array
    {
        return $this->withs; // Already initialized to []
    }

    public function setWiths(array $withs): self
    {
        $this->withs = $withs;

        return $this;
    }

    public function getWithCounts(): array
    {
        return $this->withCounts; // Already initialized to []
    }

    public function setWithCounts(array $withCounts): self
    {
        $this->withCounts = $withCounts;

        return $this;
    }

    public function toArray(): array
    {
        $array = [];

        $properties = get_object_vars($this);

        foreach ($properties as $key => $value) {
            $array[$key] = $value;
        }

        return $array;
    }
}
