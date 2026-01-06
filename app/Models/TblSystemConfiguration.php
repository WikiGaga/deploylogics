<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class TblSystemConfiguration extends Model
{
    protected $table = 'tbl_system_configuration';
    protected $primaryKey = 'system_configuration_id';

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }

    protected $fillable = [
        'system_configuration_id',
        'config_key',
        'config_value',
        'config_type',
        'config_group',
        'config_description',
        'business_id',
        'company_id',
        'branch_id',
        'system_configuration_user_id',
    ];

    public static function getValue($key, $default = null, $branchId = null)
    {
        $branchId = $branchId ?? session('user_branch') ?? auth()->user()->branch_id ?? null;

        $config = self::where('config_key', $key)
            ->where('branch_id', $branchId)
            ->first();

        if (!$config) {
            return $default;
        }

        return self::convertValue($config->config_value, $config->config_type);
    }

    public static function setValue($key, $value, $type = 'text', $group = null, $description = null, $branchId = null)
    {
        $branchId = $branchId ?? session('user_branch') ?? auth()->user()->branch_id ?? null;

        $stringValue = self::convertToString($value, $type);

        $config = self::where('config_key', $key)
            ->where('branch_id', $branchId)
            ->first();

        if ($config) {
            $config->config_value = $stringValue;
            $config->config_type = $type;
            if ($group) $config->config_group = $group;
            if ($description) $config->config_description = $description;
            $config->save();
        } else {
            $config = new self();
            $config->system_configuration_id = \App\Library\Utilities::uuid();
            $config->config_key = $key;
            $config->config_value = $stringValue;
            $config->config_type = $type;
            $config->config_group = $group;
            $config->config_description = $description;
            $config->business_id = auth()->user()->business_id ?? null;
            $config->company_id = auth()->user()->company_id ?? null;
            $config->branch_id = $branchId;
            $config->system_configuration_user_id = auth()->user()->id ?? null;
            $config->save();
        }

        return $config;
    }

    public static function getAll($group = null, $branchId = null)
    {
        try {
            $branchId = $branchId ?? session('user_branch') ?? (auth()->check() ? auth()->user()->branch_id : null);

            if (!$branchId) {
                return [];
            }

            if (!\Schema::hasTable('tbl_system_configuration')) {
                return [];
            }

            $query = self::where('branch_id', $branchId);

            if ($group) {
                $query->where('config_group', $group);
            }

            $configs = $query->get();

            $result = [];
            foreach ($configs as $config) {
                if ($config && isset($config->config_key)) {
                    $result[$config->config_key] = self::convertValue($config->config_value, $config->config_type);
                }
            }

            return $result;
        } catch (\Exception $e) {
            \Log::error('TblSystemConfiguration::getAll error: ' . $e->getMessage());
            return [];
        }
    }

    private static function convertValue($value, $type)
    {
        if ($value === null) {
            return null;
        }

        switch ($type) {
            case 'number':
                return is_numeric($value) ? (float)$value : null;
            case 'boolean':
                return in_array(strtolower($value), ['1', 'true', 'yes', 'on'], true);
            case 'json':
                return json_decode($value, true);
            default:
                return $value;
        }
    }

    private static function convertToString($value, $type)
    {
        switch ($type) {
            case 'boolean':
                return $value ? '1' : '0';
            case 'json':
                return is_string($value) ? $value : json_encode($value);
            case 'number':
                return (string)$value;
            default:
                return (string)$value;
        }
    }
}

