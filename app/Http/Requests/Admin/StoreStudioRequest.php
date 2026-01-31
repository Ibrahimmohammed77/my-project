<?php

namespace App\Http\Requests\Admin;

class StoreStudioRequest extends BaseStudioSchoolRequest
{
    /**
     * Get the entity type.
     */
    protected function entityType(): string
    {
        return 'studio';
    }

    /**
     * Get the required permission.
     */
    protected function permission(): string
    {
        return 'manage_studios';
    }

    /**
     * Get the status field name.
     */
    protected function statusField(): string
    {
        return 'studio_status_id';
    }
}
