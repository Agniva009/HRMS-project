<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * One-time migration to normalize all existing textual records to UPPERCASE.
 *
 * Add every table + column combination that must be uppercased.
 * Exception columns (email, username, password, etc.) are deliberately omitted.
 */
return new class extends Migration
{
    /**
     * Tables and their columns to normalize.
     * Extend this map as new HRMS tables are added.
     *
     * @var array<string, string[]>
     */
    private array $targets = [
        'employees' => [
            'first_name',
            'last_name',
            'father_name',
            'mother_name',
            'department',
            'designation',
            'address',
            'city',
            'state',
            'country',
        ],
        'departments' => [
            'name',
            'description',
        ],
        'designations' => [
            'title',
        ],
    ];

    /**
     * Run the migration.
     */
    public function up(): void
    {
        foreach ($this->targets as $table => $columns) {
            if (! $this->tableExists($table)) {
                continue;
            }

            foreach ($columns as $column) {
                DB::table($table)
                    ->whereNotNull($column)
                    ->update([
                        $column => DB::raw("UPPER(`{$column}`)"),
                    ]);
            }
        }
    }

    /**
     * Reverse is a no-op — we cannot recover original casing.
     */
    public function down(): void
    {
        // Irreversible: original mixed-case data is lost after uppercasing.
    }

    private function tableExists(string $table): bool
    {
        return \Illuminate\Support\Facades\Schema::hasTable($table);
    }
};
