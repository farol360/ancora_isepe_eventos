<?php
declare(strict_types=1);

namespace Farol360\Ancora\Model;

class EventType
{
    public $id;
    public $name;
    public $description;
    public $agree_terms;
    public $status;
    public $trash;



    public function __construct(array $data = [])
    {
        $this->id               = $data['id'] ?? null;
        $this->name             = $data['name'] ?? null;
        $this->description      = $data['description'] ?? null;
        $this->agree_terms      = $data['agree_terms'] ?? null;
        $this->status           = $data['status'] ?? null;
        $this->trash            = $data['trash'] ?? null;
    }
}
