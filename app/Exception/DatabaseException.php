<?php
declare(strict_types=1);

namespace Vminder\Exception;

class DatabaseException extends \Exception
{
    public function __construct()
    {
        parent::__construct();
    }

}