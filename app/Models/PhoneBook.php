<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PhoneBook extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'phone_books';

    protected $fillable = [
        'tenant_id',
        'phonebook_group_id',
        'school_id',
        'session_id',
        'name',
        'contact_no',
        'is_active',
        'created_by',
        'modified_by',
    ];

    public function phonebookgroup()
    {
        return $this->hasOne(PhonebookGroup::class, 'id', 'phonebook_group_id');
    }
    
}
