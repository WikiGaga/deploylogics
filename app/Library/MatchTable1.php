<?php
namespace App\Library;
use Illuminate\Support\Facades\DB;

class MatchTable1
{
    public function index(){
        /*
            select * from laravel8vu.users where not EXISTS
            (select * from blog.users where blog.users.id = laravel8vu.users.id)
        */

        $table = 'persons';
        $table_id = 'person_id';
        $this->insertData($table,$table_id);
        $this->updateData($table,$table_id);
        $this->deleteData($table,$table_id);
        dd("Data Transferred Perfect");
    }
    public function insertData($table,$table_id){
        $live_database = DB::connection('live_oracle');
        $local_database = DB::connection('oracle');

        $liveDBTbl = $live_database->table($table)->pluck($table_id)->toArray();
        $localDBTbl = $local_database->table($table)->pluck($table_id)->toArray();

        // live to local new data insert
        $liveTblCreatedIds = [];
        foreach ($liveDBTbl as $k=>$liveId){
            if(!in_array($liveId,$localDBTbl)){
                array_push($liveTblCreatedIds,$liveId);
            }
        }
        $liveDBTblData = $live_database->table($table)->whereIn($table_id,$liveTblCreatedIds)->get();
        foreach($liveDBTblData as $liveData){
            $local_database->table($table)->insert((array) $liveData);
        }

        // local to live new data insert
        $localTblCreatedIds = [];
        foreach ($localDBTbl as $k=>$localId){
            if(!in_array($localId,$liveDBTbl)){
                array_push($localTblCreatedIds,$localId);
            }
        }
        $localDBTblData = $local_database->table($table)->whereIn($table_id,$localTblCreatedIds)->get();
        foreach($localDBTblData as $localData){
            $live_database->table($table)->insert((array) $localData);
        }
    }
    public function updateData($table,$table_id){
        $live_database = DB::connection('live_oracle');
        $local_database = DB::connection('oracle');

        // live to local new data insert
        $liveTblUpdatedIds = $live_database->table($table)
            ->whereRaw('created_at < updated_at')
            ->pluck($table_id)->toArray();

        $liveDBTblData = $live_database->table($table)->whereIn($table_id,$liveTblUpdatedIds)->get();
        foreach($liveDBTblData as $liveData){
            $local_database->table($table)->where($table_id,$liveData->$table_id)->update((array) $liveData);
        }

        // local to live new data insert
        $localTblUpdatedIds = $local_database->table($table)
            ->whereRaw('created_at < updated_at')
            ->pluck($table_id)->toArray();

        $localDBTblData = $local_database->table($table)->whereIn($table_id,$localTblUpdatedIds)->get();
        foreach($localDBTblData as $localData){
            $live_database->table($table)->where($table_id,$localData->$table_id)->update((array) $localData);
        }

    }
    public function deleteData($table,$table_id){
        $live_database = DB::connection('live_oracle');
        $local_database = DB::connection('oracle');
        $deleted_entry_table = 'roles';
        $deleted_table_id = 'id';
        $deleted_name_col = 'display_name';
        $deleted_id_col = 'description';

        $liveDBTbl = $live_database->table($deleted_entry_table)
            ->where($deleted_name_col,$table)
            ->select($deleted_table_id,$deleted_id_col)->get();
        $localDBTbl = $local_database->table($deleted_entry_table)
            ->where($deleted_name_col,$table)
            ->select($deleted_table_id,$deleted_id_col)->get();

        $deleted_ids = [];
        foreach ($liveDBTbl as $li){
            array_push($deleted_ids,$li);
        }
        foreach ($localDBTbl as $lo){
            array_push($deleted_ids,$lo);
        }
        foreach($deleted_ids as $deleted_arr){
            if($local_database->table($table)->where($table_id,(int)$deleted_arr->$deleted_id_col)->exists()){
                $local_database->table($table)->where($table_id,(int)$deleted_arr->$deleted_id_col)->delete();
                $local_database->table($deleted_entry_table)->where($deleted_table_id,(int)$deleted_arr->$deleted_table_id)->delete();
            }
            if($live_database->table($table)->where($table_id,(int)$deleted_arr->$deleted_id_col)->exists()){
                $live_database->table($table)->where($table_id,(int)$deleted_arr->$deleted_id_col)->delete();
                $live_database->table($deleted_entry_table)->where($deleted_table_id,(int)$deleted_arr->$deleted_table_id)->delete();
            }
        }
    }
}
