<?php
namespace App\Library;
use Illuminate\Support\Facades\DB;

final class MatchTable
{
    protected function __construct(){
        $this->live_database = DB::connection('live_oracle');
        $this->local_database = DB::connection('oracle');
    }

    public static function mergeTable($table,$table_id){
        (new self)->insertData($table,$table_id);
        (new self)->updateData($table,$table_id);
       // (new self)->deleteData($table,$table_id);
    }
    public function insertData($table,$table_id){
        $liveDBTbl = $this->live_database->table($table)->pluck($table_id)->toArray();
        $localDBTbl = $this->local_database->table($table)->pluck($table_id)->toArray();

        // live to local new data insert
        $liveTblCreatedIds = [];
        foreach ($liveDBTbl as $k=>$liveId){
            if(!in_array($liveId,$localDBTbl)){
                array_push($liveTblCreatedIds,$liveId);
            }
        }
        $liveDBTblData = $this->live_database->table($table)->whereIn($table_id,$liveTblCreatedIds)->get();
        foreach($liveDBTblData as $liveData){
            $this->local_database->table($table)->insert((array) $liveData);
        }

        // local to live new data insert
        $localTblCreatedIds = [];
        foreach ($localDBTbl as $k=>$localId){
            if(!in_array($localId,$liveDBTbl)){
                array_push($localTblCreatedIds,$localId);
            }
        }
        $localDBTblData = $this->local_database->table($table)->whereIn($table_id,$localTblCreatedIds)->get();
        foreach($localDBTblData as $localData){
            $this->live_database->table($table)->insert((array) $localData);
        }
    }
    public function updateData($table,$table_id){
       // dd($table_id);
        // live to local new data insert
        $liveTblUpdatedIds = $this->live_database->table($table)->whereRaw('created_at < updated_at')
            ->pluck($table_id)->toArray();

        $liveDBTblData = $this->live_database->table($table)->whereIn($table_id,$liveTblUpdatedIds)->get();

        foreach($liveDBTblData as $liveData){
            $this->local_database->table($table)->where($table_id,(int)$liveData->$table_id)->update((array) $liveData);
        }
        // local to live new data insert
        $localTblUpdatedIds = $this->local_database->table($table)->whereRaw('created_at < updated_at')
            ->pluck($table_id)->toArray();

        $localDBTblData = $this->local_database->table($table)->whereIn($table_id,$localTblUpdatedIds)->get();
        foreach($localDBTblData as $localData){
            $this->live_database->table($table)->where($table_id,$localData->$table_id)->update((array) $localData);
        }
    }
    public function deleteData($table,$table_id){
        $del_tbl_name =  'roles';
        $id = 'id';
        $table_name = 'display_name';
        $form_id = 'description';

        $liveDBTbl = $this->live_database->table($del_tbl_name)
            ->where($table_name,$table)
            ->select($id,$form_id)->get();
        $localDBTbl = $this->local_database->table($del_tbl_name)
            ->where($table_name,$table)
            ->select($id,$form_id)->get();

        $deleted_ids = [];
        foreach ($liveDBTbl as $li){
            array_push($deleted_ids,$li);
        }
        foreach ($localDBTbl as $lo){
            array_push($deleted_ids,$lo);
        }
        foreach($deleted_ids as $deleted_arr){
            if($this->local_database->table($table)->where($table_id,(int)$deleted_arr->$form_id)->exists()){
                $this->local_database->table($table)->where($table_id,(int)$deleted_arr->$form_id)->delete();
                $this->local_database->table($del_tbl_name)->where($id,(int)$deleted_arr->$id)->delete();
            }
            if($this->live_database->table($table)->where($table_id,(int)$deleted_arr->$form_id)->exists()){
                $this->live_database->table($table)->where($table_id,(int)$deleted_arr->$form_id)->delete();
                $this->live_database->table($del_tbl_name)->where($id,(int)$deleted_arr->$id)->delete();
            }
        }
    }
}
