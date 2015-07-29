<?php

namespace LaraPayNG\DataRepositories;

interface DataRepository
{
    public function getTransactionDataFrom($gatewayTable, $transactionId);
    public function saveTransactionDataTo($gatewayTable, $data);
    public function updateTransactionDataFrom($gatewayTable, $data, $id);
    public function countStaleTransactionDataFrom($gatewayTable, $statusColumnName, $successfulTransactionName, $days = 3, $with_failed = 'false');
    public function deleteStaleTransactionDataFrom($gatewayTable, $statusColumnName, $days = 3, $with_failed = 'false');
    public function updateTransactionDataWhere($name, $id, $table, $valueToUpdate);
    public function getTransactionDataWhere($name, $id, $table);
    public function getAllTransactionsFrom($gatewayTable);
    public function getAllSuccessfulTransactionsFrom($gatewayTable);
    public function getAllFailedTransactionsFrom($gatewayTable);
    public function getItemsPaidForInTransaction($transactionId, $gatewayTable, $returnType);
}
