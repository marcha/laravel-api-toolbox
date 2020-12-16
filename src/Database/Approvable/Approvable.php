<?php

namespace Erpmonster\Database\Approvable;


trait Approvable
{

    /**
     * Determine if the given resource is approved
     *
     * @return bool
     */
    public function isApproved()
    {
        return !is_null($this->approved_at);
    }

    /**
     * Mark the given resource as approved.
     *
     * @return bool
     */
    public function markAsApproved()
    {
        return $this->forceFill([
            'APPROVED_AT' => $this->freshTimestamp(),
            'REJECTED_AT' => null,
        ])->save();
    }

    /**
     * Determine if resource is rejected.
     *
     * @return bool
     */
    public function isRejected()
    {
        return !is_null($this->REJECTED_AT);
    }

    /**
     * Mark the given resporce as rejected.
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
