<?php

namespace LaraPayNG\DataRepositories;

use Carbon\Carbon;
use DB;

class LaravelDBRepository implements DataRepository
{
    public function getTransactionDataFrom($gatewayTable, $transactionId)
    {
        return DB::table($gatewayTable)->find($transactionId);
    }

    public function getAllTransactionsFrom($gatewayTable)
    {
        return DB::table($gatewayTable)->get();
    }

    public function getAllSuccessfulTransactionsFrom($gatewayTable)
    {
        $data = null;

        switch ($gatewayTable) {

            case starts_with($gatewayTable, ['voguepay']):
                $data = DB::table($gatewayTable)->where('status', 'Approved')->get();
                break;

            case starts_with($gatewayTable, ['gtpay']):
                $data = DB::table($gatewayTable)->where('gtpay_response_description', 'Approved by Financial Institution')->get();
                break;

            case starts_with($gatewayTable, ['cashenvoy']):
                $data = DB::table($gatewayTable)->where('response_description', 'CashEnvoy transaction successful.')->get();
                break;

            case starts_with($gatewayTable, ['simplepay']):
                $data = DB::table($gatewayTable)->where('response_description', 'CashEnvoy transaction successful.')->get();
                break;

            case starts_with($gatewayTable, ['webpay']):
                $data = DB::table($gatewayTable)->where('response_description', 'CashEnvoy transaction successful.')->get();
                break;
        }

        return $data;
    }

    public function getAllFailedTransactionsFrom($gatewayTable)
    {
        $data = null;

        switch ($gatewayTable) {

            case starts_with($gatewayTable, ['voguepay']):
                $data = DB::table($gatewayTable)->where('status', '!=', 'Pending')
                    ->where('status', '!=', 'Approved')->get();
                break;

            case starts_with($gatewayTable, ['gtpay']):
                $data = DB::table($gatewayTable)->where('gtpay_response_description', '!=', 'Pending')
                    ->where('gtpay_response_description', '!=', 'Approved by Financial Institution')->get();
                break;

            case starts_with($gatewayTable, ['cashenvoy']):
                $data = DB::table($gatewayTable)->where('response_description', '!=', 'Pending')
                    ->where('response_description', '!=', 'CashEnvoy transaction successful.')->get();
                break;

            case starts_with($gatewayTable, ['simplepay']):
                $data = DB::table($gatewayTable)->where('response_description', '!=', 'Pending')
                    ->where('response_description', '!=', 'CashEnvoy transaction successful.')->get();
                break;

            case starts_with($gatewayTable, ['webpay']):
                $data = DB::table($gatewayTable)->where('response_description', '!=', 'Pending')
                    ->where('response_description', '!=', 'CashEnvoy transaction successful.')->get();
                break;
        }

        return $data;
    }

    public function saveTransactionDataTo($gatewayTable, $data)
    {
        DB::beginTransaction();

        try {
            $id = DB::table($gatewayTable)->insertGetId($data);
            DB::commit();

            return $id;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    public function updateTransactionDataFrom($gatewayTable, $data, $id)
    {
        DB::beginTransaction();

        try {
            $id = DB::table($gatewayTable)->where('id', $id)->update($data);
            DB::commit();

            return $id;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    public function deleteStaleTransactionDataFrom($gatewayTable, $statusColumnName, $days = 3, $with_failed = 'false', $successfulTransactionName = null)
    {
        DB::beginTransaction();

        try {
            if ($with_failed != 'true') {
                DB::table($gatewayTable)
                    ->where($statusColumnName, 'Pending')
                    ->where('updated_at', '<=', Carbon::now()->subDays($days))
                    ->delete();

                DB::commit();

                return true;
            } else {
                DB::table($gatewayTable)
                    ->where($statusColumnName, $successfulTransactionName)
                    ->where('updated_at', '<=', Carbon::now()->subDays($days))
                    ->delete();

                DB::commit();

                return true;
            }
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    public function updateTransactionDataWhere($name, $id, $table, $valueToUpdate)
    {
        DB::beginTransaction();

        try {
            $id = DB::table($table)->where($name, $id)->update($valueToUpdate);

            DB::commit();

            return $id;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    public function getTransactionDataWhere($name, $id, $table)
    {
        return DB::table($table)->where($name, $id)->first();
    }

    public function countStaleTransactionDataFrom($gatewayTable, $statusColumnName, $successfulTransactionName, $days = 3, $with_failed = 'false')
    {
        $date = Carbon::now()->subDays($days);
        $data = null;

        switch ($with_failed) {
            case 'true':

                $data = DB::table($gatewayTable)
                    ->where($statusColumnName, '!=', $successfulTransactionName)
                    ->where('updated_at', '<=', $date)
                    ->count();
                break;

            case 'false':
                $data = DB::table($gatewayTable)
                    ->where($statusColumnName, 'Pending')
                    ->where('updated_at', '<=', $date)
                    ->count();
                break;
        }

        return $data;
    }

    public function getItemsPaidForInTransaction($transactionId, $gatewayTable, $returnType = 'json')
    {
        $items = DB::table($gatewayTable)->select(['items'])->find($transactionId);

        if ($returnType == 'array') {
            return json_decode($items, true);
        }

        return $items;
    }
}
