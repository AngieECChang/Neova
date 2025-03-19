@extends('layouts.app')
@section('content')
<style>
  .custom-table tbody tr:hover {
    background-color: #d1ecf1 !important;  /* 淡藍色 */
  }
</style>
@php
  $patient_type = config('public.hc_patient_type');
  $gender = config('public.gender');
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
      <div style="padding-left:10px">
        <a href="{{ route('hc-create') }}" class="btn text-white" style="background-color: orange;">新增個案</a>
      </div>
    </form>
  </div>
</div>
<!-- Bootstrap Tabs for Case Types -->
<ul class="nav nav-tabs mt-3" id="caseTypeTabs" role="tablist">
  <!-- 🔹 Tabs 選項 -->
  <li class="nav-item">
    <a class="nav-link active fw-bold" id="tab-all" data-bs-toggle="tab" href="#content-all" role="tab">全部</a>
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
    <div class="row align-items-center mb-4">
      <div class="col-3">
        <h1 class="h3 text-gray-800 mb-0"></h1>
      </div>
      <div class="col-9 d-flex justify-content-end">
        <input type="text" id="tableSearch" class="form-control" placeholder="🔍 搜尋..." style="width: 150px;">
      </div>
    </div>
      <div class="card shadow-sm mb-4">
        <div class="card-body">
          <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
          <table class="table table-striped table-hover custom-table searchable-table">
            <thead class="sticky-top table-dark">
              <tr>
                <th width="100x" class="text-center">案號</th>
                <th width="120px" class="text-center">姓名</th>
                <th width="60px" class="text-center">性別</th>
                <th width="110px" class="text-center">類型</th>
                <th width="120px" class="text-center">收案日</th>
                <th class="text-center">功能</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($open_cases as $caseType => $areaGroups)
                @foreach ($areaGroups as $area => $cases)
                  <tr class="table-primary">
                    <td colspan="6" class="fw-bold text-left">{{ $area }}</td>
                  </tr>
                  @foreach ($cases as $case)
                    <tr>
                      <td class="text-center">{{ $case->caseNoDisplay }}</td>
                      <td class="text-center">{{ $case->name }}</td>
                      <td class="text-center">{!! $gender[$case->gender] ?? '' !!}</td>
                      <td class="text-center">{{ $patient_type[$caseType] ?? '未知類型' }}</td>
                      <td class="text-center">{{ $case->open_date }}</td>
                      <td>
                        <button class="btn btn-sm btn-warning edit-btn" data-id="{{ $case->caseID }}" data-caseno="{{ (string)$case->caseNoDisplay }}" data-type="{{ $case->case_type }}" data-casename="{{ $case->name }}" data-bs-toggle="modal" data-bs-target="#editcaseModal">
                          <i class="bi bi-pencil-square"></i>修改案號、類型
                        </button>
                      </td>
                    </tr>
                  @endforeach
                @endforeach
              @endforeach
            </tbody>
          </table>
        </div>
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
    <div class="row align-items-center mb-4">
      <div class="col-3">
        <h1 class="h3 text-gray-800 mb-0"></h1>
      </div>
      <div class="col-9 d-flex justify-content-end">
        <input type="text" id="tableSearch" class="form-control" placeholder="🔍 搜尋..." style="width: 150px;">
      </div>
    </div>
      <div class="card shadow-sm mb-4">
        <div class="card-body">
          <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
          <table class="table table-striped table-hover custom-table searchable-table">
            <thead class="sticky-top table-dark">
              <tr>
                <th width="100x" class="text-center">案號</th>
                <th width="120px" class="text-center">姓名</th>
                <th width="60px" class="text-center">性別</th>
                <th width="120px" class="text-center">收案日</th>
                <th class="text-center">功能</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($open_cases[$key] as $area => $cases)
                <tr class="table-primary">
                  <td colspan="5" class="fw-bold text-left">{{ $area }}</td>
                </tr>
                @foreach ($cases as $case)
                  <tr>
                    <td class="text-center">{{ $case->caseNoDisplay }}</td>
                    <td class="text-center">{{ $case->name }}</td>
                    <td class="text-center">{!! $gender[$case->gender] ?? '' !!}</td>
                    <td class="text-center">{{ $case->open_date }}</td>
                    <td>
                      <button class="btn btn-sm btn-warning edit-btn" data-id="{{ $case->caseID }}" data-caseno="{{ (string)$case->caseNoDisplay }}" data-type="{{ $case->case_type }}" data-casename="{{ $case->name }}" data-bs-toggle="modal" data-bs-target="#editcaseModal">
                        <i class="bi bi-pencil-square"></i>修改案號、類型
                      </button>
                    </td>
                  </tr>
                @endforeach
              @endforeach
            </tbody>
          </table>
        </div>
        </div>
      </div>
    @endif
    </div>
  @endforeach
</div>
<!-- 編輯 Modal -->
<div class="modal fade" id="editcaseModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">編輯個案資料 <span id="editCaseName"></span></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form id="editForm">
          {{--  在 POST 請求中自動附加 CSRF Token，並在伺服器端驗證，以防止攻擊者偽造請求 --}}
          @csrf
          @method('PUT')
          <input type="hidden" id="editCaseId">

          <div class="mb-3">
            <label class="form-label">案號</label>
            <input type="text" class="form-control" id="editCaseNo" required>
          </div>

          <div class="mb-3">
            <label class="form-label">個案類型</label>
            <select class="form-control" id="editCaseType">
              @foreach ($patient_type as $key => $value)
                <option value="{{ $key }}">{{ $value }}</option>
              @endforeach
            </select>
          </div>
          <button type="submit" class="btn btn-success w-100">儲存修改</button>
        </form>
      </div>
    </div>
  </div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
  document.getElementById('tableSearch').addEventListener('keyup', function() {
  let filter = this.value.toLowerCase();
  let tables = document.querySelectorAll(".searchable-table");

  tables.forEach(table => {
    let rows = table.querySelectorAll("tbody tr");
    rows.forEach(row => {
      let text = row.innerText.toLowerCase();
      row.style.display = text.includes(filter) ? "" : "none";
    });
  });
});

$(document).ready(function() {
  // 點擊「編輯」按鈕時，填入對應資料
  $(".edit-btn").click(function() {
    $("#editCaseId").val($(this).data("id"));
    $("#editCaseName").html($(this).data("casename"));
    $("#editCaseNo").val($(this).data("caseno"));
    $("#editCaseType").val($(this).data("type"));
  });

  // 提交表單並更新資料
  $("#editForm").submit(function(e) {
    e.preventDefault();

    let caseId = $("#editCaseId").val();
    let caseNo = $("#editCaseNo").val();
    let caseType = $("#editCaseType").val();
    let token = $("input[name=_token]").val();  //雖然有設置全域meta和app.js但沒有作用，還是要在ajax時增加送出token

    $.ajax({
      url: "/update-case/" + caseId,
      method: "PUT",
      data: {
        _token: token,
        caseNo: caseNo,
        caseType: caseType
      },
      success: function(response) {
        if (response.success) {
          alert("修改成功！");
          location.reload();
        } else {
          alert("修改失敗！");
        }
      },
      error: function() {
        alert("修改失敗！");
      }
    });
  });
});
</script>
@endsection
