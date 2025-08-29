<?php

namespace App\Traits;

trait FileTrait
{
    /**
     * Determines the file category based on the MIME type.
     *
     * @param string $mime The MIME type of the file.
     * @return string Returns 'images' if the MIME type contains 'image',
     *                'documents' if it contains 'pdf', 'word', or 'excel',
     *                and 'others' for any other MIME type.
     */

    function getFileCategory($mime)
    {
        if (str_contains($mime, 'image')) return 'images';

        if (str_contains($mime, 'pdf') || str_contains($mime, 'word') || str_contains($mime, 'excel')) return 'documents';

        return 'others';
    }

    /**
     * Logs an audit entry
     *
     * @param int $fileId The id of the file the action is being performed on
     * @param string $action The action being performed on the file
     *
     * @return void
     */
    protected function logAudit($fileId, $action)
    {
        $file = \App\Models\StudyFile::findOrFail($fileId);

        //* Don't log private files
        // if ($file->is_private) return;

        //* Don't track owner actions
        if ($file->is_owner()) return;

        \App\Models\AuditLog::create([
            'user_id'           => \Illuminate\Support\Facades\Auth::id(),
            'study_file_id'     => $fileId,
            'action'            => $action,
            'ip_address'        => request()->ip(),
            'user_agent'        => request()->userAgent(),
        ]);
    }
}
