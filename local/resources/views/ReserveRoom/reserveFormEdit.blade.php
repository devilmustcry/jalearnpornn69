<?php
  use App\func as func;

  $user_id = Auth::user()->id;
  $user_name = Auth::user()->user_name;
  $sections = func::GetSection();
  $dataEquipment = func::GET_EQUIPMENT();
?>

@extends('layouts.app')
@section('page_heading','จองห้องประชุม')
@section('content')
<div class="row">
  <div class="col-md-1"></div>
  <div class="col-md-10">
  <div class="panel panel-default">
    <div class="panel-heading">กรอกข้อมูลการจองห้องประชุม</div>
      <div class="panel-body">
        <form class="form-horizontal" action="{{ url('reserve/confirmedit') }}" method="post" enctype="multipart/form-data">
          <div class="form-group">
            <label class="col-sm-2 control-label">ห้องประชุม</label>
            <div class="col-sm-10">
              <p class="form-control-static">{{$room_name}}</p>
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label">เวลาเริ่มใช้</label>
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
            @if(isset($dataReserve->detail_topic)) <input type="text" class="form-control" name="detail_topic" value="{{$dataReserve->detail_topic}}" maxlength="100">
            @else <input type="text" class="form-control" name="detail_topic" maxlength="100">
            @endif
              <p  style="color:red">@if($errors->has('detail_topic')) {{$errors->first('detail_topic')}}@endif</p>
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label">จำนวนผู้เข้าประชุม</label>
            <div class="col-sm-10">
            @if(isset($dataReserve->detail_topic)) <input type="text" class="form-control" value="{{$dataReserve->detail_count}}" name="detail_count" maxlength="3">
            @else <input type="text" class="form-control" name="detail_count" maxlength="3">
            @endif
              <p  style="color:red">@if($errors->has('detail_count')) {{$errors->first('detail_count')}}@endif</p>
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label">ชื่อผู้ติดต่อ</label>
            <div class="col-sm-10">
              <p class="form-control-static">{{$user_name}}</p>
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label">เบอร์โทรติดต่อ</label>
            <div class="col-sm-10">
            @if(isset($dataReserve->detail_topic)) <input type="text" class="form-control" value="{{$dataReserve->booking_phone}}" name="user_tel" maxlength="10" placeholder="0123456789">
            @else <input type="text" class="form-control" name="user_tel" maxlength="10" placeholder="0123456789">
            @endif
              <p  style="color:red">@if($errors->has('user_tel')) {{$errors->first('user_tel')}}@endif</p>
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
          <input type="hidden" name="user_name" value="{{$user_name}}">
          <input type="hidden" name="meeting_id" value="{{$room_id}}">
          <input type="hidden" name="booking_id" value="{{$dataReserve->booking_ID}}">
          <input type="hidden" name="borrow_id" value="{{$dataBorrow[0]->borrow_ID}}">
          <input type="hidden" name="time_reserve" value="{{$time_reserve}}">
          <input type="hidden" name="time_select" value="{{$time_select}}">
          <input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}">
          <div class="form-group">
            <div class="col-sm-2"></div>
            <div class="col-sm-10">
              <button type="submit" class="btn btn-success">แก้ไขการจอง</button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
  <div class="col-md-1"></div>
</div>
<script>
  var equip = <?php echo $dataBorrow ?>;
  var data_equip = <?php echo $dataEquipment ?>;
  var newdata = []

  $(document).ready(function() {
    addEquioment()
    console.log(newdata)
  });
  
  function addEquioment() {
     var name = $('#input-equip-name').val()
     var amount = ($('#input-equip-amount').val()=='')? 0:$('#input-equip-amount').val()
     if (amount && amount > 0) {
       for (let index = 0; index < data_equip.length; index++) {
         if (data_equip[index].em_name == name && data_equip[index].em_count < amount) {
            swal('ไม่สำเร็จ', 'อุปกรณ์ '+data_equip[index].em_name+' ไม่เพียงพอ กรุณาเลือกจำนวนใหม่อีกครั้ง' , 'error')
            break
         } else if (data_equip[index].em_name == name && data_equip[index].em_count >= amount) {
            if (checkDuplicate(name,amount, newdata)) {
              newdata[newdata.length] = [name,amount];
            }
         }
      }
    } else {
      if (equip) {
        for (let index = 0; index < data_equip.length; index++) {
          for (let inner = 0; inner < equip.length; inner++) {
            if (data_equip[index].em_ID == equip[inner].equiment_ID && data_equip[index].em_count < equip[inner].borrow_count) {
              swal('ไม่สำเร็จ', 'อุปกรณ์ '+data_equip[index].em_name+' ไม่เพียงพอ กรุณาเลือกจำนวนใหม่อีกครั้ง' , 'error')
              break
            } else if (data_equip[index].em_ID == equip[inner].equiment_ID && data_equip[index].em_count >= equip[inner].borrow_count) {
              if (checkDuplicate(data_equip[index].em_name, equip[inner].borrow_count, newdata)) {
                newdata[newdata.length] = [data_equip[index].em_name, equip[inner].borrow_count];
              }
            }
          }
        }
      }
    }
    fetchListEquip(newdata);
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
    console.log(newdata)
 }

 function pushHiddenEquip(equipment){
    var html =''
    for(var i = 0 ; i < equipment.length ; i++){
        html +='<input type="hidden" name="hdnEq[]" value="'+equipment[i]+'">'
    }
    $('#hideEquip').html(html)
 }

 function deleteEquip(index){
  newdata.splice(index, 1);
    fetchListEquip(newdata)
  console.log(newdata)
 }
</script>
@endsection