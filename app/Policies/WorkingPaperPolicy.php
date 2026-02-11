<?php

namespace App\Policies;

use App\Models\User;
use App\Models\WorkingPaper;

class WorkingPaperPolicy
{
    /**
     * Determine if the user can view the working paper.
     */
    public function view(User $user, WorkingPaper $workingPaper): bool
    {
        // Admin can view any working paper
        if ($user->isAdmin()) {
            return true;
        }

        // Client can only view their own
        return $user->id === $workingPaper->user_id;
    }

    /**
     * Determine if the user can update the working paper.
     */
    public function update(User $user, WorkingPaper $workingPaper): bool
    {
        // Admin cannot update client's working paper (only review/approve/reject)
        if ($user->isAdmin()) {
            return false;
        }

        // Client can only update their own working paper if it's editable
        return $user->id === $workingPaper->user_id && $workingPaper->canBeEditedByClient();
    }

    /**
     * Determine if the user can delete the working paper.
     */
    public function delete(User $user, WorkingPaper $workingPaper): bool
    {
        // Admin cannot delete working papers
        if ($user->isAdmin()) {
            return false;
        }

        // Client can only delete their own if it's in draft status
        return $user->id === $workingPaper->user_id && $workingPaper->status === 'draft';
    }

    /**
     * Determine if the user can review (approve/reject) the working paper.
     */
    public function review(User $user, WorkingPaper $workingPaper): bool
    {
        // Only admin can review
        if (!$user->isAdmin()) {
            return false;
        }

        // Can only review if it's pending
        return $workingPaper->isPendingReview();
    }

    /**
     * Determine if the user can submit the working paper.
     */
    public function submit(User $user, WorkingPaper $workingPaper): bool
    {
        // Admin cannot submit
        if ($user->isAdmin()) {
            return false;
        }

        // Client can submit if it's their own and status allows it
        return $user->id === $workingPaper->user_id && in_array($workingPaper->status, ['draft', 'rejected']);
    }
}