/model<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Fix users table - add missing columns if they don't exist
        Schema::table('users', function (Blueprint $table) {
            // Add team_id if it doesn't exist
            if (!Schema::hasColumn('users', 'team_id')) {
                $table->string('team_id')->nullable()->after('user_type');
                $table->index('team_id');
            }

            // Add edition_id if it doesn't exist
            if (!Schema::hasColumn('users', 'edition_id')) {
                $table->string('edition_id')->nullable()->after('team_id');
                $table->index('edition_id');
            }

            // Add hackathon_edition_id if it doesn't exist
            if (!Schema::hasColumn('users', 'hackathon_edition_id')) {
                $table->string('hackathon_edition_id')->nullable()->after('edition_id');
                $table->index('hackathon_edition_id');
            }
        });

        // Update user_type enum to include all roles
        try {
            DB::statement("ALTER TABLE users MODIFY COLUMN user_type ENUM(
                'system_admin',
                'hackathon_admin',
                'track_supervisor',
                'workshop_supervisor',
                'team_leader',
                'team_member',
                'visitor'
            ) DEFAULT 'visitor'");
        } catch (\Exception $e) {
            // Column might already have correct enum values
        }

        // Create workshop_supervisors table if it doesn't exist
        if (!Schema::hasTable('workshop_supervisors')) {
            Schema::create('workshop_supervisors', function (Blueprint $table) {
                $table->id();
                $table->string('user_id');
                $table->string('workshop_id');
                $table->timestamps();

                $table->unique(['user_id', 'workshop_id']);
                $table->index('user_id');
                $table->index('workshop_id');
            });
        }

        // Fix editions table - add is_current column if it doesn't exist
        if (Schema::hasTable('editions')) {
            Schema::table('editions', function (Blueprint $table) {
                if (!Schema::hasColumn('editions', 'is_current')) {
                    $table->boolean('is_current')->default(false)->after('year');
                }
            });
        }

        // Ensure we have at least one current edition
        if (Schema::hasTable('editions')) {
            $currentEdition = DB::table('editions')->where('is_current', true)->first();
            if (!$currentEdition) {
                $firstEdition = DB::table('editions')->first();
                if ($firstEdition) {
                    DB::table('editions')
                        ->where('id', $firstEdition->id)
                        ->update(['is_current' => true]);
                }
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('workshop_supervisors');

        if (Schema::hasTable('editions')) {
            Schema::table('editions', function (Blueprint $table) {
                if (Schema::hasColumn('editions', 'is_current')) {
                    $table->dropColumn('is_current');
                }
            });
        }

        Schema::table('users', function (Blueprint $table) {
            $columns = ['hackathon_edition_id', 'edition_id', 'team_id'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
