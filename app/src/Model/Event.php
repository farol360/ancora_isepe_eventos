<?php
declare(strict_types=1);

namespace Farol360\Ancora\Model;

class Event
{
    public $id;
    public $name;
    public $img_featured;
    public $id_event_type;
    public $date_event;
    public $description;
    public $price;
    public $status;
    public $trash;
    public $agree_terms;
    public $subscription_limit;
    public $workload;


    public function __construct(array $data = [])
    {
        $this->id               = $data['id'] ?? null;
        $this->name             = $data['name'] ?? null;
        $this->img_featured     = $data['img_featured'] ?? null;
        $this->id_event_type    = $data['id_event_type'] ?? null;
        $this->date_event       = $data['date_event'] ?? null;
        $this->description      = $data['description'] ?? null;
        $this->price            = $data['price'] ?? null;
        $this->status           = $data['status'] ?? null;
        $this->trash            = $data['trash'] ?? null;
        $this->agree_terms      = $data['agree_terms'] ?? null;
        $this->subscription_limit = $data['subscription_limit'] ?? null;
        $this->workload         = $data['workload'] ?? null;
    }
}
