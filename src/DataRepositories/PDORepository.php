<?php

namespace LaraPayNG\DataRepositories;

use PDO;
use PDOException;

class PDORepository implements DataRepository {

    /**
     * @var array
     */
    private $config;

    public function __construct(array $config)
    {

        $this->config = $config;
    }

    public function getTransactionDataFrom($gatewayTable, $transactionId)
    {

        $db = $this->setDBConnection($gatewayTable);

    }

    public function saveTransactionDataTo($gatewayTable, $data)
    {
        $db = $this->setDBConnection($gatewayTable);
    }

    public function updateTransactionDataFrom($gatewayTable, $data, $id)
    {
        $db = $this->setDBConnection($gatewayTable);
    }


    public function countStaleTransactionDataFrom(
        $gatewayTable,
        $statusColumnName,
        $successfulTransactionName,
        $days = 3,
        $with_failed = 'false'
    ) {
        $db = $this->setDBConnection($gatewayTable);
    }

    public function updateTransactionDataWhere($name, $id, $table, $valueToUpdate)
    {
        $db = $this->setDBConnection($table);
    }

    public function getTransactionDataWhere($name, $id, $table)
    {
        $db = $this->setDBConnection($table);
    }

    public function getAllTransactionsFrom($gatewayTable)
    {
        $db = $this->setDBConnection($gatewayTable);

        return $db->query("SELECT * FROM " . $gatewayTable);
    }

    public function getAllSuccessfulTransactionsFrom($gatewayTable)
    {
        $db = $this->setDBConnection($gatewayTable);
    }

    public function getAllFailedTransactionsFrom($gatewayTable)
    {
        $db = $this->setDBConnection($gatewayTable);
    }

    public function getStaleTransactionDataFrom($gatewayTable)
    {
        $db = $this->setDBConnection($gatewayTable);
    }

    public function deleteStaleTransactionDataFrom($gatewayTable, $statusColumnName, $days = 3, $with_failed = 'false')
    {
        $db = $this->setDBConnection($gatewayTable);
    }

    /**
     * @param $table
     *
     */
    private function setDBConnection($table)
    {
        try {
            $this->db = new PDO(
                $this->config['db_driver'] . ':host=' . $this->config['db_host'] . ';dbname=' . $table,
                $this->config['db_user'], $this->config['db_pass']
            );
        } catch ( PDOException $e ) {
            echo 'Connection failed: ' . $e->getMessage();
        }
    }
}
