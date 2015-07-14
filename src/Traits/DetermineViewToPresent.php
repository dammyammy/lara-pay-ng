<?php


namespace LaraPayNG\Traits;


use LaraPayNG\Events\TransactionSuccessful;
use LaraPayNG\Events\TransactionUnsuccessful;
//

trait DetermineViewToPresent {

    /**
     * @param $result
     *
     * @return \Illuminate\View\View
     */
    private function determineViewToPresent($result)
    {
        if($this->determineTransactionStatus($result) == 'successful') {
            return view(config('lara-pay-ng.gateways.routes.success_view_name'), compact('result'));
        }

        return view(config('lara-pay-ng.gateways.routes.failure_view_name'), compact('result'));
    }


    private function dispatchAppropriateEvents($result)
    {
        switch($this->determineTransactionStatus($result)) {
            case 'successful':
                event(new TransactionSuccessful($result));
                break;

            case 'failed':
                event(new TransactionUnsuccessful($result));
                break;

            default:
                event(new TransactionUnsuccessful($result));

                break;
        }
    }

    /**
     * @param $result
     *
     * @return \Illuminate\View\View
     */
    private function determineTransactionStatus($result)
    {
        switch ($result['status']) {
            case 'Approved':
            case 'Approved by Financial Institution':
            case 'CashEnvoy transaction successful.':
                return 'successful';
            break;

            default:
                return 'failed';
                break;
        }
    }
}
