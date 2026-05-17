<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Machine extends Model
{
    use HasFactory;

    protected $fillable = [
        'onsite',
        'fa_tag_no',
        'family_desc',
        'supp_code',
        'supp_name',
        'supp_invoice',
        'local_import',
        'link_doc',
        'bc_doc',
        'acq_date',
        'ref_sage',
        'sage_coa',
        'physic_tag_no',
        'fa_desc',
        'fa_sub_desc',
        'fa_unit',
        'acq_cost',
        'dept_code',
        'sect_code',
        'sub_sect_code',
        'loc_code',
        'line_code',
        'serial_number',
        'cross_check_sn',
        'live_time',
        'condition_status',
        'remark',
        'qr_code_image',
        'asset_image1',
        'asset_image2',
        'asset_image3',
        'create_by',
        'create_date',
        'last_modify_by',
        'last_modify_date',
        'flag_sync',
        'assignee',
        'last_update_log',
        'uniq_id',
    ];
}
