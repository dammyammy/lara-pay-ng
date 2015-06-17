<?php


namespace LaraPayNG\Commands;

use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;
use Illuminate\Support\Facades\Config;
use DB;
use Carbon\Carbon;

class PurgeDatabaseCommand extends Command
{
    use ConfirmableTrait;
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'lara-pay-ng:purge-database
                            {gateway=default : The Gateway Driver currently used}
                            {--days=3 : Number of days before transaction is deemed stale}
                            {--with-failed=false : Should Failed Transactions be deleted as well?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Purges stale record off the respective gateway database. By default Pending transactions after 3 days are considered stale';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $title = 'LaraPayNG Command - Purge Database';
        $this->comment(str_repeat('*', strlen($title) + 12));
        $this->comment('*     '.$title.'     *');
        $this->comment(str_repeat('*', strlen($title) + 12));
        $this->output->writeln('');


        $gateway = trim(strtolower($this->argument('gateway')));

        $gateway = ($gateway == 'default') ? config('lara-pay-ng.gateways.driver') : $gateway;

        $days = $this->option('days');


        $withFailed = $this->option('with-failed');

        $allowedGateways = ['cashenvoy', 'gtpay', 'simplepay', 'webpay', 'voguepay'];

        if (! in_array($gateway, $allowedGateways)) {
            return $this->error(PHP_EOL.'The specified gateway is either not supported or was mis-spelt.'.PHP_EOL);
        }

        if (! is_numeric($days)) {
            return $this->error(PHP_EOL.'No. of days must be numeric.'.PHP_EOL);
        }

        if (!in_array($withFailed, ['true', 'false'])) {
            return $this->error(PHP_EOL.'With-Failed (--with-failed=) accepts only true/false.'.PHP_EOL);
        }

        $days = intval($days);



        $this->showInputtedDetails($gateway, $days, $withFailed);
        $allowFailedFields = [ 'true', 'false' ];


        if (! $this->confirm(PHP_EOL.'Is the Information Presented Above Correct? [y|N]')) {

            $gateway = $this->choice(PHP_EOL.'What Payment Gateway are you using?', $allowedGateways, array_search($gateway, $allowedGateways));

            $days = $this->anticipate(PHP_EOL.'How many days ago should pending transactions be removed?', [3, 7]);
            $withFailed = $this->choice(PHP_EOL.'I am Guessing you do not want Failed Transactions, Dating back ' . $days . ' days included?',
                $allowFailedFields, array_search($withFailed, $allowFailedFields));

            $this->showInputtedDetails($gateway, $days, $withFailed);

            $this->collectRequiredInformation($allowedGateways, $gateway, $withFailed);
        }



        $statusColumnName = $this->getStatusColumnName($gateway);
        $successfulTransactionWord = $this->getSuccessfulTransactionWord($gateway);

        $count = $this->getToBeDeletedCount($withFailed, $gateway, $statusColumnName, $successfulTransactionWord, $days);

        if($count > 0) {
            if ($this->confirm(PHP_EOL.$count . ' record(s) would be deleted from your database and cannot be restored,' .PHP_EOL. (($withFailed == 'true') ? PHP_EOL ."This Includes Transactions that were Attempted but Failed due to issues from gateway" . PHP_EOL: '') . 'Are You Sure you want to do that? [y|N]')) {
                $this->info(PHP_EOL.'Purging Stale Transactions From ' . $this->getGatewayTable($gateway) . ' Table ... '.PHP_EOL);
                $this->deleteStaleTransactions($withFailed, $gateway, $statusColumnName, $successfulTransactionWord, $days);
            }
            else {
                $this->comment('Ok! If you insist, I won\'t delete a thing. Good Bye!');
            }
        }
        else
        {
            $this->comment('There are no records considered stale, No Records Would be Deleted As they are not more than ' . $days . ' days old. Good Bye!');

        }



    }

    /**
     * @param $gateway
     * @param $days
     */
    private function showInputtedDetails($gateway, $days, $withFailed)
    {
        $this->info(PHP_EOL.'The Gateway Transaction Table you are about to purge is for: ' . $gateway.PHP_EOL);
        $this->info(PHP_EOL.'The Table Name is : ' . $this->getGatewayTable($gateway) .PHP_EOL);
        $this->info(PHP_EOL.'The standard number of days a transaction is considered stale is : ' . $days . ' day(s)'.PHP_EOL);
        $this->info(PHP_EOL.'Failed Transactions, Dating back ' . $days . ' days should be included? : ' . $withFailed.PHP_EOL);
    }

    /**
     * @param $allowedGateways
     * @param $gateway
     * @param $withFailed
     */
    private function collectRequiredInformation($allowedGateways, $gateway, $withFailed)
    {
        switch ($this->confirm('Is the Information Presented Above Correct? [y|N]')) {
            case true:
                continue;
                break;

            case false:
                $gateway = $this->choice(PHP_EOL.'What Payment Gateway are you using?', $allowedGateways, array_search($gateway, $allowedGateways));
                $days = $this->anticipate(PHP_EOL.'How many days ago should pending transactions be removed?', [3, 7]);
                $withFailed = $this->choice(PHP_EOL.'I am Guessing you do not want Failed Transactions, Dating back ' . $days . ' days included?', [true, false], $withFailed);
                $this->showInputtedDetails($gateway, $days, $withFailed);

                $this->collectRequiredInformation($allowedGateways, $gateway, $withFailed);

                break;
        }
    }

    /**
     * @param $withFailed
     * @param $gateway
     * @param $statusColumnName
     * @param $successfulTransactionName
     * @param $days
     * @return mixed
     */
    private function getToBeDeletedCount($withFailed, $gateway, $statusColumnName, $successfulTransactionName, $days)
    {
        $table = config('lara-pay-ng.gateways.' . $gateway . '.table');

        switch ($withFailed) {
            case 'true':

                return DB::table($table)
                    ->where($statusColumnName, '!=', $successfulTransactionName)
                    ->where('updated_at', '<=', Carbon::now()->subDays($days))
                    ->count();
                break;

            case 'false':
                return DB::table($table)
                    ->where($statusColumnName, 'Pending')
                    ->where('updated_at', '<=', Carbon::now()->subDays($days))
                    ->count();
                break;
        }
    }


    /**
     * @param $withFailed
     * @param $gateway
     * @param $statusColumnName
     * @param $successfulTransactionName
     * @param $days
     * @return mixed
     * @throws \Exception
     */
    private function deleteStaleTransactions($withFailed, $gateway, $statusColumnName, $successfulTransactionName, $days)
    {
        $table = config('lara-pay-ng.gateways.' . $gateway . '.table');

        switch ($withFailed) {
            case 'true':

                // Start transaction!
                DB::beginTransaction();

                try {
                    DB::table($table)
                        ->where($statusColumnName, '!=', $successfulTransactionName)
                        ->where('updated_at', '<=', Carbon::now()->subDays($days))
                        ->delete();

//                    if (! $this->confirm(PHP_EOL.'This is your Last Chance to Decide not to delete, Do you want me to stop? [y|N]')) {
//                        DB::rollback();
//
//                        $this->comment(PHP_EOL.'Ok too bad, I cannot help you if you refuse to grant the power, Till next time. Safe!'.PHP_EOL);
//                        return false;
//                    }

                    DB::commit();
                    $this->comment(PHP_EOL.'Done Deleting Stale Transactions, Till next time. Safe!'.PHP_EOL);
                    return true;
                } catch (\Exception $e) {
                    DB::rollback();
                    throw $e;
                }



                break;

            case 'false':
                return DB::table($table)
                    ->where($statusColumnName, 'Pending')
                    ->where('updated_at', '<=', Carbon::now()->subDays($days))
                    ->delete();
                break;
        }
    }

    /**
     * @param $gateway
     * @return Config
     */
    private function getGatewayTable($gateway)
    {
        return config('lara-pay-ng.gateways.' . $gateway . '.table');
    }

    /**
     * @return string
     */
    private function getStatusColumnName($gateway)
    {
        switch ($gateway) {
            case 'voguepay':
                return 'status';
                break;

            case 'gtpay':
                return 'gtpay_response_description';
                break;

            case 'webpay':
                return 'status';
                break;

            case 'simplepay':
                return 'status';
                break;

            case 'cashenvoy':
                return 'status';
                break;
        }
    }

    /**
     * @return string
     */
    private function getSuccessfulTransactionWord($gateway)
    {
        switch ($gateway) {
            case 'voguepay':
                return 'Approved';
                break;

            case 'gtpay':
                return 'Approved by Financial Institution';
                break;

            case 'webpay':
                return 'Approved by Financial Institution';
                break;

            case 'simplepay':
                return 'Approved';
                break;

            case 'cashenvoy':
                return 'Approved';
                break;
        }
    }
}
