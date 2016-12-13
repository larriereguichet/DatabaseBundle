<?php


namespace LAG\DatabaseBundle\Error;


class ErrorStack
{
    protected $stack = [];

    public function addError($type, $message)
    {
        $this->stack[] = [
            'type' => $type,
            'error' => $message,
            'date' => date('d/M/Y h:i:s')
        ];
    }

    /**
     * @return array
     */
    public function getStack()
    {
        return $this->stack;
    }
}
