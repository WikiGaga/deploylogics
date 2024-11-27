<?php

namespace App\Http\Controllers;

use App\Library\Utilities;
use Illuminate\Support\Facades\DB;

class WelcomeController extends Controller
{
    public function webservice($str)
    {
        $pdo = DB::getPdo();
        $stmt = $pdo->prepare("begin ".Utilities::getDatabaseUsername().".PRO_WA_WEBSERVICE(:p1); end;");
        $stmt->bindParam(':p1', $str);
        $stmt->execute();

        return response()->json(['success'=> true]);
    }
}
