<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('machines', function (Blueprint $table) {
            $table->id();
            
            // Enterprise 39 Asset Registry Fields
            $table->string('onsite')->nullable();
            $table->string('fa_tag_no')->unique(); // FATagNo (Primary Code)
            $table->string('family_desc')->nullable();
            $table->string('supp_code')->nullable();
            $table->string('supp_name')->nullable();
            $table->string('supp_invoice')->nullable();
            $table->string('local_import')->nullable();
            $table->text('link_doc')->nullable();
            $table->string('bc_doc')->nullable();
            $table->string('acq_date')->nullable();
            $table->string('ref_sage')->nullable();
            $table->string('sage_coa')->nullable();
            $table->string('physic_tag_no')->nullable();
            $table->text('fa_desc')->nullable(); // FADesc (Machine Name / Description)
            $table->text('fa_sub_desc')->nullable(); // FASubDesc (Model details)
            $table->string('fa_unit')->nullable();
            $table->string('acq_cost')->nullable();
            $table->string('dept_code')->nullable();
            $table->string('sect_code')->nullable(); // SectCode (e.g. Sewing, Cutting)
            $table->string('sub_sect_code')->nullable();
            $table->string('loc_code')->nullable(); // LocCode (e.g. LYG-MJLK)
            $table->string('line_code')->nullable(); // LineCode (e.g. ENG PF-01)
            $table->string('serial_number')->nullable(); // SerialNumber
            $table->string('cross_check_sn')->nullable();
            $table->string('live_time')->nullable();
            $table->string('condition_status')->nullable(); // ConditionStatus
            $table->text('remark')->nullable();
            $table->text('qr_code_image')->nullable();
            $table->text('asset_image1')->nullable();
            $table->text('asset_image2')->nullable();
            $table->text('asset_image3')->nullable();
            $table->string('create_by')->nullable();
            $table->string('create_date')->nullable();
            $table->string('last_modify_by')->nullable();
            $table->string('last_modify_date')->nullable();
            $table->string('flag_sync')->nullable();
            $table->string('assignee')->nullable();
            $table->text('last_update_log')->nullable();
            $table->string('uniq_id')->unique()->nullable(); // UniqID
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('machines');
    }
};
