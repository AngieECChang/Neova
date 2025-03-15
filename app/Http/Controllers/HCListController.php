<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HCListController extends Controller
{
  public function HCList()
  {
      return view('hc-list');
  }
}