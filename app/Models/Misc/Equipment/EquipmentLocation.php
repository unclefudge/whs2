<?php

namespace App\Models\Misc\Equipment;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class EquipmentLocation extends Model {

    protected $table = 'equipment_location';
    protected $fillable = ['site_id', 'other', 'status', 'notes', 'company_id', 'created_by', 'created_at', 'updated_at', 'updated_by'];

    /**
     * A EquipmentLocation has many items.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function items()
    {
        return $this->hasMany('App\Models\Misc\Equipment\EquipmentLocationItem', 'location_id');
    }


    /**
     * A EquipmentLocation MAY belongs to a Site
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function site()
    {
        return ($this->site_id) ? $this->belongsTo('App\Models\Site\Site') : NULL;
    }

    /**
     * A EquipmentLocation MAY have extra items.
     *
     * @return collection
     */
    public function extraItems()
    {
        return EquipmentLocationItem::where('location_id', $this->id)->where('extra', '>', 0)->get();
    }

    /**
     * A Equipment specific items.
     *
     * @return collection
     */
    public function equipment($equipment_id = '')
    {
        return ($equipment_id) ? EquipmentLocationItem::where('location_id', $this->id)->where('equipment_id', $equipment_id)->first() : EquipmentLocationItem::where('location_id', $this->id)->get();
    }

    /**
     * A EquipmentLocation has many items - List format.
     *
     * @return STRING
     */
    public function itemsList()
    {
        $str = '';
        foreach ($this->items as $item) {
            $str = "($item->qty) $item->item_name<br>";
        }
        return $str;
    }

    /**
     * Get the Item Total  (getter)
     */
    public function getNameAttribute()
    {
        return ($this->site_id) ? $this->site->suburb . ' (' . $this->site->name . ')' : $this->other;
    }

    /**
     * Get the Item Total  (getter)
     */
    public function getName2Attribute()
    {
        return ($this->site_id) ? $this->site->code . ' &nbsp; ' . $this->site->name  : $this->other;
    }


    /**
     * The "booting" method of the model.
     *
     * Overrides parent function
     *
     * @return void
     */
    public static function boot()
    {
        parent::boot();

        if (Auth::check()) {
            // create a event to happen on creating
            static::creating(function ($table) {
                $table->created_by = Auth::user()->id;
                $table->updated_by = Auth::user()->id;
                $table->company_id = 3; //Auth::user()->company_id;
            });

            // create a event to happen on updating
            static::updating(function ($table) {
                $table->updated_by = Auth::user()->id;
            });
        }
    }
}