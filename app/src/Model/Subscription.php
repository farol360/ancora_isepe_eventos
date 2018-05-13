<?php
declare(strict_types=1);

namespace Farol360\Ancora\Model;

class Subscription
{
    public $id;
    public $id_user;
    public $id_event;
    public $payd;
    public $workload;
    public $is_certificate;
    public $img_certificate;
    public $code_certificate;
    public $date_certificate;

    public function __construct(array $data = [])
    {
     $this->id               = $data['id'] ?? null;
     $this->id_user          = $data['id_user'] ?? null;
     $this->id_event         = $data['id_event'] ?? null;
     $this->payd             = $data['payd'] ?? null;
     $this->workload         = $data['workload'] ?? null;
     $this->is_certificate   = $data['is_certificate'] ?? null;
     $this->img_certificate  = $data['img_certificate'] ?? null;
     $this->code_certificate = $data['code_certificate'] ?? null;
     $this->date_certificate = $data['date_certificate'] ?? null;
    }


}
