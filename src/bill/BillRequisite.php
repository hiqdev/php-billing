<?php

namespace hiqdev\php\billing\bill;

class BillRequisite
{
    /** @var int|string|null */
    protected $id;

    protected ?string $name = null;

    public function __construct($id = null, string $name = null)
    {
        $this->id = $id;
        $this->name = $name;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }
}
