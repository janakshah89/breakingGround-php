<?php

namespace App\Traits;

trait RequestTrait
{
    protected $authToken = null;
    protected $userRole = null;

    public function isSuperAdmin()
    {
        return $this->currentUser->hasRole('superadmin');
    }

    public function isClientUser()
    {
        return $this->currentUser->hasRole('client');
    }

    public function isConsultant()
    {
        return $this->currentUser->hasRole('consultant');
    }

    public function isEm()
    {
        return $this->currentUser->hasRole('em');
    }

    public function isSg()
    {
        return $this->currentUser->hasRole('sg');
    }
}
