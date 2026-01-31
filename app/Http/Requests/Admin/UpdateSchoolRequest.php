<?php

namespace App\Http\Requests\Admin;

class UpdateSchoolRequest extends BaseStudioSchoolRequest
{
    /**
     * Get the entity type.
     */
    protected function entityType(): string
    {
        return 'school';
    }

    /**
     * Get the required permission.
     */
    protected function permission(): string
    {
        return 'manage_schools';
    }

    /**
     * Get the status field name.
     */
    protected function statusField(): string
    {
        return 'school_status_id';
    }
}
