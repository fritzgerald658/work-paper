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
        return $user->id === $workingPaper->user_id;
    }

    /**
     * Determine if the user can update the working paper.
     */
    public function update(User $user, WorkingPaper $workingPaper): bool
    {
        // Can only update if it's their own and not yet submitted
        return $user->id === $workingPaper->user_id && $workingPaper->status !== 'submitted';
    }

    /**
     * Determine if the user can delete the working paper.
     */
    public function delete(User $user, WorkingPaper $workingPaper): bool
    {
        return $user->id === $workingPaper->user_id && $workingPaper->status === 'draft';
    }
}