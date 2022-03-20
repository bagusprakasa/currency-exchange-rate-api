<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Currency;
use App\Models\CurrencyRate;
use Illuminate\Support\Facades\DB;

class CurrencyController extends Controller
{
    public function insertCurrency()
    {
        DB::beginTransaction();
        try {
            $allCurrencies = json_decode(Http::get('https://openexchangerates.org/api/currencies.json?app_id=68d594634873459082f9bd1229c32088'), true);

            foreach ($allCurrencies as $key => $value) {
                if (!Currency::where('code', $key)->exists()) {
                    $newCurrency = new Currency;
                    $newCurrency->code = $key;
                    $newCurrency->name = $value;
                    $newCurrency->save();
                }
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'data' => $allCurrencies
            ], 200);

        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => 'Internal Server Error',
            ], 500);
        }
    }

    public function updateCurrencyRate()
    {
        DB::beginTransaction();
        try {
            $data = json_decode(Http::get('https://openexchangerates.org/api/latest.json?app_id=68d594634873459082f9bd1229c32088'), true);
            $timestamp = $data['timestamp'];
            // return $timestamp;
            foreach ($data['rates'] as $key => $value) {
                if (!CurrencyRate::where('code', $key)->where('timestamp', $timestamp)->exists()) {
                    $newRate = new CurrencyRate;
                    $newRate->code = $key;
                    $newRate->rate = $value;
                    $newRate->timestamp = $timestamp;
                    $newRate->save();
                }
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'data' => $data
            ], 200);

        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => 'Internal Server Error',
            ], 500);
        }
    }

    public function getAllCurrencies()
    {
        try {
            $allCurrencies = Currency::orderBy('code', 'asc')->pluck('name','code');
            return response()->json([
                'status' => 'success',
                'data' => $allCurrencies
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Internal Server Error',
            ], 500);
        }
    }

    public function getCurrencyRateByCode($code)
    {
        try {
            $currencyRate = CurrencyRate::where('code', strtoupper($code))->orderBy('timestamp', 'desc')->select('code', 'rate', 'created_at')->first();

            return response()->json([
                'status' => 'success',
                'base' => 'USD',
                'data' => $currencyRate
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Internal Server Error',
            ], 500);
        }
    }
}
