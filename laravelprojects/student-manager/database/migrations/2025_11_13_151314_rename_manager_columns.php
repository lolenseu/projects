<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameManagerColumns extends Migration
{
    public function up()
    {
        if (Schema::hasTable('managers')) {
            if (Schema::hasColumn('managers', 'status')) {
                if (!Schema::hasColumn('managers', 'student_id')) {
                    Schema::table('managers', function (Blueprint $table) {
                        $table->string('student_id')->nullable()->after('user_id');
                    });
                }
                Schema::table('managers', function (Blueprint $table) {
                    $table->dropColumn('status');
                });
            }
            if (Schema::hasColumn('managers', 'description')) {
                Schema::table('managers', function (Blueprint $table) {
                    $table->renameColumn('description', 'department');
                });
            } else {
                if (!Schema::hasColumn('managers', 'department')) {
                    Schema::table('managers', function (Blueprint $table) {
                        $table->text('department')->nullable()->after('name');
                    });
                }
            }
        }
    }

    public function down()
    {
        if (Schema::hasTable('managers')) {
            if (Schema::hasColumn('managers', 'department')) {
                Schema::table('managers', function (Blueprint $table) {
                    $table->renameColumn('department', 'description');
                });
            }
            if (Schema::hasColumn('managers', 'student_id')) {
                Schema::table('managers', function (Blueprint $table) {
                    $table->dropColumn('student_id');
                });
                if (!Schema::hasColumn('managers', 'status')) {
                    Schema::table('managers', function (Blueprint $table) {
                        $table->enum('status', ['active', 'inactive'])->default('active')->after('user_id');
                    });
                }
            }
        }
    }
}