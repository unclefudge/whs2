<?php

namespace App\Mail\Misc;

use App\Models\Misc\Equipment\EquipmentLocationItem;
use App\Models\Site\Site;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class EquipmentMaterialTransfer extends Mailable implements ShouldQueue {

    use Queueable, SerializesModels;

    public $site;
    public $item;
    public $qty;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Site $site, EquipmentLocationItem $item,  $qty)
    {
        $this->site = $site;
        $this->item = $item;
        $this->qty = $qty;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails/misc/equipment-material-transfer')->subject('SafeWorksite - Equipment Material Transfer');
    }
}
