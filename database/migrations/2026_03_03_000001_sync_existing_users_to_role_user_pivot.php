<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Sync existing users with role_id to the role_user pivot table.
     * This ensures users created before Laratrust have proper pivot entries.
     */
    public function up(): void
    {
        $usersWithRoles = DB::table('users')
            ->whereNotNull('role_id')
            ->where('user_type', 'staff_members')
            ->get(['id', 'role_id', 'company_id']);

        foreach ($usersWithRoles as $user) {
            $exists = DB::table('role_user')
                ->where('user_id', $user->id)
                ->where('role_id', $user->role_id)
                ->exists();

            if (!$exists) {
                DB::table('role_user')->insert([
                    'role_id' => $user->role_id,
                    'user_id' => $user->id,
                    'user_type' => 'App\Models\User',
                    'company_id' => $user->company_id,
                ]);
            }
        }
    }

    public function down(): void
    {
        // No rollback needed — removing pivots could break the system
    }
};
