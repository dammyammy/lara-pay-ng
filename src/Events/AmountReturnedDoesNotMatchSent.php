<?php


namespace LaraPayNG\Events;

use Illuminate\Queue\SerializesModels;

class AmountReturnedDoesNotMatchSent
{

    use SerializesModels;
    /**
     * @var
     */
    public $result;

    /**
     * Create a new event instance.
     *
     * @param $result
     */
    public function __construct($result)
    {
        $this->result = $result;
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return [ ];
    }
}
