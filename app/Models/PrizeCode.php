<?php

namespace App\Models;

use App\Models\Model;
use Carbon\Carbon;

class PrizeCode extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
       'name', 'code', 'user_id', 'use_limit', 'start_at', 'end_at', 'is_active'
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'prize_codes';

     /**
     * Dates on the model to convert to Carbon instances.
     *
     * @var array
     */
    public $dates = ['start_at', 'end_at'];

    /**
     * Validation rules for creation.
     *
     * @var array
     */
    public static $createRules = [
        'name' => 'required', 
    ];
    
    /**
     * Validation rules for updating.
     *
     * @var array
     */
    public static $updateRules = [
        'name' => 'required', 
    ];


    /**********************************************************************************************
    
        RELATIONS

    **********************************************************************************************/

    /**
     * Get the user logs attached to this code.
     */
    public function redeemers()
    {
        return $this->hasMany('App\Models\User\UserPrizeLog', 'prize_id');
    }

    /**
     * Get the user who generated the invitation code.
     */
    public function user() 
    {
        return $this->belongsTo('App\Models\User\User');
    }

    /**
     * Gets the decoded output json
     *
     * @return array
     */
    public function getRewardsAttribute()
    { 
        $rewards = [];
        if($this->output) {
            $assets = $this->getRewardItemsAttribute();

            foreach($assets as $type => $a)
            {
                $class = getAssetModelString($type, false);
                foreach($a as $id => $asset)
                {
                    $rewards[] = (object)[
                        'rewardable_type' => $class,
                        'rewardable_id' => $id,
                        'quantity' => $asset['quantity']
                    ];
                }
            }
        }
        return $rewards;
    }

    /**
     * Interprets the json output and retrieves the corresponding items
     *
     * @return array
     */
    public function getRewardItemsAttribute()
    {
        return parseAssetData(json_decode($this->output, true));
    }

    /**
     * @return string
     */
    public function getNameWithCodeAttribute()
    { 
        $usedcode = $this->redeemers->count();
        return $usedcode.'/'.($this->use_limit);
    }

     /**
     * Check if the code is active or not.
     *
     * @return string
     */
    public function getActiveAttribute() {
        if ($this->start_at && $this->end_at && $this->start_at->isPast() && $this->end_at->isFuture() || $this->start_at == null && $this->end_at && $this->end_at->isFuture() || $this->start_at && $this->start_at->isPast() && $this->end_at == null || $this->start_at == null && $this->end_at == null && $this->is_active) {
            return true;
        } 
        return false;
    }

}
