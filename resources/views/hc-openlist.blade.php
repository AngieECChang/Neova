@extends('layouts.app')
@section('content')
<style>
  .custom-table tbody tr:hover {
    background-color: #d1ecf1 !important;  /* 淡藍色 */
}
</style>
@php
  $patient_type = array("1"=>"一般居護","2"=>"四階(IDS)","3"=>"
安寧舊制","4"=>"居整","5"=>"居整(呼吸器)","6"=>"居整(安寧)","7"=>"HAH","8"=>"自費","9"=>"長照2.0");
@endphp
<div class="row align-items-center mb-4">
  <div class="col-3">
    <h1 class="h3 text-gray-800 mb-0">收案列表</h1>
  </div>
  <div class="col-9">
    <form method="GET" action="{{ route('hc-openlist') }}" class="d-flex align-items-center justify-content-end" id="regionForm">
      <label for="region" class="visually-hidden">區域：</label>
      <select name="region" id="region" class="form-control me-2" style="width:160px" onchange="document.getElementById('regionForm').submit();">
        <option value="">全部</option>
        @foreach ($areaNames as $area)
          <option value="{{ $area }}" {{ request('region') == $area ? 'selected' : '' }}>
              {{ $area }}
          </option>
        @endforeach
      </select> 
      <a href="{{ route('hc-openlist') }}" class="btn text-white" style="background-color: orange;">新增個案</a>
    </form>
  </div>
</div>
<!-- Bootstrap Tabs for Case Types -->
<ul class="nav nav-tabs mt-3" id="caseTypeTabs" role="tablist">
  <!-- 🔹 Tabs 選項 -->
  <li class="nav-item">
    <a class="nav-link active fw-bold" id="tab-all" data-bs-toggle="tab" href="#content-all" role="tab">全部    </a>
  </li>
  @foreach ($patient_type as $key => $value)
    <li class="nav-item">
      <a class="nav-link fw-bold" id="tab-{{ $key }}" data-bs-toggle="tab" href="#content-{{ $key }}" role="tab">
       {{ $value }}
      </a>
    </li>
  @endforeach
</ul>
<div class="tab-content mt-3">
  <div class="tab-pane fade show active" id="content-all" role="tabpanel">
    @php
      // 🔹 合併所有 caseType 的個案
      $all_cases = collect($open_cases)->collapse()->collapse(); 
    @endphp

    @if ($all_cases->isEmpty()) 
      <div class="alert alert-warning text-center mt-3">
        🚨 目前沒有任何個案
      </div>
    @else
      <div class="card shadow-sm mb-4">
        <div class="card-body">
          <table class="table table-striped table-hover custom-table">
            <thead class="table-dark">
              <tr>
                <th>個案ID</th>
                <th>名稱</th>
                <th>區域</th>
                <th>類型</th>
                <th>建立時間</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($open_cases as $caseType => $areaGroups)
                @foreach ($areaGroups as $area => $cases)
                  <tr class="table-primary">
                    <td colspan="5" class="fw-bold text-left">{{ $area }}</td>
                  </tr>
                  @foreach ($cases as $case)
                    <tr>
                      <td>{{ $case->caseID }}</td>
                      <td>{{ $case->name }}</td>
                      <td>{{ $case->areaName }}</td>
                      <td>{{ $patient_type[$caseType] ?? '未知類型' }}</td>
                      <td>{{ $case->open_date }}</td>
                    </tr>
                  @endforeach
                @endforeach
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    @endif
  </div>
  @foreach ($patient_type as $key => $value)
    <div class="tab-pane fade" id="content-{{ $key }}" role="tabpanel">
    @if (!isset($open_cases[$key]) || $open_cases[$key]->isEmpty()) 
      <div class="alert alert-warning text-center mt-3">
        🚨 目前沒有「{{ $value }}」類型的個案
      </div>
    @else
      <div class="card shadow-sm mb-4">
        <div class="card-body">
          <table class="table table-striped table-hover custom-table">
            <thead class="table-dark">
              <tr>
                <th>護字號</th>
                <th>姓名</th>
                <th>收案日</th>
                <th>建立時間</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($open_cases[$key] as $area => $cases)
                <tr class="table-primary">
                  <td colspan="4" class="fw-bold text-left">{{ $area }}</td>
                </tr>
                @foreach ($cases as $case)
                  <tr>
                    <td>{{ $case->caseID }}</td>
                    <td>{{ $case->name }}</td>
                    <td>{{ $case->areaName }}</td>
                    <td>{{ $case->open_date }}</td>
                  </tr>
                @endforeach
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    @endif
    </div>
  @endforeach
</div>
@endsection
