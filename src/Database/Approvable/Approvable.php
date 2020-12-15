<?php

namespace Erpmonster\Database\Approvable;


trait Approvable
{

    /**
     * Determine if the user has verified their email address.
     *
     * @return bool
     */
    public function isApproved()
    {
        return !is_null($this->approved_at);
    }

    /**
     * Mark the given user's email as verified.
     *
     * @return bool
     */
    public function markAsApproved()
    {
        return $this->forceFill([
            'APPROVED_AT' => $this->freshTimestamp(),
        ])->save();
    }

    /**
     * Determine if the user has verified their email address.
     *
     * @return bool
     */
    public function isRejected()
    {
        return !is_null($this->REJECTED_AT);
    }

    /**
     * Mark the given user's email as verified.
     *
     * @return bool
     */
    public function markAsRejected()
    {
        return $this->forceFill([
            'REJECTED_AT' => $this->freshTimestamp(),
        ])->save();
    }
}
