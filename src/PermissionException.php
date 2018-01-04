<?php

namespace PhpCalendar;

class PermissionException extends \Exception
{
    public function __construct($message = null)
    {
        if ($message == null) {
            $message = __('permission-error');
        }
        parent::__construct();
    }
}
