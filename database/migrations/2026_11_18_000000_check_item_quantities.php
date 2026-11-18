<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private $quantities = [
        'inventories' => ['quantity', 0],
        'recipes' => ['result_quantity', 1],
        'recipe_ingredient' => ['quantity', 1],
    ];

    public function up(): void
    {
        foreach ($this->quantities as $table => [$column, $min]) {
            $name = $table.'_quantity_check';

            if (DB::getDriverName() === 'sqlite') {
                foreach (['insert', 'update'] as $action) {
                    DB::statement("CREATE TRIGGER {$name}_{$action} BEFORE $action ON $table
                        WHEN typeof(NEW.$column) != 'integer' OR NEW.$column NOT BETWEEN $min AND 4294967295
                        BEGIN SELECT RAISE(ABORT, 'Invalid quantity'); END");
                }
            } else {
                DB::statement("ALTER TABLE $table ADD CONSTRAINT $name CHECK ($column BETWEEN $min AND 4294967295)");
            }
        }
    }

    public function down(): void
    {
        foreach (array_keys($this->quantities) as $table) {
            $name = $table.'_quantity_check';
            $driver = DB::getDriverName();

            if ($driver === 'sqlite') {
                DB::statement("DROP TRIGGER {$name}_insert");
                DB::statement("DROP TRIGGER {$name}_update");
            } else {
                $type = $driver === 'mysql' && !DB::connection()->isMaria() ? 'CHECK' : 'CONSTRAINT';
                DB::statement("ALTER TABLE $table DROP $type $name");
            }
        }
    }
};
