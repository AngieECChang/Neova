<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Services\DatabaseConnectionService;

class CaseController extends Controller
{
  public function case_update(Request $request, $id)
  {
    $databaseName = session('DB'); // 可根據條件動態變更
    $db = DatabaseConnectionService::setConnection($databaseName);

    $currentCase = $db->table('case')
      ->where('caseID', $id)
      ->select(
        'caseNoDisplay',
        'case_type'
      )
      ->first();

    if (!$currentCase) {
      return response()->json(['success' => false, 'message' => '個案不存在']);
    }

    $newData = [
      'caseNoDisplay' => $request->caseNo,
      'case_type' => $request->caseType
    ];

    $oldData = [
      'caseNoDisplay' => $currentCase->caseNoDisplay,
      'case_type' => $currentCase->case_type
    ];

    if ($newData == $oldData) {
      return response()->json(['success' => false, 'message' => '資料無變更']);
    }else{
      if ($newData['caseNoDisplay'] != $oldData['caseNoDisplay']) {
        $db->table('case_log')
        ->insert([
          'caseID' => $id,
          'date' => now(),
          'action' => 'update',
          'function' => 'change_caseNoDisplay',
          'old_value' => $oldData['caseNoDisplay'],
          'new_value' => $newData['caseNoDisplay'],
          'filler' => session('user_id')
        ]);
      }
      if ($newData['case_type'] != $oldData['case_type']) {
        $db->table('case_log')
        ->insert([
          'caseID' => $id,
          'date' => now(),
          'action' => 'update',
          'function' => 'change_caseType',
          'old_value' => $oldData['case_type'],
          'new_value' => $newData['case_type'],
          'filler' => session('user_id')
        ]);
      }

      $affected = $db->table('case')
      ->where('caseID', $id)
      ->update([
        'caseNoDisplay' => $request->caseNo,
        'case_type' => $request->caseType
      ]);
  
      if ($affected) {
        return response()->json(['success' => true, 'message' => '更新成功']);
      } else {
        return response()->json(['success' => false, 'message' => '更新失敗']);
      }
    }
  }
}