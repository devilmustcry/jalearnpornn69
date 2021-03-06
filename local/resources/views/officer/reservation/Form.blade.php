<?php
  use App\func as func;

  $user_id = Auth::user()->id;
  $sections = func::GetSection();
  $dataEquipment = func::GET_EQUIPMENT();
?>
@extends('layouts.officer',['page'=>'reservation'])
@section('page_heading','จองห้องประชุม')
@section('content')
<div class="row">
  <div class="col-md-1"></div>
  <div class="col-md-10">
  <div class="panel panel-default">
    <div class="panel-heading">กรอกข้อมูลการจองห้องประชุม</div>
      <div class="panel-body">
        <form class="form-horizontal" action="{{ url('control/reservation/confirm') }}" method="post" enctype="multipart/form-data">
          <div class="form-group">
            <label class="col-sm-2 control-label">ห้องประชุม</label>
            <div class="col-sm-10">
              <p class="form-control-static">{{$room->meeting_name}}</p>
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label" >เวลาเริ่มใช้</label>
            <div class="col-sm-10">
              <p class="form-control-static">{{$time_reserve}}</p>
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label">จำนวนเวลา</label>
            <div class="col-sm-5">
              <select class="sectionlist form-control" name="time_use">
                @for($index = 1; $index <= $time_remain; $index++)
                  <option value="{{$index}}">{{$index}}</option>
                @endfor
              </select>
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label">หัวข้อการประชุม</label>
            <div class="col-sm-10">
              <input type="text" class="form-control" name="detail_topic" maxlength="100">
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label">จำนวนผู้เข้าประชุม</label>
            <div class="col-sm-10">
              <input type="text" class="form-control" name="detail_count" maxlength="3">
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label">ชื่อผู้ติดต่อ</label>
            <div class="col-sm-10">
              <input type="text" class="form-control" name="contract_name" maxlength="100">
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label">เบอร์โทรติดต่อ</label>
            <div class="col-sm-5">
              <input type="text" class="form-control" name="user_tel" maxlength="10" placeholder="0123456789">
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label">สาขา</label>
            <div class="col-sm-5">
              <select class="sectionlist form-control" name="section_id">
                @foreach($sections as $section)
                  <option value="{{$section->section_ID}}">{{$section->section_name}}</option>
                @endforeach
              </select>
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label">เอกสารการจอง</label>
            <div class="col-sm-5">
              <input type="file" class="form-control" name="contract_file[]" multiple>
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label">อุปกรณ์ที่ยืมเพิ่ม</label>
            <div class="col-sm-5">
              <select class="sectionlist form-control" id="input-equip-name">
                @foreach($dataEquipment as $equipment)
                  <option value="{{$equipment->em_name}}">{{$equipment->em_name}} : (เหลือจำนวน {{$equipment->em_count}})</option>
                @endforeach
              </select>
            </div>
            <label class="col-sm-1 control-label">จำนวน</label>
            <div class="col-sm-2">
                    <input type="number" class="form-control" min="1" id="input-equip-amount">
            </div>
            <div class="col-sm-1 control-label" >
                <button style="padding-top: 0px" type="button" class="btn btn-default btn-circle" onclick="addEquioment()">
                    <i style="margin-top:8px"class="fa fa-lg fa-plus" aria-hidden="true"></i>
                </button>
            </div>
          </div>
          <div class="form-group form-room" id="div-show-equip" style="display:none">
            <label class="col-sm-3 control-label"></label>
            <div class="col-sm-7">
                <ul style="-webkit-padding-start: 15px;" id="list-equip">
                </ul>
            </div>
          </div>
          <div id="hideEquip"></div>
    
          <input type="hidden" name="user_id" value="{{$user_id}}">
          <input type="hidden" name="meeting_id" value="{{$room->meeting_ID}}">
          <input type="hidden" name="time_reserve" value="{{$time_reserve}}">
          <input type="hidden" name="checkin" value="{{$checkin}}">
          <input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}">
          <div class="form-group">
            <div class="col-sm-2"></div>
            <div class="col-sm-10">
              <button type="submit" class="btn btn-success">ยืนยันการจอง</button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
  <div class="col-md-1"></div>
</div>
<script>
  var equip =[]
  var data_equip = <?php echo $dataEquipment ?>;
  
  function addEquioment() {
     var name = $('#input-equip-name').val()
     var amount = ($('#input-equip-amount').val()=='')? 0:$('#input-equip-amount').val()
     if (amount && amount > 0) {
       for (let index = 0; index < data_equip.length; index++) {
         if (data_equip[index].em_name == name && data_equip[index].em_count < amount) {
            swal('ไม่สำเร็จ', 'อุปกรณ์ '+data_equip[index].em_name+' ไม่เพียงพอ กรุณาเลือกจำนวนใหม่อีกครั้ง' , 'error')
            break
         } else if (data_equip[index].em_name == name && data_equip[index].em_count >= amount) {
            if (checkDuplicate(name,amount, equip)) {
              equip[equip.length] = [name,amount];
            }
         }
      }
    }
    fetchListEquip(equip);
    $('#input-equip-amount').val('')
 }

 function checkDuplicate(newVal, amount, arrVal) {
    for (var m = 0; m < arrVal.length; m++)
        if (newVal == arrVal[m][0]) return false;
    return true;
 }

 function fetchListEquip(equipment){  
     if(equipment.length == 0){
        $('#div-show-equip').hide()
     }else{
        var html = ''
        for(var i = 0 ; i < equipment.length ; i++){
            html +='<li>'+
                        '<b>'+equipment[i][0]+'</b> จำนวน : '+equipment[i][1]+
                        ' <i class="fa fa-times" aria-hidden="true" title="ลบ" onclick="deleteEquip('+i+')"></i>'+
                    '</li>'
        }
        pushHiddenEquip(equipment)
        $('#list-equip').html(html)
        $('#div-show-equip').show()
    }
 }

 function pushHiddenEquip(equipment){
    var html =''
    for(var i = 0 ; i < equipment.length ; i++){
        html +='<input type="hidden" name="hdnEq[]" value="'+equipment[i]+'">'
    }
    $('#hideEquip').html(html)
 }

 function deleteEquip(index){
    equip.splice(index, 1);
    fetchListEquip(equip)
 }
</script>
@endsection