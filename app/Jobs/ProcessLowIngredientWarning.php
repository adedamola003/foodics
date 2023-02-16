<?php

namespace App\Jobs;

use App\Notifications\LowIngredientWarning;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Notification;

class ProcessLowIngredientWarning// implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private array $warningData;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($warningData)
    {
        $this->warningData = $warningData;
    }


    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        //check if notification has been sent for each ingredient in the array
        foreach ($this->warningData as $ingredientID => $ingredientData) {
            {
                if (!Cache::has("has_sent_ingredient_warning_" . $ingredientID)) {
                    //add warning sent status for ingredient to cache
                    Cache::forever("has_sent_ingredient_warning_" . $ingredientID, true);
                    //todo remove key when ingredient stock is topped up
                    //update array data usage unit to standard unit
                    $ingredientStockBalanceStandardUnitData = convertUsageUnitToIngredientUnit($this->warningData[$ingredientID]['stock_balance'], $this->warningData[$ingredientID]['unit']);
                    $ingredientMaxStockStandardUnitData = convertUsageUnitToIngredientUnit($this->warningData[$ingredientID]['ingredient_max_stock'], $this->warningData[$ingredientID]['unit']);
                    $ingredientThresholdBalanceStandardUnitData = convertUsageUnitToIngredientUnit($this->warningData[$ingredientID]['warning_threshold_balance'], $this->warningData[$ingredientID]['unit']);
                    $this->warningData[$ingredientID]['stock_balance'] = $ingredientStockBalanceStandardUnitData['quantity'];
                    $this->warningData[$ingredientID]['unit'] = $ingredientStockBalanceStandardUnitData['unit'];
                    $this->warningData[$ingredientID]['ingredient_max_stock'] = $ingredientMaxStockStandardUnitData['quantity'];
                    $this->warningData[$ingredientID]['warning_threshold_balance'] = $ingredientThresholdBalanceStandardUnitData['quantity'];
                } else {
                    //remove ingredient from array
                    unset($this->warningData[$ingredientID]);
                }

            }
        }
        //check if array is empty
        if (empty($this->warningData)) {
            //delete job from queue
            $this->delete();
            return;
        }
        //send notification
        Notification::route('mail', Config('app.merchant_notification_email'))->notify((new LowIngredientWarning($this->warningData))->delay(now()->addSeconds(5)));
    }
}
