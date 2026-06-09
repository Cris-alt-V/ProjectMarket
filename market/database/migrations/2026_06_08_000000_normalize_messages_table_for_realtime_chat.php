<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('messages')) {
            return;
        }

        if (!Schema::hasColumn('messages', 'conversation_id')) {
            DB::statement('ALTER TABLE messages ADD COLUMN conversation_id BIGINT');
            DB::statement('UPDATE messages SET conversation_id = COALESCE(product_id, id)');
            DB::statement('ALTER TABLE messages ALTER COLUMN conversation_id SET NOT NULL');
            DB::statement('CREATE INDEX IF NOT EXISTS messages_conversation_id_index ON messages (conversation_id)');
        }

        if (!Schema::hasColumn('messages', 'product_id')) {
            DB::statement('ALTER TABLE messages ADD COLUMN product_id BIGINT NULL');
            DB::statement('CREATE INDEX IF NOT EXISTS messages_product_id_index ON messages (product_id)');
        }

        if (Schema::hasColumn('messages', 'updated_at')) {
            DB::statement('ALTER TABLE messages DROP COLUMN updated_at');
        }

        if (Schema::hasColumn('messages', 'subject')) {
            DB::statement('ALTER TABLE messages DROP COLUMN subject');
        }

        DB::statement('ALTER TABLE messages ALTER COLUMN created_at SET DEFAULT CURRENT_TIMESTAMP');
    }

    public function down(): void
    {
        if (!Schema::hasTable('messages')) {
            return;
        }

        if (!Schema::hasColumn('messages', 'subject')) {
            DB::statement('ALTER TABLE messages ADD COLUMN subject VARCHAR(255) NULL');
        }

        if (!Schema::hasColumn('messages', 'updated_at')) {
            DB::statement('ALTER TABLE messages ADD COLUMN updated_at TIMESTAMP NULL');
        }
    }
};
